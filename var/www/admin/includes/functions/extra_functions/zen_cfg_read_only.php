<?php
/**
 * zen_cfg_read_only.php
 *
 * @package
 * @copyright Copyright 2004-2013 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2013 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: zen_cfg_read_only.php, v 1.0 13.02.2010 14:38:17 AndrewBerezin $
 */

if (!function_exists('zen_cfg_read_only')) {
  function zen_cfg_read_only($text, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    return $text . zen_draw_hidden_field($name, $text);
  }
}

// EOF