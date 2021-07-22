<?php
// -----
// Part of the Zen Cart "All Invoices" plugin.
// Copyright (C) 2014, Vinos de Frutas Tropicales (lat9@vinosdefrutastropicales.com)
//
require('includes/application_top.php');
$orderStatus = '';
$order_message = '';
if (isset($_GET['status'])) {
  $orderStatus = $_GET['status'];
  $orders_check = $db->Execute("SELECT count(*) AS total FROM " . TABLE_ORDERS . " WHERE orders_status = $orderStatus");
  if ($orders_check->fields['total'] > 0) {
    require('includes/templates/tpl_all_packingslips.php');
    
  } else {
    $order_message = sprintf(WARNING_NO_ORDERS, zen_get_order_status_name($orderStatus));
    
  }
}
if ($orderStatus == '' || $order_message != '') {
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
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
  // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->

    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <p><?php echo TEXT_ALL_PACKINGSLIP_INFO; ?></p>
<?php
if ($order_message != '') {
?>
          <p><?php echo $order_message; ?></p>
<?php
}
?>

          <form style="margin-left: 20px;" action="all_packingslips.php" method="get" target="_new">
<?php
$statuses_array = array();
$statuses = $db->Execute("select orders_status_id, orders_status_name
                          from " . TABLE_ORDERS_STATUS . "
                          where language_id = '" . (int)$_SESSION['languages_id'] . "'
                          order by orders_status_id");

while (!$statuses->EOF) {
  $statuses_array[] = array('id' => $statuses->fields['orders_status_id'],
                            'text' => $statuses->fields['orders_status_name'] . ' [' . $statuses->fields['orders_status_id'] . ']');
  $statuses->MoveNext();
}
echo zen_draw_pull_down_menu('status', $statuses_array);
?>
            <input type="submit" value="Generate">
          </form>
        </td>
      </tr>
    </table></td>

<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php

require(DIR_WS_INCLUDES . 'application_bottom.php');

} // Select status
?>