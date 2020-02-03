<?php
// referrals mod
  
  // rmh referral
  function zen_get_sources_name($source_id, $customers_id) {
    global $db;

    if ($source_id == '9999') {
      $sources_query = "select sources_other_name as sources_name from " . TABLE_SOURCES_OTHER . " where customers_id = '" . (int)$customers_id . "'";
    } else {
      $sources_query = "select sources_name from " . TABLE_SOURCES . " where sources_id = '" . (int)$source_id . "'";
    }

    $sources=$db->Execute($sources_query);

    if ($sources->RecordCount()<= 0) {
      if ($source_id == '9999') {
        return TEXT_OTHER;
      } else {
        return TEXT_NONE;
     }
    } else {
      return $sources->fields['sources_name'];
    }

  }

