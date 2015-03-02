# Simplify PHP - Thumb

Thumbnail generation and image processing, extendable and with a fluent interface.

## Usage:

Basic example:

	require_once('vendor/simplify/thumb/lib/autoload.php');
	
	$thumb = new Simplify_Thumb();
	
	$filename = $thumb
	  ->setBaseDir(dirname(__file__))
	  ->setFilesPath('files')
	  ->setCachePath('files/cache')
	  ->load('dummy.jpg')
	  ->resize(150, 150)
	  ->cache()
	  ->getCacheFilename();

The library uses the method's parameters (like resize/150/150) to check for an existing cache file and uses it instead of recreating the image. 

### Output

Get the cached filename

	$thumb->getCacheFilename();

or save it to a new location

	$thumb->save('path/newfile.php');

or overwrite current image

	$thumb->save();

or send the image to the browser

	$thumb->output();

### Change formats

Just use the PHP constants (IMAGETYPE\_PNG, IMAGETYPE\_JPEG, ...):

	$thumb->cache(IMAGETYPE_PNG);

or

	$thumb->output(IMAGETYPE_PNG);

## Available methods

### Resize

	$thumb->resize($width, $height, $mode, $far, $r, $g, $b, $a);

The third parameter in resize changes how images are resized. It defaults to `Simplify_Thumb::FIT\_INSIDE`. Options for `$mode` are:

* `Simplify_Thumb::FIT_INSIDE` - resize the image to fit inside a box defined by `$width` and `$height`
* `Simplify_Thumb::FIT_OUTSIDE` - resize the image to fit outside a box defined by `$width` and `$height`
* `Simplify_Thumb::SCALE_TO_FIT` - resize the image to exactly `$width` and `$height`
* `Simplify_Thumb::NO_SCALE` - don't resize the image, that's usefull for enlarging the image canvas

The fourth parameters, `$far` (force aspect ratio), when true, forces the final image to be exactly `$width` by `$height` pixels. The `$r`, `$g`, `$b` and `$a` parameters set the color for the background, so, for example:

	$thumb->resize(150, 150, Simplify_Thumb::FIT_INSIDE, true, 0, 0, 0, 0);

on a 300 x 100px image, would fit the image inside a 150 x 150px box with a black background.

### ZoomCrop

Crops the image so that it fills the dimensions you specify. The third parameter specifies wich part of the image will be used.

	$thumb->zoomCrop($width, $height, Simplify_Thumb::CENTER);

### PHP image filters

	$thumb->brightness($level);
	$thumb->grayscale();
	$thumb->negate();
	$thumb->contrast($level);
	$thumb->colorize($red, $green, $blue, $alpha);
	$thumb->edgedetect();
	$thumb->emboss();
	$thumb->gaussianBlur();
	$thumb->selectiveBlur();
	$thumb->meanRemoval();
	$thumb->smooth($level);
	$thumb->pixelate($blockSize, $advanced);

### Offset

Shrink/enlarge the image canvas and fill background with color. 

	$thumb->offset($top, $right, $bottom, $left, $r = 0, $g = 0, $b = 0, $a = 0);

`$top`, `$right`, `$bottom` and `left` are relative. Positive numbers make the canvas bigger, negative numbers make it smaller (crops the image).

## Custom plugins

Implement custom plugins by extending `Simplify_Thumb_Plugin` and calling:

	$thumb->plugin($plugin);

where `$plugin` is the string representing the plugin class.

Example:

Plugin that overlays an image on top of the current one:

	class Simplify_Thumb_Plugin_Overlay extends Simplify_Thumb_Plugin
	{
	
	  protected function process(Simplify_Thumb_Processor $thumb, $overlayImage = null)
	  {
	    $overlay = Simplify_Thumb_Functionload($overlayImage);
	
	    $w = imagesx($overlay);
	    $h = imagesy($overlay);
	
	    imagecopyresampled($thumb->image, $overlay, 0, 0, 0, 0, $w, $h, $w, $h);
	  }
	
	}

then:

	$thumb->plugin('Simplify_Thumb_Plugin_Overlay', 'overlay.png');
