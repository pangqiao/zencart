<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// +----------------------------------------------------------------------+
// | export of orders is based on easypopulate module 2005 by langer      |
// +----------------------------------------------------------------------+
// $Id: ordersExport_functions.php,v0.1 2007 matej $
//

function mat_query($query) {
	// global $mat_debug_logging, $mat_debug_logging_all, $mat_stack_sql_error;
	global $mat_sql_errors_msgs, $mat_stack_sql_error;
	$result = mysql_query($query);
	if (mysql_errno()) {
		$mat_stack_sql_error = true;
		$mat_sql_errors_msgs .= "<br />".mysql_errno() . ": " . mysql_error() . "\n";
	}
	return $result;
}

?>