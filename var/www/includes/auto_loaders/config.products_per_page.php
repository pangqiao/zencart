<?php
/**
 * config.products_per_page.php
 *
 * @package initSystem
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * Added by rbarbour (ZCAdditions.com), Set number of products displayed per page (24)
 */
if (!defined('IS_ADMIN_FLAG')) {
 die('Illegal Access');
}



  $autoLoadConfig[160][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_products_per_page.php');