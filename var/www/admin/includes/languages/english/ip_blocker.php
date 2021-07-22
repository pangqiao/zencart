<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
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
// $Id: ip_blocker.php, v1.0.0 2009/09/09 $d <noblesenior@gmail.com> $
//
define('IP_BLOCKER_TITLE', 'IP Blocker (v2.1.0)');

define('IP_BLOCKER_ENABLE', 'Enable IP Blocker?');
define('IB_TEXT_PASSWORD', 'Password:');
define('IB_BLOCKED_RANGE', 'Blocked IP Addresses:');
define('IB_ALLOWED_RANGE', 'Allowed IP Addresses:');
define('IB_ON', 'Yes');
define('IB_OFF', 'No');
define('IP_BLOCKER_LOCKOUT_COUNT', 'Login lockout count:');  //-v2.0.1a

define('IP_BLOCKER_INSTRUCTIONS', 'Use this page to configure the <em>IP Blocker</em>.  You can<ul><li>Enable or Disable the IP Blocker\'s operation.</li><li>Set the login lockout count.  The <em>Login Lockout Count</em> is the number of unsuccessful login attempts from the &quot;Special&quot; login page before the page stops taking input and simply presents a white-screen.  A value of 0 in the count results in unlimited attempts allowed.</li><li>Set the IP addresses (or address ranges) that should be blocked from your store.  If an address is present in a blocked-range and is not present in an allowed-range, then the access attempt to your store will be blocked.<br /><br />Enter the IP addresses (or address ranges), one per line. Address ranges can be specified using an asterisk to identify all values in that segment, e.g. <code>192.168.1.*</code>, or you can identify a specific range, e.g. <code>192.168.1.1/17</code>.</li>');

define('IP_BLOCKER_HELP_PASSWORD_SETTINGS_DEFAULT', 'You are currently using the default password (<b>123456</b>); you should change the password before you enable the IP blocker!');
define('IB_PASSWORD_SET', 'Your IP blocker password has been changed from the default value.  If you don\'t remember the value, you can change it here.');

define('IB_MESSAGE_PASSWORD_REQUIRED_ERROR', 'A password is required!');
define('IB_MESSAGE_BAD_BLOCKED_IP_ERROR', 'An error was found in your <em>Blocked IP Addresses</em>: %s');
define('IB_MESSAGE_BAD_ALLOWED_IP_ERROR', 'An error was found in your <em>Allowed IP Addresses</em>: %s');

define('IB_MESSAGE_UPDATED', 'Your IP Blocker settings have been successfully updated.');