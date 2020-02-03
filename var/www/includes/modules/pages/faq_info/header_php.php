<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: header_php.php 18697 2011-05-04 14:35:20Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_info_header.php updated 2012-10-12 to be v1.5 compatible kamelion0927
 */
if (!$_GET['$fcPath']) {
$fcPath = zen_get_faqs_faq_category_id($_GET['$faqs_id']);
}
  require(DIR_WS_MODULES . 'require_languages.php');
  $sql = "select faqs_name from " . TABLE_FAQS_DESCRIPTION . " where faqs_id = '" . (int)$_GET['faqs_id'] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'";
  $faq_metatags = $db->Execute($sql);
    define('META_TAG_TITLE', $faq_metatags->fields['faqs_name'] . ' ' . TITLE);
    $faq_breadcrumb = '<a href="' . zen_href_link(FILENAME_DEFAULT) . '">' . HEADER_TITLE_CATALOG . '</a>' . FAQ_BREAD_CRUMBS_SEPARATOR . '<a href="' . zen_href_link(FILENAME_FAQS) . '">' . BOX_INFORMATION_FAQS . '</a>' . FAQ_BREAD_CRUMBS_SEPARATOR . $faq_metatags->fields['faqs_name'];
?>