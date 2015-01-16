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
 * Crop plugin
 *
 */
class Callback extends \Simplify\Thumb\Plugin
{

  /**
   * (non-PHPdoc)
   * @see \Simplify\Thumb\Plugin::process()
   */
  protected function process(\Simplify\Thumb\Processor $thumb, $callback = null)
  {
    $args = func_get_args();
    unset($args[1]);
    call_user_func_array($callback, $args);
  }

}
