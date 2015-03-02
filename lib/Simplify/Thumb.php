<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

namespace Simplify;

/**
 *
 * Image processing
 *
 */
class Thumb
{

  const TOP = 'T';

  const TOP_LEFT = 'TL';

  const TOP_RIGHT = 'TR';

  const BOTTOM = 'B';

  const BOTTOM_LEFT = 'BL';

  const BOTTOM_RIGHT = 'BR';

  const LEFT = 'L';

  const RIGHT = 'R';

  const CENTER = 'C';

  const NO_SCALE = 0;

  const FIT_INSIDE = -1;

  const FIT_OUTSIDE = 1;

  const SCALE_TO_FIT = 2;

  public $baseDir;

  public $filesPath;

  public $cachePath;

  protected $ignoreCache = false;

  protected $operations = array();

  protected $originalFile;

  protected $originalType;

  /**
   * Return a new instance of \Simplify\Thumb with optional params
   *
   * @param mixed $params
   * @return \Simplify\Thumb
   */
  public static function factory($params = null)
  {
    $thumb = new self();

    if ($params !== false) {
      $thumb->baseDir = sy_get_param($params, 'baseDir', \Simplify::config()->get('www_dir'));
      $thumb->filesPath = sy_get_param($params, 'filesPath', \Simplify::config()->get('files_path'));
      $thumb->cachePath = sy_get_param($params, 'cachePath', $thumb->filesPath . 'cache/');
    }

    return $thumb;
  }

  /**
   *
   * @param string $baseDir base (absolute) dir
   * @return \Simplify\Thumb
   */
  public function setBaseDir($baseDir)
  {
    $this->baseDir = $baseDir;
    return $this;
  }

  /**
   *
   * @param string $path relative path to files
   * @return \Simplify\Thumb
   */
  public function setFilesPath($path)
  {
    $this->filesPath = $path;
    return $this;
  }

  /**
   *
   * @param string $path relative path to cache
   * @return \Simplify\Thumb
   */
  public function setCachePath($path)
  {
    $this->cachePath = $path;
    return $this;
  }

  /**
   * Constructor
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * Ignore cached files
   *
   * @param boolean $ignoreCache
   * @return \Simplify\Thumb
   */
  public function ignoreCache($ignoreCache = true)
  {
    $this->ignoreCache = $ignoreCache;
    return $this;
  }

  /**
   * Load image file
   *
   * @param string $file
   * @return \Simplify\Thumb
   */
  public function load($file)
  {
    $this->originalFile = $file;
    $file = $this->makeAbsolute($file);
    $info = @getimagesize($file);
    $this->originalType = $info[2];
    return $this;
  }

  /**
   * Resize image
   *
   * @param int $width reference output width
   * @param int $height reference output height
   * @param int $mode resizing mode
   * @param int $far force aspect ratio
   * @param int $r background red channel (0 - 255)
   * @param int $g background green channel (0 - 255)
   * @param int $b background blue channel (0 - 255)
   * @param int $a background alpha channel (0 = opaque, 127 = transparent)
   * @return \Simplify\Thumb
   */
  public function resize($width = null, $height = null, $mode = \Simplify\Thumb::FIT_INSIDE, $far = false, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\Resize');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Crop image
   *
   * @param int $x crop top position
   * @param int $y crop left position
   * @param int $width crop width
   * @param int $height crop height
   * @param int $r background red channel (0 - 255)
   * @param int $g background green channel (0 - 255)
   * @param int $b background blue channel (0 - 255)
   * @param int $a background alpha channel (0 = opaque, 127 = transparent)
   * @return \Simplify\Thumb
   */
  public function crop($x, $y, $width, $height, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\Crop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Offset image size
   *
   * @param int $top offset top
   * @param int $right offset left
   * @param int $bottom offset bottom
   * @param int $left offset left
   * @param int $r background red channel (0 - 255)
   * @param int $g background green channel (0 - 255)
   * @param int $b background blue channel (0 - 255)
   * @param int $a background alpha channel (0 = opaque, 127 = transparent)
   * @return \Simplify\Thumb
   */
  public function offset($top, $right, $bottom, $left, $r = 0, $g = 0, $b = 0, $a = 0)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\Offset');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Zoom and crop image
   *
   * @param int $width final width
   * @param int $height final height
   * @param string $gravity position position
   * @return \Simplify\Thumb
   */
  public function zoomCrop($width = null, $height = null, $gravity = \Simplify\Thumb::CENTER)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\ZoomCrop');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Set jpg quality
   *
   * @param int $quality jpg ouput quality (1 - 100)
   * @return \Simplify\Thumb
   */
  public function quality($quality)
  {
    $this->operations[] = array('quality', func_get_args());
    return $this;
  }

  /**
   * Change image brightness level
   *
   * @param int $level -255 = min brightness, 0 = no change, +255 = max brightness
   * @return \Simplify\Thumb
   */
  public function brightness($level)
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_BRIGHTNESS);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Converts the image into grayscale
   *
   * @return \Simplify\Thumb
   */
  public function grayscale()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_GRAYSCALE);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Reverses all colors of the image
   *
   * @return \Simplify\Thumb
   */
  public function negate()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_NEGATE);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Change image contrast level
   *
   * @param int $level -100 = max contrast, 0 = no change, +100 = min contrast
   * @return \Simplify\Thumb
   */
  public function contrast($level)
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_CONTRAST);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Like IMG_FILTER_GRAYSCALE, except you can specify the color. Use arg1, arg2 and arg3 in the form of red, green,
   * blue and arg4 for the alpha channel. The range for each color is 0 to 255
   *
   * @param int $red value of red component
   * @param int $green value of green component
   * @param int $blue value of blue component
   * @param int $alpha alpha channel, A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent
   * @return \Simplify\Thumb
   */
  public function colorize($red, $green, $blue, $alpha)
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_COLORIZE);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Uses edge detection to highlight the edges in the image.
   *
   * @return \Simplify\Thumb
   */
  public function edgedetect()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_EDGEDETECT);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Embosses the image.
   *
   * @return \Simplify\Thumb
   */
  public function emboss()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_EMBOSS);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Blurs the image using the Gaussian method.
   *
   * @return \Simplify\Thumb
   */
  public function gaussianBlur()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_GAUSSIAN_BLUR);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Blurs the image.
   *
   * @return \Simplify\Thumb
   */
  public function selectiveBlur()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_SELECTIVE_BLUR);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Uses mean removal to achieve a "sketchy" effect.
   *
   * @return \Simplify\Thumb
   */
  public function meanRemoval()
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_MEAN_REMOVAL);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Makes the image smoother. Use arg1 to set the level of smoothness.
   *
   * @param int $level Smoothness level.
   * @return \Simplify\Thumb
   */
  public function smooth($level)
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_SMOOTH);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Applies pixelation effect to the image, use arg1 to set the block size and arg2 to set the pixelation effect mode.
   *
   * @param int $blockSize Block size in pixels.
   * @param boolean $advanced Whether to use advanced pixelation effect or not (defaults to FALSE).
   * @return \Simplify\Thumb
   */
  public function pixelate($blockSize, $advanced = false)
  {
    $params = func_get_args();
    array_unshift($params, IMG_FILTER_PIXELATE);
    array_unshift($params, '\Simplify\Thumb\Plugin\ImageFilter');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Overlay an image
   *
   * @param string $overlayImage the image to overlay
   * @param int $dst_x
   * @param int $dst_y
   * @param int $src_x
   * @param int $src_y
   * @param int $dst_w
   * @param int $dst_h
   * @param int $src_w
   * @param int $src_h
   */
  public function overlay($overlayImage = null, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $dst_w = null, $dst_h = null, $src_w = null, $src_h = null)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\Overlay');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Merge an image
   *
   * @param string $overlayImage the image to overlay
   * @param int $dst_x
   * @param int $dst_y
   * @param int $src_x
   * @param int $src_y
   * @param int $dst_w
   * @param int $dst_h
   * @param int $src_w
   * @param int $src_h
   */
  public function merge($overlayImage = null, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = null, $src_h = null, $pct = 0)
  {
    $params = func_get_args();
    array_unshift($params, '\Simplify\Thumb\Plugin\Merge');
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Call plugin
   *
   * @param string $plugin plugin class
   * @return \Simplify\Thumb
   */
  public function plugin($plugin)
  {
    $params = func_get_args();
    $this->operations[] = array('callPlugin', $params);
    return $this;
  }

  /**
   * Save the image
   *
   * @param string $file output filename
   * @return string
   */
  public function save($file = null)
  {
    if (empty($file)) {
      $file = $this->originalFile;
    }

    if (!sy_path_is_absolute($file)) {
      $file = $this->baseDir . $this->filesPath . $file;
    }

    $cacheFilename = $this->getCacheFilename();

    if (file_exists($cacheFilename) && !$this->ignoreCache) {
      copy($cacheFilename, $file);
    }
    else {
      $this->process()->save($file);
    }

    return $file;
  }

  /**
   * Output the image to the browser
   *
   * @param string $type image type
   * @param int $cacheSeconds cache time in seconds
   */
  public function output($type = null, $cacheSeconds = 604800)
  {
    $cacheFilename = $this->getCacheFilename();

    if (file_exists($cacheFilename) && !$this->ignoreCache) {
      $this->outputFromCache($type, $cacheSeconds);
    }
    else {
      $this->process()->output($type, $cacheSeconds);
    }
  }

  /**
   * Cache and output the image to browser
   *
   * @param string $type image type
   * @param int $cacheSeconds cache time in seconds
   */
  public function outputFromCache($type = null, $cacheSeconds = 604800)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }

    if ($cacheSeconds) {
      header("Cache-Control: private, max-age={$cacheSeconds}, pre-check={$cacheSeconds}");
      header("Expires: " . date(DATE_RFC822, strtotime("{$cacheSeconds} seconds")));
      header("Pragma: private");
    }
    else {
      header("Pragma: no-cache");
    }

    header('Content-Type: ' . $this->getImageMimeType($type));

    readfile($this->baseDir . $this->cache($type)->getCacheFilename());

    exit();
  }

  /**
   * Process and cache the image
   *
   * @param string $type image type
   * @return \Simplify\Thumb
   */
  public function cache($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    } else {
      $this->originalType = $type;
    }

    $cacheFilename = $this->getCacheFilename($type);

    $filename = $this->baseDir . $cacheFilename;

    if (!file_exists($filename) || $this->ignoreCache) {
      try {
        $this->process()->save($filename, $type);
      } catch (ThumbException $e) {
        //
      }
    }

    return $this;
  }

  /**
   * Get image cache filename
   *
   * @return string
   */
  public function getCacheFilename($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }

    $filename = $this->cachePath . $this->getCachePrefix() . md5(serialize($this->operations)) .
       image_type_to_extension($type);

    return $filename;
  }

  /**
   * Delete image cache
   *
   * @return \Simplify\Thumb
   */
  public function cleanCached()
  {
    foreach (glob($this->baseDir . $this->cachePath . $this->getCachePrefix() . '*.*') as $file) {
      @unlink($file);
    }

    return $this;
  }

  /**
   * Get mime type for image type
   *
   * @param string $type image type
   * @return string
   */
  protected function getImageMimeType($type = null)
  {
    if (empty($type)) {
      $type = $this->originalType;
    }
    return image_type_to_mime_type($type);
  }

  /**
   * Get image cache prefix
   *
   * @return string
   */
  protected function getCachePrefix()
  {
    return 'thumbcache_' . md5($this->originalFile) . '_';
  }

  /**
   * Process the image
   *
   * @return \Simplify\Thumb\Processor
   */
  protected function process()
  {
    $file = $this->originalFile;

    $file = $this->makeAbsolute($file);

    $f = new \Simplify\Thumb\Processor();

    $f->load($file);

    foreach ($this->operations as $op) {
      call_user_func_array(array($f, $op[0]), $op[1]);
    }

    return $f;
  }

  protected function makeAbsolute($file)
  {
    if (!\sy_path_is_absolute($file)) {
      $file = $this->baseDir . $file;
    }

    return $file;
  }

}
