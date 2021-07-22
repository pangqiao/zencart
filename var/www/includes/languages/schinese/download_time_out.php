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
// $Id: download_time_out.php 1969 2005-09-13 06:57:21Z drbyte $
//

define('NAVBAR_TITLE', '您的下载 ...');
define('HEADING_TITLE', '您的下载 ...');

define('TEXT_INFORMATION', '很抱歉，您的下载已过期。<br /><br />
  如果有其他的下载，
  请访问<a href="' . zen_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">我的帐号</a>页面查看订单。<br /><br />
  如果您对订单有任何疑问，请<a href="' . zen_href_link(FILENAME_CONTACT_US) . '">联系我们</a> <br /><br />
  谢谢！
  ');
?>