<?php
/*
 $Id: reg_backup_mysql.php, v 1.4 2011/11/24  $																    
                                                     
  By PRO-Webs.net 12.9.2011
                                                      
  Powered by Zen-Cart (www.zen-cart.com)              
  Portions Copyright (c) 2006 The Zen Cart Team       
                                                      
  Released under the GNU General Public License       
  available at www.zen-cart.com/license/2_0.txt       
  or see "license.txt" in the downloaded zip          

  DESCRIPTION: Add Cross Sell + Advanced to Catalog Menu
*/

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (function_exists('zen_register_admin_page')) {
    if (!zen_page_key_exists('cross_sell_products')) {
        zen_register_admin_page('cross_sell_products', 'BOX_CATALOG_XSELL_PRODUCTS','FILENAME_XSELL_PRODUCTS', '', 'catalog', 'Y', 17);
    }
    if (!zen_page_key_exists('advanced_cross_sell')) {
        zen_register_admin_page('advanced_cross_sell', 'BOX_CATALOG_ADVANCED_XSELL_PRODUCTS','FILENAME_XSELL_ADVANCED_PRODUCTS', '', 'catalog', 'Y', 17);
    }
}