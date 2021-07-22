<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_manager_file_names.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

  define('FILENAME_FAQS', 'faqs_all');
  define('FILENAME_FAQ_LISTING_DISPLAY_ORDER', 'faq_listing_display_order');  
  define('FILENAME_FEATURED_FAQS', 'featured_faqs');  
 
  define('TABLE_FAQ_CATEGORIES', DB_PREFIX . 'faq_categories');
  define('TABLE_FAQ_CATEGORIES_DESCRIPTION', DB_PREFIX . 'faq_categories_description');
  define('TABLE_FAQS', DB_PREFIX . 'faqs');
  define('TABLE_FAQS_DESCRIPTION', DB_PREFIX . 'faqs_description');
  define('TABLE_FAQS_TO_FAQ_CATEGORIES', DB_PREFIX . 'faqs_to_faq_categories');
  define('TABLE_FEATURED_FAQS', DB_PREFIX . 'faqs_featured');
?>