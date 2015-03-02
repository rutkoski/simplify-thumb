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

namespace Simplify\Thumb\Plugin;

/**
 *
 * Wrapper for PHP imagefilter function
 *
 */
class ImageFilter extends \Simplify\Thumb\Plugin
{

  /**
   * (non-PHPdoc)
   * @see \Simplify\Thumb\Plugin::process()
   */
  protected function process(\Simplify\Thumb\Processor $thumb, $filter = null)
  {
    \Simplify\Thumb\Functions::validateImageResource($thumb->image);
    $args = func_get_args();
    $args[0] = $thumb->image;
    call_user_func_array('imagefilter', $args);
  }

}
