<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * init_faq.php updated 2012-09-28 kamelion0927
 */
  if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
  }
 
if (isset($_GET['faqs_id'])) $_GET['faqs_id'] = preg_replace('[^0-9a-f:]', '', $_GET['faqs_id']);
if (isset($_GET['fcPath'])) $_GET['fcPath'] = preg_replace('[^0-9_]', '', $_GET['fcPath']);

while (list($key, $value) = each($_GET)) {
$_GET[$key] = preg_replace('[<>]', '', $value);
}

/**
 * validate faqs_id for search engines and bookmarks, etc.
 */
  if (isset($_GET['faqs_id']) and isset($_SESSION['check_valid']) &&  $_SESSION['check_valid'] != 'false') {
    $check_valid = zen_faqs_id_valid($_GET['faqs_id']);
    if (!$check_valid) {
      $_GET['main_page'] = zen_get_info_faq_page($_GET['faqs_id']);
      /**
       * do not recheck redirect
       */
      $_SESSION['check_valid'] = 'false';
      zen_redirect(zen_href_link($_GET['main_page'], 'faqs_id=' . $_GET['faqs_id']));
    }
  } else {
    $_SESSION['check_valid'] = 'true';
  }
  
// calculate faq_category path
  if (isset($_GET['fcPath'])) {
    $fcPath = $_GET['fcPath'];
  } 
  if (isset($_GET['faqs_id']) && !zen_check_url_get_terms()) {
    $fcPath = zen_get_faq_path($_GET['faqs_id']);
  } 
  
  if (zen_not_null($fcPath)) {
    $fcPath_array = zen_parse_faq_category_path($fcPath);
    $fcPath = implode('_', $fcPath_array);
    $current_faq_category_id = $fcPath_array[(sizeof($fcPath_array)-1)];
  } else {
    $current_faq_category_id = 0;
    $fcPath_array = array();
  }
  
  require(DIR_WS_FUNCTIONS . 'featured_faqs.php');
  zen_start_featured_faqs();
  zen_expire_featured_faqs();
  ?>