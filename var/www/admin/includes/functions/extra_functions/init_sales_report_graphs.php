<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
} 
if (function_exists('zen_register_admin_page')) {
    if (!zen_page_key_exists ('sales_report_graphs')) {
        // Add the link to Apsona ShopAdmin
        zen_register_admin_page('sales_report_graphs', 'BOX_SALES_REPORT_GRAPHS', 'FILENAME_SALES_REPORT_GRAPHS', '', 'reports', 'Y', 15);
    }
}
// Don't have closing bracket below - otherwise Zen Cart sends headers, causing trouble.
