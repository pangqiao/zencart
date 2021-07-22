<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: featured.php 18695 2011-05-04 05:24:19Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @featured_faqs.php created 2012-09-18 for v1.5 kamelion0927
 */

    function zen_expire_featured_faqs() {
    global $db;
    $date_range = time();
    $zc_featured_faqs_date = date('Ymd', $date_range);
    $featured_faqs_query = "select featured_faqs_id
                       from " . TABLE_FEATURED_FAQS . "
                       where status = '1'
                       and ((" . $zc_featured_faqs_date . " >= expires_date and expires_date != '0001-01-01')
                       or (" . $zc_featured_faqs_date . " < featured_date_available and featured_date_available != '0001-01-01'))";
    $featured_faqs = $db->Execute($featured_faqs_query);
    if ($featured_faqs->RecordCount() > 0) {
      while (!$featured_faqs->EOF) {
        zen_set_featured_faqs_status($featured_faqs->fields['featured_faqs_id'], '0');
        $featured_faqs->MoveNext();
      }
    }
  }
  
  function zen_set_featured_faqs_status($featured_faqs_id, $status) {
    global $db;
    $sql = "update " . TABLE_FEATURED_FAQS . "
            set status = '" . (int)$status . "', date_status_change = now()
            where featured_faqs_id = '" . (int)$featured_faqs_id . "'";
    return $db->Execute($sql);
   }
   
////
// Auto start featured faqs
  function zen_start_featured_faqs() {
    global $db;
    $date_range = time();
    $zc_featured_faqs_date = date('Ymd', $date_range);
    $featured_faqs_query = "select featured_faqs_id
                       from " . TABLE_FEATURED_FAQS . "
                       where status = '0'
                       and (((featured_date_available <= " . $zc_featured_faqs_date . " and featured_date_available != '0001-01-01') and (expires_date > " . $zc_featured_faqs_date . "))
                       or ((featured_date_available <= " . $zc_featured_faqs_date . " and featured_date_available != '0001-01-01') and (expires_date = '0001-01-01'))
                       or (featured_date_available = '0001-01-01' and expires_date > " . $zc_featured_faqs_date . "))
                       ";
    $featured_faqs = $db->Execute($featured_faqs_query);
    if ($featured_faqs->RecordCount() > 0) {
      while (!$featured_faqs->EOF) {
        zen_set_featured_faqs_status($featured_faqs->fields['featured_faqs_id'], '1');
        $featured_faqs->MoveNext();
      }
    }
  }
?>