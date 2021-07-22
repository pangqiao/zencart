<?php
// referrals mod

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (function_exists('zen_register_admin_page')) {
    if (!zen_page_key_exists('referrals')) {
        zen_register_admin_page('referrals', 'BOX_CUSTOMERS_REFERRALS','FILENAME_REFERRALS', '', 'localization', 'Y', 30);
    }
    if (!zen_page_key_exists('stats_referral_sources')) {
        zen_register_admin_page('stats_referral_sources', 'BOX_REPORTS_REFERRAL_SOURCES','FILENAME_STATS_REFERRAL_SOURCES', '', 'reports', 'Y', 20);
    }
}

/**
 * Removal: Uncomment the following lines to cause complete removal of all database and menu content related to this mod.
 * Then remove this PHP file and all other related PHP files used by this mod.
 */
//zen_deregister_admin_pages('referrals');
//zen_deregister_admin_pages('stats_referral_sources');
//$db->Execute("DROP TABLE IF EXISTS sources");
//$db->Execute("DROP TABLE IF EXISTS sources_other");
//if (defined('TABLE_SOURCES')) $db->Execute("DROP TABLE IF EXISTS " . TABLE_SOURCES);
//if (defined('TABLE_SOURCES_OTHER')) $db->Execute("DROP TABLE IF EXISTS " . TABLE_SOURCES_OTHER);
//$db->Execute("ALTER TABLE " . TABLE_CUSTOMERS_INFO . " DROP customers_info_source_id");
//$db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('DISPLAY_REFERRAL_OTHER', 'REFERRAL_REQUIRED')");
