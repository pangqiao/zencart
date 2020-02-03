<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_categories.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

define('HEADING_TITLE', 'FAQ Categories / FAQs');
define('HEADING_TITLE_GOTO', 'Go To:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_FAQ_CATEGORIES_FAQS', 'FAQ Categories / FAQs');
define('TABLE_HEADING_FAQ_CATEGORIES_SORT_ORDER', 'Sort');
define('TEXT_FAQ_AVAILABLE', 'Active');
define('TEXT_FAQ_NOT_AVAILABLE', 'Inactive');
define('TEXT_LEGEND_FAQ_LINKED','Linked FAQs');
define('TEXT_FAQS_SORT_ORDER', 'Sort Order:');
define('IMAGE_ICON_FAQ_LINKED', 'FAQ is Linked');
define('IMAGE_ICON_FAQ_CAT_LINKED', 'A FAQ in this Category is Linked');
define('TABLE_HEADING_QUANTITY','FAQ Count');

define('TABLE_HEADING_ACTION', 'Action');
define('TABLE_HEADING_STATUS', 'Status');

define('TEXT_FAQ_CATEGORIES', 'FAQ Categories:');
define('TEXT_SUBFAQ_CATEGORIES', 'FAQ Subcategories:');
define('TEXT_FAQS', 'FAQs:');

define('TEXT_DATE_ADDED', 'Date Added:');
define('TEXT_FAQS_STATUS_INFO_OFF', '<span class="alert">FAQ is Disabled</span>');
define('TEXT_LAST_MODIFIED', 'Last Modified:');
define('CATEGORY_HAS_SUBFAQCATEGORIES ', '');
define('TEXT_NO_CHILD_FAQ_CATEGORIES_OR_FAQS', 'Please insert a new FAQ category or faq in this level.');
define('TEXT_FAQ_MORE_INFORMATION', 'For more information, please visit this faqs <a href="http://%s" target="blank">webpage</a>.');
define('TEXT_FAQ_DATE_ADDED', 'This faq was added to our site on %s.');


define('TEXT_EDIT_INTRO', 'Please make any necessary changes');
define('TEXT_EDIT_FAQ_CATEGORIES_ID', 'FAQ Category ID:');
define('TEXT_EDIT_FAQ_CATEGORIES_NAME', 'FAQ Category Name:');
define('SUCCESS_FAQ_CATEGORY_MOVED', 'Category has been successfully moved.');
define('TEXT_EDIT_SORT_ORDER', 'Sort Order:');

define('TEXT_INFO_COPY_TO_INTRO', 'Please choose the FAQ category you wish to copy this faq to.');
define('TEXT_INFO_CURRENT_FAQ_CATEGORIES', 'Current FAQ Categories: ');

define('TEXT_INFO_HEADING_NEW_FAQ_CATEGORY', 'New FAQ Category');
define('TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY', 'Edit FAQ Category');
define('TEXT_INFO_HEADING_DELETE_FAQ_CATEGORY', 'Delete FAQ Category');
define('TEXT_INFO_HEADING_MOVE_FAQ_CATEGORY', 'Move FAQ Category');

define('ERROR_CANNOT_MOVE_FAQ_CATEGORY_TO_CATEGORY_SELF', 'Fail - Categories cannot be moved to themselves.');
define('IMAGE_NEW_FAQ_CATEGORY', 'New FAQ Category');
define('IMAGE_NEW_FAQ', 'New FAQ');

define('TEXT_INFO_HEADING_DELETE_FAQ', 'Delete FAQ');
define('TEXT_INFO_HEADING_MOVE_FAQ', 'Move FAQ');
define('TEXT_INFO_HEADING_COPY_TO', 'Copy To');

define('TEXT_DELETE_FAQ_CATEGORY_INTRO', 'Are you sure you want to delete this FAQ category?');
define('TEXT_DELETE_FAQ_INTRO', 'Are you sure you want to permanently delete this faq?<br /><br /><strong>For Linked FAQs</strong><br />Check the checkbox for the FAQ category to delete the FAQ from.');

define('TEXT_DELETE_WARNING_CHILDS', '<b>WARNING:</b> There are %s (child-)FAQ categories still linked to this FAQ category!');
define('TEXT_DELETE_WARNING_FAQS', '<b>WARNING:</b> There are %s faqs still linked to this FAQ category!');

define('TEXT_MOVE_FAQS_INTRO', 'Please select which FAQ category you wish to move <b>%s</b> to.');
define('TEXT_MOVE_FAQ_CATEGORIES_INTRO', 'Please select which FAQ category you wish to move <b>%s</b> to.');
define('TEXT_MOVE', 'Move <b>%s</b> to:');

define('TEXT_NEW_FAQ_CATEGORY_INTRO', 'Please fill out the following information for the new FAQ category');
define('TEXT_FAQ_CATEGORIES_NAME', 'FAQ Category Name:');
define('TEXT_FAQ_CATEGORIES_IMAGE', 'FAQ Category Image:');
define('TEXT_SORT_ORDER', 'Sort Order:');

define('TEXT_FAQS_STATUS', 'FAQs Status:');
define('TEXT_FAQS_NAME', 'FAQs Name:');
define('TEXT_FAQS_DESCRIPTION', 'FAQs Description:');
define('TEXT_FAQS_ANSWER', 'FAQs Answer:');
define('EMPTY_FAQ_CATEGORY', 'Empty FAQ Category');

define('TEXT_HOW_TO_COPY', 'Copy Method:');
define('TEXT_COPY_AS_LINK', 'Link faq');
define('TEXT_COPY_AS_DUPLICATE', 'Duplicate faq');
define('TEXT_NEW_FAQ', 'New Faq');
define('TEXT_INFO_CURRENT_FAQ', 'Current FAQ: ');

// FAQ categories status
define('TEXT_INFO_HEADING_STATUS_FAQ_CATEGORY', 'Change FAQ Category Status for:');
define('TEXT_FAQ_CATEGORIES_STATUS_INTRO', 'Change the FAQ Category Status to: ');
define('TEXT_FAQ_CATEGORIES_STATUS_OFF', 'OFF');
define('TEXT_FAQ_CATEGORIES_STATUS_ON', 'ON');
define('TEXT_FAQS_STATUS_INFO', 'Change ALL FAQ Status to: ');
define('TEXT_FAQS_STATUS_OFF', 'OFF');
define('TEXT_FAQS_STATUS_ON', 'ON');
define('TEXT_FAQS_STATUS_NOCHANGE', 'Unchanged');
define('TEXT_FAQ_CATEGORIES_STATUS_WARNING', '<strong>WARNING ...</strong><br />Note: Disabling a FAQ category will disable all faqs in this FAQ category. Linked faqs located in this FAQ category that are shared with other FAQ categories will also be disabled.');
define('TEXT_DISPLAY_NUMBER_OF_FAQS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> FAQs)');
define('TEXT_FAQS_STATUS_ON_OF',' of ');
define('TEXT_FAQS_STATUS_ACTIVE',' active ');

define('TEXT_FAQ_CATEGORIES_DESCRIPTION', 'FAQ Categories Description:');
define('TABLE_HEADING_FAQ_MANAGER_CONFIGURATION', 'FAQ Manager Configuration');
define('TABLE_HEADING_FAQ_MANAGER_SUPPORT', 'FAQ Manager Support');
?>
