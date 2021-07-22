<?php
/*
  $Id: stats_recover_cart_sales.php, v4.00.00a 2012/04/23 testuser -> iChoze -> Zen Cart v1.5 $ Exp $
  Recover Cart Sales Report v2.11
  
  Recover Cart Sales contribution: JM Ivler 11/20/03
  (c) Ivler / Ideas From the Deep / osCommerce

  Released under the GNU General Public License

  Modifed by Aalst (recover_cart_sales.php,v 1.2 .. 1.36)
  aalst@aalst.com

  Modified by Lane Roathe (recover_cart_sales.php,v 1.4d .. v2.11)
  lane@ifd.com	www.osc-modsquad.com / www.ifd.com
*/
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');

$currencies = new currencies();

$tdate = (isset($_GET['tdate']) ? $_GET['tdate'] : RCS_BASE_DAYS);
$ndate = seadate($tdate);

function zen_date_order_stat($raw_date) {
	if ($raw_date == '') return false;
	$year = substr($raw_date, 2, 2);
	$month = (int)substr($raw_date, 4, 2);
	$day = (int)substr($raw_date, 6, 2);
	return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
}

function seadate($day) {
	$ts = date("U");
	$rawtime = strtotime("-".$day." days", $ts);
	$ndate = date("Ymd", $rawtime);
	return $ndate;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
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
  if (typeof _editor_url == "string") HTMLArea.replace('message_html');
}
// -->
</script>
<?php if ($editor_handler != '') include ($editor_handler); ?>
</head>
<body onLoad="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="main" align="right">
<?php
        echo DAYS_FIELD_PREFIX . zen_draw_form('set_depth_days', FILENAME_STATS_RECOVER_CART_SALES, '', 'get') . '&nbsp;&nbsp;' . zen_draw_input_field('tdate', $tdate, 'size="4" style="text-align:right;"') . DAYS_FIELD_POSTFIX .
        zen_hide_session_id() .
//        zen_image_submit('', DAYS_FIELD_BUTTON) .
        '<input type="submit" title="' . DAYS_FIELD_BUTTON . '" value="' . DAYS_FIELD_BUTTON . '" />' .
        '</form>';
?>
            </td>
          </tr>
        </table></td>
      </tr>
<?php
// Init vars
$cust_array = array();
$custknt = 0;
$total_recovered = 0;
$custlist = '';

// Query database for abandoned carts within our timeframe
$scart = $db->Execute("SELECT s.scartid, s.customers_id, s.dateadded, s.datemodified, c.customers_firstname, c.customers_lastname, c.customers_email_address
											 FROM " . TABLE_SCART . " s
												 LEFT JOIN " . TABLE_CUSTOMERS . " c ON (s.customers_id = c.customers_id)
											 WHERE dateadded >= '" . $ndate . "'
											 ORDER BY dateadded DESC" );
$rc_cnt = $scart->RecordCount();

// Loop though each one and process it
while(!$scart->EOF) {
	// Query DB for the FIRST order that matches this customer ID and came
	// after the abandoned cart
	$orders = $db->Execute("SELECT o.orders_id, o.date_purchased, s.orders_status_name, ot.text as order_total, ot.value
                            FROM " . TABLE_ORDERS . " o 
                              LEFT JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id) 
                              LEFT JOIN " . TABLE_ORDERS_STATUS . " s ON (s.orders_status_id = o.orders_status AND s.language_id = " . $_SESSION['languages_id'] . ")
                            WHERE o.customers_id = " . (int)$scart->fields['customers_id'] . "
                              AND o.orders_status > " . RCS_PENDING_SALE_STATUS . "
                              AND o.date_purchased >= '" . date('Y-m-d', strtotime($scart->fields['dateadded'])) . "'
                              AND ot.class = 'ot_total'");
	// If we got a match, create the table entry to display the information
	if (!$orders->EOF) {
		$custknt++;
		$total_recovered += $orders->fields['value'];
		$cust_array[$custknt] = array_merge($scart->fields, $orders->fields);
	}
	$scart->MoveNext();
}
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr>
            <td align="right" class="main"><b><?php echo TOTAL_RECORDS ?></b></td>
            <td align="left" class="main"><?php echo $rc_cnt ?></td>
          </tr>
          <tr>
            <td align="right" class="main"><b><?php echo TOTAL_SALES ?></b></td>
            <td align="left" class="main"><?php echo $custknt . TOTAL_SALES_EXPLANATION ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <!-- Header -->
          <tr class="dataTableHeadingRow">
            <th width="4%"  class="dataTableHeadingContent"><?php echo TABLE_HEADING_SCART_ID ?></td>
            <th width="8%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_SCART_ADD_DATE ?></td>
            <th width="8%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_SCART_SENT_DATE ?></td>
            <th width="50%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMER ?></td>
            <th width="8%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_DATE ?></td>
            <th width="10%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_STATUS ?></td>
            <th width="12%" class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_AMOUNT ?></td>
          </tr>
<?php
//echo '<pre>';var_export($cust_array);echo '</pre>';
	foreach($cust_array as $cust) {
?>
          <tr class="<?php echo ($i % 2 ? RCS_REPORT_EVEN_STYLE : RCS_REPORT_ODD_STYLE); ?>">
            <td class="dataTableContent" align="right"><?php echo $cust['scartid']; ?></td>
            <td class="dataTableContent" align="center"><?php echo zen_date_order_stat($cust['dateadded']); ?></td>
            <td class="dataTableContent" align="center"><?php echo zen_date_order_stat($cust['datemodified']); ?></td>
            <td class="dataTableContent"><a href="<?php echo zen_href_link(FILENAME_CUSTOMERS, "search=" . $cust['customers_email_address']); ?>"><?php echo $cust['customers_firstname'] . " " . $cust['customers_lastname']; ?></a></td>
            <td class="dataTableContent" align="center"><?php echo zen_date_short($cust['date_purchased']); ?></td>
            <td class="dataTableContent" align="center"><?php echo $cust['orders_status_name']; ?></td>
            <td class="dataTableContent" align="right"><?php echo strip_tags($cust['order_total']); ?></td>
          </tr>
<?php
	}
?>
        </table></td>
      </tr>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr>
            <td valign="bottom"><hr width="100%" size="1" color="#800000" noshade></td>
          </tr>
          <tr>
            <td align="right" class="main"><b><?php echo TOTAL_RECOVERED; ?>&nbsp;<?php echo $rc_cnt ? zen_round(($custknt / $rc_cnt) * 100, 2) : 0 ?>%&nbsp;&nbsp;<?php echo $currencies->format(zen_round($total_recovered, 2)); ?></b></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
<!-- body_text_eof //-->
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>