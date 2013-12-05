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
 * Text operations and utilities
 *
 */
class Simplify_Thumb_Text
{

  public static function breakTextToLength($text, $font, $size, $length, $chars = ' .!,:?')
  {
    $chars = preg_quote($chars);

    if (preg_match_all('/(?:(?:.*?)(?:[' . $chars . '])+|.+)/', $text, $matches)) {
      $words = $matches[0];
    }
    else {
      $words = (array) $text;
    }

    $new = '';

    $line = '';
    while (count($words)) {
      $word = array_shift($words);

      $l = strlen($line . $word);

      if ($l > $length) {
        $new .= "\n";

        $line = $word;
      }
      else {
        $line .= $word;
      }

      $new .= $word;
    }

    return $new;
  }

  public static function breakTextToWidth($text, $font, $size, $width, $chars = ' .!,:?')
  {
    $chars = preg_quote($chars);

    if (preg_match_all('/(?:(?:.*?)(?:[' . $chars . '])+|.+)/', $text, $matches)) {
      $words = $matches[0];
    }
    else {
      $words = (array) $text;
    }

    $new = '';

    $line = '';
    while (count($words)) {
      $word = array_shift($words);

      $b = imagettfbbox($size, 0, $font, $line . $word);
      $w = $b[0] + $b[2];

      if ($w > $width) {
        $new .= "\n";

        $line = $word;
      }
      else {
        $line .= $word;
      }

      $new .= $word;
    }

    return $new;
  }

}
