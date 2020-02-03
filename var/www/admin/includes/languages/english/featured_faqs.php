<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on $Id: featured.php 18695 2011-05-04 05:24:19Z drbyte $
 * featured_faqs.php created kamelion0927
 */

define('HEADING_TITLE', 'Featured FAQs');

define('TABLE_HEADING_FAQS', 'FAQs');
define('TABLE_HEADING_AVAILABLE_DATE', 'Available');
define('TABLE_HEADING_EXPIRES_DATE','Expires');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_FEATURED_FAQ', 'FAQ:');
define('TEXT_FEATURED_EXPIRES_DATE', 'Expiry Date:');
define('TEXT_FEATURED_AVAILABLE_DATE', 'Available Date:');

define('TEXT_INFO_DATE_ADDED', 'Date Added:');
define('TEXT_INFO_LAST_MODIFIED', 'Last Modified:');
define('TEXT_INFO_AVAILABLE_DATE', 'Available On:');
define('TEXT_INFO_EXPIRES_DATE', 'Expires At:');
define('TEXT_INFO_STATUS_CHANGE', 'Status Change:');

define('TEXT_INFO_HEADING_DELETE_FEATURED', 'Delete Featured');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete the featured faq?');

define('SUCCESS_FEATURED_PRE_ADD', 'Successful: Pre-Add of Featured ... please update the dates ...');
define('WARNING_FEATURED_PRE_ADD_EMPTY', 'Warning: No FAQ ID specified ... nothing was added ...');
define('WARNING_FEATURED_PRE_ADD_DUPLICATE', 'Warning: FAQ ID already Featured ... nothing was added ...');
define('WARNING_FEATURED_PRE_ADD_BAD_FAQS_ID', 'Warning: FAQ ID is invalid ... nothing was added ...');
define('TEXT_INFO_HEADING_PRE_ADD_FEATURED', 'Manually add new Featured by FAQ ID');
define('TEXT_INFO_PRE_ADD_INTRO', 'On large databases, you may Manually Add a Featured by the FAQ ID<br /><br />This is best used when the page takes too long to render and trying to select a FAQ from the dropdown becomes difficult due to too many FAQs from which to choose.');
define('TEXT_PRE_ADD_FAQS_ID', 'Please enter the FAQ ID to be Pre-Added: ');
define('TEXT_INFO_MANUAL', 'FAQ ID to be Manually Added as a Featured');
?>