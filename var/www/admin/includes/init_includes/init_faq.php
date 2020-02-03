<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * admin/init_faq.php created 2012-09-18 for v1.5 kamelion0927
 */
  if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
  }
 // auto expire featured faqs
  require(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'featured_faqs.php');
  zen_start_featured_faqs();
  zen_expire_featured_faqs();
  ?>