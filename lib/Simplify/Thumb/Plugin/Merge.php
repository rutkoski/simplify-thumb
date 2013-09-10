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

/**
 *
 * Resize plugin
 *
 */
class Simplify_Thumb_Plugin_Merge extends Simplify_Thumb_Plugin
{

  /**
   * (non-PHPdoc)
   * @see Simplify_Thumb_Plugin::process()
   */
  protected function process(Simplify_Thumb_Processor $thumb, $overlayImage = null, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = null, $src_h = null, $pct = 0)
  {
    $overlay = Simplify_Thumb_Functions::load($overlayImage);

    $src_w = is_null($src_w) ? imagesx($overlay) : $src_w;
    $src_h = is_null($src_h) ? imagesy($overlay) : $src_h;

    imagecopymerge($thumb->image, $overlay, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
  }

}