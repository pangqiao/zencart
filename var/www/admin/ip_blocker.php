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
// $Id: ip_blocker.php, v1.0.0 2009/09/09 $d <noblesenior@gmail.com> $
// ----------------------
// Modified for Zen Cart v1.5.0+ by lat9 (@vinosdefrutastropicales.com)
// ----------------------

require_once ('includes/application_top.php');

// -----
// Create the IP blocker password (previously in /YOUR_ADMIN/includes/functions/extra_functions/ip_blocker_functions.php.
//
function ip_blocker_md5($password){
  return md5 (md5 ($password . '_secure_key'));
}

$message_pwd = '';
$message_blocklist = '';
$message_passlist = '';

if (isset ($_POST) && isset ($_GET['action']) && $_GET['action'] = 'process') {
  if ($_POST['pwd'] == '') {
    $message_pwd = IB_MESSAGE_PASSWORD_REQUIRED_ERROR;
    $pwd = '';
    
  } else {
    $pwd = ($_POST['pwd'] == $_POST['current_pwd']) ? $_POST['current_pwd'] : ip_blocker_md5 ($_POST['pwd']);
    
  }
  
  $blocklist = zen_db_prepare_input ($_POST['blocklist']);
  $message_blocklist = ip_blocker_save_iplist ($blocklist, 'block');
  
  $passlist = zen_db_prepare_input ($_POST['passlist']);
  $message_passlist = ip_blocker_save_iplist ($passlist, 'pass');
    
  if (($message_pwd . $message_blocklist . $message_passlist) == '') {
    $enabled = (int)$_POST['enable'];
    $lockout_count = (int)$_POST['lockout_count'];
    
    $db->Execute ("UPDATE " . TABLE_IP_BLOCKER . " SET ib_power = $enabled, ib_lockout_count = $lockout_count, ib_password = '$pwd' WHERE ib_id = 1");
    $messageStack->add_session (IB_MESSAGE_UPDATED, 'success');
    zen_redirect (zen_href_link (FILENAME_IP_BLOCKER));
    
  }
  
} else {
  $ip_list = $db->Execute('SELECT * FROM ' . TABLE_IP_BLOCKER . ' WHERE ib_id=1');
  $enabled = (int)$ip_list->fields['ib_power'];
  $lockout_count = $ip_list->fields['ib_lockout_count'];
  $pwd = $ip_list->fields['ib_password'];
  $blocklist = ip_blocker_array_to_list (unserialize ($ip_list->fields['ib_blocklist']));
  $blocklist = (is_array ($blocklist)) ? implode ("\r\n", $blocklist) : '';
  $passlist = ip_blocker_array_to_list (unserialize ($ip_list->fields['ib_passlist']));
  $passlist = (is_array ($passlist)) ? implode ("\r\n", $passlist) : '';
  
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<style>
<!--
div, form, input, span, ul, li { margin: 0; padding: 0; }
ul { list-style-position: inside; }
li { padding-top: 5px; }
.d_title { margin-bottom: 15px; }
.instructions { }
.form-row { padding-bottom: 15px; }
.labelRow { font-weight: bold; float: left; width: 15em; }
.inputField { float: left; }
.cb { clear: both; }
.error { padding: 5px; color: red; border: 1px dotted red; margin-bottom: 5px; }
#main-holder { width: 100%; margin: 20px; padding: 20px; font-size: 12px; }
.spacer { width: 15em; }
-->
</style>
<script type="text/javascript" src="includes/menu.js"></script>
<script type="text/javascript">
<!--
function init()
{
  cssjsmenu('navbar');
  if (document.getElementById)
  {
    var kill = document.getElementById('hoverJS');
    kill.disabled = true;
  }

}
//-->
</script>
</head>
<body onload="init();">
<!-- header //-->
<?php 
require(DIR_WS_INCLUDES . 'header.php'); 
?>
<div id="main-holder">
  <div class="pageHeading d_title"><?php echo IP_BLOCKER_TITLE; ?></div>
  <div class="instructions"><p><?php echo IP_BLOCKER_INSTRUCTIONS; ?></p></div>
  <div><?php echo zen_draw_form('blocker', FILENAME_IP_BLOCKER, 'action=process'); ?>
      <div class="form-row">
        <div class="labelRow"><?php echo IP_BLOCKER_ENABLE; ?></div>
        <div class="inputField"><?php echo zen_draw_radio_field('enable', '1', $enabled) . IB_ON . '&nbsp;&nbsp;' . zen_draw_radio_field('enable', '0', !$enabled) . IB_OFF; ?></div>
        <div class="cb"></div>
      </div>
      <div class="form-row">
        <div class="labelRow"><?php echo IP_BLOCKER_LOCKOUT_COUNT; ?></div>
        <div class="inputField"><?php echo zen_draw_input_field('lockout_count', $lockout_count, 'style="width: 2em;"'); ?></div>
        <div class="cb"></div>
      </div>
      <div class="form-row">
<?php
if ($message_pwd != '') {
?>
        <div class="labelRow">&nbsp;</div>
        <div class="inputField error"><?php echo $message_pwd; ?></div>
        <div class="cb"></div>
<?php
}
?>
        <div class="labelRow"><?php echo IB_TEXT_PASSWORD; ?></div>
        <div class="inputField"><?php echo zen_draw_password_field('pwd', $pwd) . zen_draw_hidden_field ('current_pwd', $pwd) . '&nbsp;&nbsp;&nbsp;' . (($pwd == ip_blocker_md5 ('123456')) ? IP_BLOCKER_HELP_PASSWORD_SETTINGS_DEFAULT : IB_PASSWORD_SET); ?></div>
        <div class="cb"></div>
      </div>
      <div class="form-row">
<?php
if ($message_blocklist != '' || $message_passlist != '') {
  $message = ($message_blocklist == '') ? '' : sprintf (IB_MESSAGE_BAD_BLOCKED_IP_ERROR, $message_blocklist);
  if ($message_passlist != '') {
    $message .= (($message == '') ? '' : '<br />') . sprintf (IB_MESSAGE_BAD_ALLOWED_IP_ERROR, $message_passlist);
  }
?>
        <div class="labelRow">&nbsp;</div>
        <div class="inputField error"><?php echo $message; ?></div>
        <div class="cb"></div>
<?php
}
?>
        <div class="labelRow"><?php echo IB_BLOCKED_RANGE; ?></div>
        <div class="inputField"><?php echo zen_draw_textarea_field ('blocklist', 'nowrap', 35, 15, $blocklist); ?></div>
        <div class="spacer"></div>
        <div class="labelRow"><?php echo IB_ALLOWED_RANGE; ?></div>
        <div class="inputField"><?php echo zen_draw_textarea_field ('passlist', 'nowrap', 35, 15, $passlist); ?></div>
        <div class="cb"></div>
      </div>
      <div id="the_button" class="cb"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE, BUTTON_UPDATE_ALT); ?></div>
    </form>
  </div>
</div>
<?php
require(DIR_WS_INCLUDES . 'footer.php'); 
?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>