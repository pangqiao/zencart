<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
} 

//----
// If the installation supports admin-page registration (i.e. v1.5.0 and later), then
// register the All Invoices tool into the admin menu structure.
//
if (function_exists('zen_register_admin_page')) {
  if (!zen_page_key_exists('reportsAllInvoices')) {
    zen_register_admin_page('reportsAllInvoices', 'BOX_ALL_INVOICES', 'FILENAME_ALL_INVOICES', '' , 'reports', 'Y', 20);
  }
  if (!zen_page_key_exists ('reportsAllPackingSlips')) {
    zen_register_admin_page ('reportsAllPackingSlips', 'BOX_ALL_PACKINGSLIPS', 'FILENAME_ALL_PACKINGSLIPS', '', 'reports', 'Y', 21);
  }
  
}