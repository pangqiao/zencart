<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faqs_all.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 
define('NAVBAR_TITLE', 'Frequently Asked Questions');
define('HEADING_TITLE', 'Frequently Asked Questions');
define('TEXT_NO_FAQS', 'There are no FAQs available at this time - please check back later.');
define('TEXT_INFO_SORT_BY_FAQS_NAME', 'FAQ Name');
define('TEXT_INFO_SORT_BY_FAQS_NAME_DESC', 'FAQ Name - Desc');
define('TEXT_INFO_SORT_BY_FAQS_CATEGORY', 'Category');
define('TEXT_INFO_SORT_BY_FAQS_DATE_DESC', 'Date Added - New to Old');
define('TEXT_INFO_SORT_BY_FAQS_DATE', 'Date Added - Old to New');
define('TEXT_INFO_SORT_BY_FAQS_SORT_ORDER', 'Default Display');
define('TEXT_DISPLAY_NUMBER_OF_FAQS_ALL', 'Displaying <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> faqs)');
define('SQL_SHOW_FAQ_INDEX_LISTING',"select configuration_key, configuration_value from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'SHOW_FEATURED_FAQS_INDEX' and configuration_value > 0 order by configuration_value");
define('TABLE_HEADING_FEATURED_FAQS','Most Frequently Asked Questions');
?>