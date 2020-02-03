<?php
/**
 * @package admin
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: recover_cart_sales.php v4.00.00a 2012/04/23 testuser -> iChoze -> Zen Cart v1.5 $ Exp $
 */
/*
 $Id: recover_cart_sales.php,v 1.6 2005/06/22 06:10:35 lane Exp $
 Recover Cart Sales Tool v2.11

 Copyright (c) 2003-2005 JM Ivler / Ideas From the Deep / OSCommerce
 Released under the GNU General Public License

 Based on an original release of unsold carts by: JM Ivler

 That was modifed by Aalst (aalst@aalst.com) until v1.7 of stats_unsold_carts.php

 Then, the report was turned into a sales tool (recover_cart_sales.php) by
 JM Ivler based on the scart.php program that was written off the Oct 8 unsold carts code release.

 Modifed by Aalst (recover_cart_sales.php,v 1.2 ... 1.36)
 aalst@aalst.com

 Modifed by willross (recover_cart_sales.php,v 1.4)
 reply@qwest.net
 - don't forget to flush the 'scart' db table every so often

 Modified by Lane Roathe (recover_cart_sales.php,v 1.4d .. v2.11)
 lane@ifd.com	www.osc-modsquad.com / www.ifd.com
*/
require('includes/application_top.php');

require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . FILENAME_MAIL . '.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ($action == 'set_editor') {
	// Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
	zen_redirect(zen_href_link(FILENAME_RECOVER_CART_SALES));
}

// Delete Entry Begin
if ($_GET['action'] == 'delete') {
	$customer = $db->Execute("SELECT customers_firstname, customers_lastname
														FROM " . TABLE_CUSTOMERS . "
														WHERE customers_id ='" . (int)$_GET['customer_id'] . "' LIMIT 1");
	$db->Execute("DELETE FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id='" . (int)$_GET['customer_id'] . "'");
	$db->Execute("DELETE FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " WHERE customers_id='" . (int)$_GET['customer_id'] . "'");
	$messageStack->add(MESSAGE_STACK_CART_CUSTOMER . $customer->fields['customers_firstname'] . ' ' . $customer->fields['customers_lastname'] . ' (ID ' . $_GET['customer_id'] . ')' . MESSAGE_STACK_DELETE_SUCCESS, 'success');
}
// Delete Entry End

// Set Contacted Begin
if ($_GET['action'] == 'setconacted') {
	$customer = $db->Execute("SELECT customers_firstname, customers_lastname
														FROM " . TABLE_CUSTOMERS . "
														WHERE customers_id ='" . (int)$_GET['customer_id'] . "' LIMIT 1");
	// See if a record for this customer already exists; if not create one and if so update it
	$donequery = $db->Execute("SELECT * FROM ". TABLE_SCART ." WHERE customers_id = '" . (int)$_GET['customer_id'] . "'");
	if ($donequery->RecordCount() == 0)
		$db->Execute("INSERT INTO " . TABLE_SCART . " (customers_id, dateadded, datemodified) VALUES ('" . (int)$_GET['customer_id'] . "', '" . date('Ymd') . "', '" . date('Ymd') . "')");
	else
		$db->Execute("UPDATE " . TABLE_SCART . " SET datemodified = '" . date('Ymd') . "' WHERE customers_id = " . (int)$_GET['customer_id']);
	$messageStack->add(MESSAGE_STACK_CUSTOMER . $customer->fields['customers_firstname'] . ' ' . $customer->fields['customers_lastname'] . ' (ID ' . $_GET['customer_id'] . ')' . MESSAGE_STACK_SETCONACTED, 'success');
}
// Set Contacted End

$tdate = (isset($_GET['tdate']) ? (int)$_GET['tdate'] : RCS_BASE_DAYS);

function zen_cart_date_short($raw_date) {
	if ($raw_date <= 0)
		return false;
	$year = substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 4, 2);
	$day = (int)substr($raw_date, 6, 2);
	if (@date('Y', mktime(0, 0, 0, $month, $day, $year)) == $year) {
		return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
	} else {
    return preg_replace('/2037$/', $year, date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, 2037))); /*lat9-c*/
	}
}

// This will return a list of active customers
function zen_GetCustomerOnline() {
	global $db;
  $members = array();
// Set expiration time, default is 1200 secs (20 mins)
  $xx_mins_ago = (time() - 1200);
  $whos_online_query = $db->Execute("SELECT customer_id
                                     FROM " . TABLE_WHOS_ONLINE . "
                                     WHERE time_last_click > '" . $xx_mins_ago . "'");
  while (!$whos_online_query->EOF) {
    if ($whos_online_query->fields['customer_id'] != 0) {
    	$members[] = $whos_online_query->fields['customer_id'];
    }
    $whos_online_query->MoveNext();
  }
  return $members;
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
<?php if ($editor_handler != '') { $PHP_SELF_save = $PHP_SELF; $PHP_SELF = 'mail.php'; include ($editor_handler); $PHP_SELF = $PHP_SELF_save; } ?>
</head>
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="main" align="right">
<?php
        echo DAYS_FIELD_PREFIX . zen_draw_form('set_depth_days', FILENAME_RECOVER_CART_SALES, '', 'get') . '&nbsp;&nbsp;' . zen_draw_input_field('tdate', $tdate, 'size="4" style="text-align:right;"') . DAYS_FIELD_POSTFIX .
        zen_hide_session_id() .
//        zen_image_submit('', DAYS_FIELD_BUTTON) .
        '<input type="submit" title="' . DAYS_FIELD_BUTTON . '" value="' . DAYS_FIELD_BUTTON . '" />' .
        '</form>';

// toggle switch for editor
        echo '&nbsp;&nbsp;&nbsp;&nbsp' . TEXT_EDITOR_INFO . zen_draw_form('set_editor_form', FILENAME_RECOVER_CART_SALES, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onChange="this.form.submit();"') .
        zen_hide_session_id() .
        zen_draw_hidden_field('action', 'set_editor') .
        '</form>';
?>
            </td>
          </tr>
        </table></td>
      </tr>
<?php if(isset($_GET['action']) && $_GET['action'] == 'sendmail') { ?>
      <tr>
        <td><table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
   <td class="pageHeading" align="left" colspan=6><?php echo HEADING_EMAIL_SENT; /*lat9-c*/ ?></td>
  </tr>
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left" colspan="1" width="15%"><?php echo TABLE_HEADING_CUSTOMER; ?></td>
    <td class="dataTableHeadingContent" align="left" colspan="1" width="30%">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left" colspan="1" width="25%">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left" colspan="1" width="10%">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left" colspan="1" width="10%">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left" colspan="1" width="10%">&nbsp;</td>
  </tr>
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%"><?php echo TABLE_HEADING_MODEL; ?></td>
    <td class="dataTableHeadingContent" align="left"   colspan="2"  width="55%"><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
    <td class="dataTableHeadingContent" align="center" colspan="1"  width="10%"> <?php echo TABLE_HEADING_QUANTY; ?></td>
    <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%"><?php echo TABLE_HEADING_PRICE; ?></td>
    <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%"><?php echo TABLE_HEADING_TOTAL; ?></td>
  </tr>
<?php
	if(!isset($_POST['custid'])) $_POST['custid'] = array();
	foreach ($_POST['custid'] as $cid) {
		$mline = '';
		$basket = $db->Execute("SELECT    cb.products_id,
																			cb.customers_basket_quantity,
																			cb.customers_basket_date_added,
																			cus.customers_firstname fname,
																			cus.customers_lastname lname,
																			cus.customers_email_address email
														FROM      " . TABLE_CUSTOMERS_BASKET . " cb,
																			" . TABLE_CUSTOMERS . " cus
														WHERE     cb.customers_id = cus.customers_id AND
																			cus.customers_id ='" . $cid . "'
														ORDER BY  cb.customers_basket_date_added DESC ");
		while (!$basket->EOF) {

		// set new cline and curcus
			if ($lastcid != $cid) {
				if ($lastcid != "") {
					$cline .= "
					<tr>
						<td class='dataTableContent' align='right' colspan='6'><b>" . TABLE_CART_TOTAL . "</b>" . $currencies->format($tprice) . "</td>
					</tr>
					<tr>
						<td colspan='6' align='right'><a href=" . zen_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate) . ">" . zen_image_button('button_delete.gif', IMAGE_DELETE) . "</a></td>
					</tr>\n";
					echo $cline;
				}
				$cline = "<tr> <td class='dataTableContent' align='left' colspan='6'><a href='" . zen_href_link(FILENAME_CUSTOMERS, 'search=' . $basket->fields['lname']) . "'>" . $basket->fields['fname'] . " " . $basket->fields['lname'] . "</a>".$customer."</td></tr>";
				$tprice = 0;
			}
			$lastcid = $cid;

			$products = $db->Execute("SELECT p.products_model model,
																				pd.products_name name
																FROM " . TABLE_PRODUCTS . " p,
																		 " . TABLE_PRODUCTS_DESCRIPTION . " pd
																WHERE p.products_id = '" . $basket->fields['products_id'] . "'
																  AND pd.products_id = p.products_id
																  AND pd.language_id = " . (int)$_SESSION['languages_id']);

			$sprice = zen_get_products_actual_price($basket->fields['products_id']);

			$tprice += $basket->fields['customers_basket_quantity'] * $sprice;

			$cline .= "<tr class='dataTableRow'>
									 <td class='dataTableContent' align='left' width='15%'>" . $products->fields['model'] . "</td>
											<td class='dataTableContent' align='left' colspan='2' width='55%'><a href='" . zen_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $basket->fields['products_id'] . '&origin=' . FILENAME_RECOVER_CART_SALES . '?page=' . $_GET['page']) . "'>" . $products->fields['name'] . "</a></td>
											<td class='dataTableContent' align='center' width='10%'>" . $basket->fields['customers_basket_quantity'] . "</td>
											<td class='dataTableContent' align='right' width='10%'>" . $currencies->format($sprice) . "</td>
											<td class='dataTableContent' align='right' width='10%'>" . $currencies->format($basket->fields['customers_basket_quantity'] * $sprice) . "</td>
									 </tr>";

			$mline .= $basket->fields['customers_basket_quantity'] . ' x ' . $products->fields['name'] . "\n";
			$mline .= '   <blockquote><a href="' . zen_catalog_href_link(FILENAME_PRODUCT_INFO, 'products_id='. $basket->fields['products_id']) . '">' . zen_catalog_href_link(FILENAME_PRODUCT_INFO, 'products_id='. $basket->fields['products_id']) . "</a></blockquote>\n\n";
			$basket->MoveNext();
		}

		$cline .= "</td></tr>";

		// E-mail Processing - Requires EMAIL_* defines in the
		// includes/languages/english/recover_cart_sales.php file
		$email = '';

		if (RCS_EMAIL_FRIENDLY == 'true'){
			$email .= EMAIL_TEXT_SALUTATION . $basket->fields['fname'] . ' ' . $basket->fields['lname'] . ",";
		} else {
			$email .= STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n";
		}

		$cquery = $db->Execute("SELECT * FROM " . TABLE_ORDERS . " WHERE customers_id = '" . $cid . "'" );
		if ($cquery->RecordCount() < 1) {
			$email .= sprintf(EMAIL_TEXT_NEWCUST_INTRO, $mline);
		} else {
			$email .= sprintf(EMAIL_TEXT_CURCUST_INTRO, $mline);
		}

		$email .= EMAIL_TEXT_BODY_HEADER . $mline . EMAIL_TEXT_BODY_FOOTER;

		if( EMAIL_USE_HTML == 'true' )
			$email .= '<a href="' . zen_catalog_href_link(FILENAME_DEFAULT) . '">' . STORE_OWNER . "\n" . zen_catalog_href_link(FILENAME_DEFAULT)  . '</a>';
		else
			$email .= STORE_OWNER . "\n" . zen_catalog_href_link(FILENAME_DEFAULT);

		$email .= "\n\n";

		$email .= "\n" . EMAIL_SEPARATOR . "\n\n";
		$email .= EMAIL_TEXT_LOGIN;

		if( EMAIL_USE_HTML == 'true' )
			$email .= '  <a href="' . zen_catalog_href_link(FILENAME_LOGIN, '', 'SSL') . '">' . zen_catalog_href_link(FILENAME_LOGIN, '', 'SSL') . '</a>';
		else
			$email .= '  (' . zen_catalog_href_link(FILENAME_LOGIN, '', 'SSL') . ')';

		$custname = $basket->fields['fname']." ".$basket->fields['lname'];
		$outEmailAddr = '"' . $custname . '" <' . $basket->fields['email'] . '>';
		if( zen_not_null(RCS_EMAIL_COPIES_TO) )
			$outEmailAddr .= ', ' . RCS_EMAIL_COPIES_TO;

		$html_msg['EMAIL_MESSAGE_HTML'] = nl2br($email) . zen_db_prepare_input($_POST['message_html']);
		$email = strip_tags($email . "\n\n" . zen_db_prepare_input($_POST['message']));
		$from = zen_db_prepare_input($_POST['from']); // STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS
		$subject = zen_db_prepare_input($_POST['subject']); // EMAIL_TEXT_SUBJECT
		zen_mail('', $outEmailAddr, $subject, $email, '', $from, $html_msg);

		$mline = "";

		// See if a record for this customer already exists; if not create one and if so update it
		$donequery = $db->Execute("SELECT * FROM ". TABLE_SCART ." WHERE customers_id = '" . $cid . "'");
		if ($donequery->RecordCount() == 0)
			$db->Execute("INSERT INTO " . TABLE_SCART . " (customers_id, dateadded, datemodified) VALUES ('" . $cid . "', '" . date('Ymd') . "', '" . date('Ymd') . "')");
		else
			$db->Execute("UPDATE " . TABLE_SCART . " SET datemodified = '" . date('Ymd') . "' WHERE customers_id = " . $cid );

		echo $cline;

		$cline = "";
	}
?>
          <tr><td colspan=8 align="right" class="dataTableContent"><?php echo '<b>' . TABLE_CART_TOTAL . '</b>' . $currencies->format($tprice); ?></td> </tr>
          <tr><td colspan=6 align="right"><a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate); ?>"><?php echo zen_image_button('button_delete.gif', IMAGE_DELETE); ?></a></td></tr>
          <tr><td colspan=6 align=center><a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES); ?>"><?php echo TEXT_RETURN; ?></a></td></tr>
        </table></td>
      </tr>
<?php
} else {  //we are not doing an e-mail to some customers
?>
      <tr>
        <td>
          <?php echo zen_draw_form('mail', FILENAME_RECOVER_CART_SALES,'action=sendmail','post', 'enctype="multipart/form-data"') . "\n"; //action=preview ?>
<!-- REPORT TABLE BEGIN //-->
          <table border="0" width="100%" cellspacing="2" cellpadding="2">
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="2" width="10%"><?php echo TABLE_HEADING_CONTACT; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="15%"><?php echo TABLE_HEADING_DATE; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="30%"><?php echo TABLE_HEADING_CUSTOMER; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="30%"><?php echo TABLE_HEADING_EMAIL; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="15%"><?php echo TABLE_HEADING_PHONE; ?></td>
            </tr>
<!--
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="2"  width="10%">&nbsp; </td>
              <td class="dataTableHeadingContent" align="left" colspan="1"  width="15%"><?php echo TABLE_HEADING_MODEL; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="55%"><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
              <td class="dataTableHeadingContent" align="center" colspan="1" width="5%"> <?php echo TABLE_HEADING_QUANTY; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="5%"><?php echo TABLE_HEADING_PRICE; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1" width="10%"><?php echo TABLE_HEADING_TOTAL; ?></td>
            </tr>
-->
<?php
	$cust_ses_ids = zen_GetCustomerOnline();
	$basket = $db->Execute("SELECT cb.customers_id,
																 cb.products_id,
																 cb.customers_basket_quantity,
																 cb.customers_basket_date_added,
																 cus.customers_firstname,
																 cus.customers_lastname,
																 cus.customers_telephone,
																 cus.customers_email_address,
																 sc.datemodified
													FROM " . TABLE_CUSTOMERS_BASKET . " cb
														LEFT JOIN " . TABLE_CUSTOMERS . " cus ON (cb.customers_id = cus.customers_id)
														LEFT JOIN " . TABLE_SCART . " sc ON (cb.customers_id = sc.customers_id)
													WHERE cb.customers_basket_date_added >= '" . date('Ymd', time()-$tdate*24*60*60) . "'
														AND cb.customers_id NOT IN ('" . implode(", ", $cust_ses_ids) . "')
													ORDER BY cb.customers_id ASC, cb.customers_basket_date_added DESC");
	$results = 0;
	$curcus = 0;
	$tprice = 0;
	$totalAll = 0;
	$first_line = true;
	$skip = false;

	while (!$basket->EOF) {
//echo '<pre>';var_export($basket->fields);echo '</pre>';
	// If this is a new customer, create the appropriate HTML
		if ($curcus != $basket->fields['customers_id']) {
			$totalAll += $tprice;
			if ($curcus != 0 && !$skip) {
?>
            </tr>
              <td class="dataTableContent" align="right" colspan="8"><?php echo '<b>' . TABLE_CART_TOTAL . '</b> ' . $currencies->format($tprice); ?></td>
            </tr>
            <tr>
              <td colspan="8" align="right"><a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES, 'action=setconacted&customer_id=' . $curcus . '&tdate=' . $tdate); ?>"><?php echo TEXT_SET_CONTACTED; ?></a>&nbsp;<a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES, 'action=delete&customer_id=' . $curcus . '&tdate=' . $tdate); ?>"><?php echo zen_image_button('button_delete.gif', IMAGE_DELETE); ?></a></td>
            </tr>
<?php }
			$curcus = $basket->fields['customers_id'];
			$tprice = 0;

			// change the color on those we have contacted add customer tag to customers
			$fcolor = RCS_UNCONTACTED_COLOR;
			$checked = 1; // assume we'll send an email
			$new = 1;
			$skip = false;
			$sentdate = "";
			$beforeDate = RCS_CARTS_MATCH_ALL_DATES ? '0' : $basket->fields['customers_basket_date_added'];
			$customer = $basket->fields['customers_firstname'] . " " . $basket->fields['customers_lastname'];
			$status = "";
			if ((time()-(RCS_EMAIL_TTL*24*60*60)) <= strtotime($basket->fields['datemodified'])) {
				$sentdate = $basket->fields['datemodified'];
				$fcolor = RCS_CONTACTED_COLOR;
				$checked = 0;
				$new = 0;
			}
			// See if the customer has purchased from us before
			// Customers are identified by either their customer ID or name or email address
			// If the customer has an order with items that match the current order, assume
			// order completed, bail on this entry!
			$orec = $db->Execute('SELECT orders_id, orders_status
														FROM ' . TABLE_ORDERS . '
														WHERE (customers_id = ' . (int)$curcus . '
																		OR customers_email_address like "' . $basket->fields['customers_email_address'] .'"
																		OR customers_name like "' . $basket->fields['customers_firstname'] . ' ' . $basket->fields['customers_lastname'] . '")
															AND date_purchased >= "' . $basket->fields['customers_basket_date_added'] . '"' );
			if ($orec->RecordCount() > 0) {
        // skip repeat customers
        if (RCS_CHECK_REPEAT == 'true') {
          $skip = true; 
        } else {
				  // We have a matching order; assume current customer but not for this order
				  $customer = '<font color=' . RCS_CURCUST_COLOR . '><b>' . $customer . '</b></font>';
				  // Now, look to see if one of the orders matches this current order's items
				  while(!$orec->EOF) {
					  $ccquery = $db->Execute('SELECT products_id
					                           FROM ' . TABLE_ORDERS_PRODUCTS . '
					                           WHERE orders_id = ' . (int)$orec->fields['orders_id'] . '
					                             AND products_id = ' . (int)(int)$basket->fields['products_id'] );
					  if( $ccquery->RecordCount() > 0 ) {
						  if( $orec->fields['orders_status'] > RCS_PENDING_SALE_STATUS )
							  $checked = 0;
						  // OK, we have a matching order; see if we should just skip this or show the status
						  if( RCS_SKIP_MATCHED_CARTS == 'true' && !$checked ) {
							  $skip = true; // reset flag & break us out of the while loop!
							  break;
						  } else {
						  // It's rare for the same customer to order the same item twice, so we probably have a matching order, show it
							  $fcolor = RCS_MATCHED_ORDER_COLOR;
							  $srec = $db->Execute("SELECT orders_status_name
							                        FROM " . TABLE_ORDERS_STATUS . "
							                        WHERE language_id = " . (int)$_SESSION['languages_id'] . "
							                          AND orders_status_id = " . (int)$orec->fields['orders_status'] );
							  if( $srec )
								  $status = ' [' . $srec->fields['orders_status_name'] . ']';
							  else
								  $status = ' ['. TEXT_CURRENT_CUSTOMER . ']';
						  }
					  }
					  $orec->MoveNext();
				  }
        }
				if( $skip )
					continue; // got a matched cart, skip to next one
			}

?>
    <tr bgcolor="<?php echo $fcolor; ?>">
      <td class="dataTableContent" align="center" width="1%"><?php echo zen_draw_checkbox_field("custid[]", $curcus, RCS_AUTO_CHECK == "true" ? $checked : 0); ?></td>
      <td class="dataTableContent" align="left" width="9%"><b><?php echo ($sentdate != "" ? zen_cart_date_short($sentdate) : TEXT_NOT_CONTACTED); ?></b></td>
      <td class="dataTableContent" align="left" width="15%"><?php echo zen_cart_date_short($basket->fields['customers_basket_date_added']); ?></td>
      <td class="dataTableContent" align="left" width="30%"><a href="<?php echo zen_href_link(FILENAME_CUSTOMERS, "search=" . $basket->fields['customers_lastname']); ?>" target="_blank"><?php echo $customer; ?></a><?php echo $status; ?></td>
      <td class="dataTableContent" align="left" colspan="2" width="30%"><a href="<?php echo zen_href_link("mail.php", "selected_box=tools&customer=" . $basket->fields['customers_email_address']); ?>" target="_blank"><?php echo $basket->fields['customers_email_address']; ?></a></td>
      <td class="dataTableContent" align="left" colspan="2" width="15%"><?php echo $basket->fields['customers_telephone']; ?></td>
    </tr>
<?php
		}

		// We only have something to do for the product if the quantity selected was not zero!
		if ($basket->fields['customers_basket_quantity'] > 0) {
			// Get the product information (name, price, etc)
			$products = $db->Execute("SELECT p.products_model model,
																			 p.products_type,
																			 pd.products_name name
																FROM " . TABLE_PRODUCTS . " p
																	LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (pd.products_id = p.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "')
																WHERE p.products_id = '" . (int)$basket->fields['products_id'] . "'");
			// Check to see if the product is on special, and if so use that pricing
			$sprice = zen_get_products_actual_price( (int)$basket->fields['products_id'] );

			// BEGIN OF ATTRIBUTE DB CODE
			$prodAttribs = ''; // DO NOT DELETE
			if (RCS_SHOW_ATTRIBUTES == 'true') {
				$attribrecs = $db->Execute("SELECT cba.products_id,
																					 po.products_options_name poname,
																					 pov.products_options_values_name povname
																		FROM " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " cba,
																				 " . TABLE_PRODUCTS_OPTIONS . " po,
																				 " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
																				 " . TABLE_LANGUAGES . " l
																		WHERE cba.products_id ='" . $basket->fields['products_id'] . "'
																			AND cba.customers_id = " . $curcus . "
																			AND po.products_options_id = cba.products_options_id
																			AND pov.products_options_values_id = cba.products_options_value_id
																			AND po.language_id =" . (int)$_SESSION['languages_id'] . "
																			AND pov.language_id =" . (int)$_SESSION['languages_id']);
				$hasAttributes = false;
				if ($attribrecs->RecordCount() > 0){
					$hasAttributes = true;
					$prodAttribs = '<br />';
					while (!$attribrecs->EOF){
						$prodAttribs .= '<small><i> - ' . $attribrecs->fields['poname'] . ' ' . $attribrecs->fields['povname'] . '</i></small><br />';
						$attribrecs->MoveNext();
					}
				}
			}
			// END OF ATTRIBUTE DB CODE
			$tprice += $basket->fields['customers_basket_quantity'] * $sprice;
			$ib++;
?>
  <tr class="dataTableRow">
    <td class="dataTableContent" align="left" vAlign="top" colspan="2" width="10%"><?php echo $ib; ?></td>
    <td class="dataTableContent" align="left" vAlign="top" width="15%"><?php echo $products->fields['model']; ?></td>
    <td class="dataTableContent" align="left" vAlign="top" colspan="2" width="55%"><a href="<?php echo zen_href_link($zc_products->get_admin_handler($products->fields['products_type']), "page=" . $_GET['page'] . "&product_type=" . $products->fields['products_type'] . "&cPath=" . zen_get_product_path((int)$basket->fields['products_id']) . "&pID=" . (int)$basket->fields['products_id'] . "&action=new_product"); ?>" target="_blank"><b><?php echo $products->fields['name']; ?></b></a><?php echo $prodAttribs; ?></td>
    <td class="dataTableContent" align="center" vAlign="top" width="5%"><?php echo $basket->fields['customers_basket_quantity']; ?></td>
    <td class="dataTableContent" align="right"  vAlign="top" width="5%"><?php echo $currencies->format($sprice); ?></td>
    <td class="dataTableContent" align="right"  vAlign="top" width="10%"><?php echo $currencies->format($basket->fields['customers_basket_quantity'] * $sprice); ?></td>
  </tr>
<?php
		}
		$basket->MoveNext();
	}
	if ($curcus != 0) {
		$totalAll += $tprice;
?>
    <tr>
      <td class='dataTableContent' align='right' colspan='8'><?php echo '<b>' . TABLE_CART_TOTAL . '</b>' . $currencies->format($tprice); ?></td>
    </tr>
    <tr>
      <td colspan='8' align='right'><a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES, 'action=setconacted&customer_id=' . $curcus . '&tdate=' . $tdate); ?>"><?php echo TEXT_SET_CONTACTED; ?></a>&nbsp;<a href="<?php echo zen_href_link(FILENAME_RECOVER_CART_SALES, 'action=delete&customer_id=' . $curcus . '&tdate=' . $tdate); ?>"><?php echo zen_image_button('button_delete.gif', IMAGE_DELETE); ?></a></td>
    </tr>
<?php  } ?>
    <tr>
      <td class='dataTableContent' align='right' colspan='8'><hr align=right><?php echo '<b>' . TABLE_GRAND_TOTAL . '</b>' . $currencies->format($totalAll); ?></td>
    </tr>
  </table></td>
    </tr>
    <tr>
      <td><table width="100%">
        <tr>
          <td valign="top" class="main" width="5%"></td>
          <td valign="top" class="main" width="95%"></td>
        </tr>
        <tr>
          <td valign="top" class="main" colspan=2><hr size=1 color=000080></td>
        </tr>
        <tr>
          <td valign="top" class="main" colspan=2><b><?php echo PSMSG; ?></b></td>
        </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_FROM; ?></td>
            <td><?php echo zen_draw_input_field('from', (isset($_POST['from']) ? $_POST['from'] : STORE_OWNER . ' <' . STORE_OWNER_EMAIL_ADDRESS . '>'), 'size="60"'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_SUBJECT; ?></td>
            <td><?php echo zen_draw_input_field('subject', (isset($_POST['subject']) ? $_POST['subject'] : EMAIL_TEXT_SUBJECT), 'size="60"'); ?></td>
          </tr>
        <tr>
          <td valign="top" class="main"><?php echo TEXT_MESSAGE_HTML;?></td>
          <td class="main" width="750">
<?php if (EMAIL_USE_HTML != 'true') echo TEXT_WARNING_HTML_DISABLED; ?>
<?php if (EMAIL_USE_HTML == 'true') {
	if ($_SESSION['html_editor_preference_status']=="FCKEDITOR") {
		$oFCKeditor = new FCKeditor('message_html') ;
		$oFCKeditor->Value = stripslashes($_POST['message_html']) ;
		$oFCKeditor->Width  = '97%' ;
		$oFCKeditor->Height = '350' ;
//		$oFCKeditor->Create() ;
		$output = $oFCKeditor->CreateHtml() ; echo $output;
	} else { // using HTMLAREA or just raw "source"
		echo zen_draw_textarea_field('message_html', 'soft', '100%', '25', stripslashes($_POST['message_html']), 'id="message_html"');
	}
} ?>
          </td>
        </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
            <td><?php echo zen_draw_textarea_field('message', 'soft', '100%', '15', $_POST['message']); ?></td>
          </tr>
          <tr>
<!--            <td colspan="2" align="right"><?php echo zen_image_submit('button_preview.gif', IMAGE_PREVIEW); ?></td> //-->
            <td colspan="2" align="right"><?php echo zen_image_submit('button_send_mail.gif', IMAGE_SEND_EMAIL); ?></td>
          </tr>
        </table>
      </td>
    </tr>
    </form>
<?php
}
//
// end footer of both e-mail and report
//
?>
  <!-- REPORT TABLE END //-->
<!-- body_text_eof //-->
</tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>