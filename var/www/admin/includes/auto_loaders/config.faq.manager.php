<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @admin/config.faq.manager.php created 2012-10-12 for v1.5 kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
 die('Illegal Access');
}
  $autoLoadConfig[190][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_faq.php');
?>