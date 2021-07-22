<?php
// Search Log v2.2
// Written By C.J.Pinder (c) 2007
// Portions Copyright 2003-2007 Zen Cart Development Team
// Portions Copyright 2003 osCommerce
//
// This source file is subject to version 2.0 of the GPL license, 
// that is bundled with this package in the file LICENSE, and is
// available through the world-wide-web at the following url:
// http://www.zen-cart.com/license/2_0.txt
// If you did not receive a copy of the zen-cart license and are unable
// to obtain it through the world-wide-web, please send a note to
// license@zen-cart.com so we can mail you a copy immediately.    

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

define('TABLE_SEARCH_LOG', DB_PREFIX . 'search_log');
define('FILENAME_STATS_SEARCH_LOG', 'stats_search_log.php');
define('STATS_SEARCH_LOG_VERSION', '2.2');
?>