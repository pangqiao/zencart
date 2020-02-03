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
// $Id: ordersExport.php,v0.1 2007 matej $
//

require_once ('includes/application_top.php');
@set_time_limit(300); // if possible, let's try for 5 minutes before timeouts

$tempdir = ORDERSEXPORT_CONFIG_TEMP_DIR;
// orders export version
$version = '0.1';
$mat_dltype = NULL;
$mat_dlmethod = NULL;
$mat_stack_sql_error = false; // function returns true on any 1 error, and notifies user of an error
$mat_sql_errors_msgs = ORDERSEXPORT_MSGSTACK_ERROR_EXISTS;

$separator = "\t"; // only tab allowed

//I may not need this as the file name is now fixed, let's test it later
if (substr($tempdir, -1) != '/') $tempdir .= '/';
if (substr($tempdir, 0, 1) == '/') $tempdir = substr($tempdir, 1);

// now to create the file layout for each download type..
$mat_dltype = (isset($_GET['dltype'])) ? $_GET['dltype'] : $mat_dltype;
// obf download type is set, we need a file layout
if (zen_not_null($mat_dltype)) {	
	// create a file layout depending on the type of the download (full, noAttribs, onlyAttribs)
	$fieldmap = array(); // future field names	
	switch($mat_dltype){
	case 'full':
		// The file layout is dynamically made 
		$iii = 0;
		$filelayout = array(
	//all columns:
'v_date_purchased'    => $iii++, 
'v_orders_status_name'	=> $iii++,
'v_orders_id'     => $iii++, 
'v_customers_id'    => $iii++, 
'v_customers_name'    => $iii++, 
'v_customers_company'    => $iii++, 
'v_customers_street_address'    => $iii++, 
'v_customers_suburb'    => $iii++, 
'v_customers_city'    => $iii++, 
'v_customers_postcode'    => $iii++, 
//'v_customers_state'    => $iii++, 
'v_customers_country'    => $iii++, 
'v_customers_telephone'    => $iii++, 
'v_customers_email_address'    => $iii++, 
'v_products_model'    => $iii++, 
'v_products_name'    => $iii++, 
'v_products_options'    => $iii++, 
'v_products_options_values'    => $iii++, 
			);

	$filelayout_sql = "SELECT 
zo.orders_id as v_orders_id,
customers_id as v_customers_id,
customers_name as v_customers_name,
customers_company as v_customers_company,
customers_street_address as v_customers_street_address,
customers_suburb as v_customers_suburb,
customers_city as v_customers_city,
customers_postcode as v_customers_postcode,
customers_country as v_customers_country,
customers_telephone as v_customers_telephone,
customers_email_address as v_customers_email_address,
date_purchased as v_date_purchased,
orders_status_name as v_orders_status_name,
products_model as v_products_model,
products_name as v_products_name,
products_options as v_products_options,
products_options_values as v_products_options_values
FROM ".TABLE_ORDERS_PRODUCTS." zop LEFT JOIN ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa
ON(zop.orders_products_id = opa.orders_products_id), ".TABLE_ORDERS." zo, ".TABLE_ORDERS_STATUS." zos
WHERE zo.orders_id = zop.orders_id
AND zo.orders_status = zos.orders_status_id
		";
		break;
		
	case 'fullb':
		// The file layout is dynamically made 
		$iii = 0;
		$filelayout = array(
	//all columns:
'v_date_purchased'    => $iii++, 
'v_orders_status_name'	=> $iii++,
'v_orders_id'     => $iii++, 
'v_customers_id'    => $iii++, 
'v_customers_name'    => $iii++, 
'v_customers_company'    => $iii++, 
'v_customers_street_address'    => $iii++, 
'v_customers_suburb'    => $iii++, 
'v_customers_city'    => $iii++, 
'v_customers_postcode'    => $iii++, 
//'v_customers_state'    => $iii++, 
'v_customers_country'    => $iii++, 
'v_customers_telephone'    => $iii++, 
'v_customers_email_address'    => $iii++, 
'v_products_model'    => $iii++, 
'v_products_name'    => $iii++, 
'v_products_options'    => $iii++, 
'v_products_options_values'    => $iii++, 
			);

	$filelayout_sql = "SELECT 
zo.orders_id as v_orders_id,
customers_id as v_customers_id,
customers_name as v_customers_name,
customers_company as v_customers_company,
customers_street_address as v_customers_street_address,
customers_suburb as v_customers_suburb,
customers_city as v_customers_city,
customers_postcode as v_customers_postcode,
customers_country as v_customers_country,
customers_telephone as v_customers_telephone,
customers_email_address as v_customers_email_address,
date_purchased as v_date_purchased,
orders_status_name as v_orders_status_name,
products_model as v_products_model,
products_name as v_products_name,
products_options as v_products_options,
products_options_values as v_products_options_values
FROM ".TABLE_ORDERS_PRODUCTS." zop LEFT JOIN ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa
ON(zop.orders_products_id = opa.orders_products_id), ".TABLE_ORDERS." zo, ".TABLE_ORDERS_STATUS." zos
WHERE zo.orders_id = zop.orders_id
AND zos.orders_status_id != '3'
AND zo.orders_status = zos.orders_status_id
		";
		break;
	
	// I will need 2 more cases for 'onlyAttribs' and 'noAttribs' below
	
	case 'noAttribs':
		$iii = 0;
		$filelayout = array(
	//all columns:
'v_date_purchased'    => $iii++, 
'v_orders_status_name'	=> $iii++,
'v_orders_id'     => $iii++, 
'v_customers_id'    => $iii++, 
'v_customers_name'    => $iii++, 
'v_customers_company'    => $iii++, 
'v_customers_street_address'    => $iii++, 
'v_customers_suburb'    => $iii++, 
'v_customers_city'    => $iii++, 
'v_customers_postcode'    => $iii++, 
//'v_customers_state'    => $iii++, 
'v_customers_country'    => $iii++, 
'v_customers_telephone'    => $iii++, 
'v_customers_email_address'    => $iii++, 
'v_products_model'    => $iii++, 
'v_products_name'    => $iii++, 
'v_total_cost' => $iii++,
//'v_products_options'    => $iii++, 
//'v_products_options_values'    => $iii++, 
			);

	$filelayout_sql = "SELECT 
zo.orders_id as v_orders_id,
customers_id as v_customers_id,
customers_name as v_customers_name,
customers_company as v_customers_company,
customers_street_address as v_customers_street_address,
customers_suburb as v_customers_suburb,
customers_city as v_customers_city,
customers_postcode as v_customers_postcode,
customers_country as v_customers_country,
customers_telephone as v_customers_telephone,
customers_email_address as v_customers_email_address,
date_purchased as v_date_purchased,
orders_status_name as v_orders_status_name,
products_model as v_products_model,
products_name as v_products_name,
order_total as v_total_cost
	FROM ".TABLE_ORDERS." zo JOIN ".TABLE_ORDERS_PRODUCTS." zop
	ON(zo.orders_id = zop.orders_id), ".TABLE_ORDERS_STATUS." zos
	WHERE zos.orders_status_id != '3'
	AND zo.orders_status = zos.orders_status_id	 
	";
	break;		
		
	case 'onlyAttribs':
		$iii = 0;
		$filelayout = array(
	//all columns:
'v_date_purchased'    => $iii++, 
'v_orders_status_name'	=> $iii++,
'v_orders_id'     => $iii++, 
'v_customers_id'    => $iii++, 
'v_customers_name'    => $iii++, 
'v_customers_company'    => $iii++, 
'v_customers_street_address'    => $iii++, 
'v_customers_suburb'    => $iii++, 
'v_customers_city'    => $iii++, 
'v_customers_postcode'    => $iii++, 
//'v_customers_state'    => $iii++, 
'v_customers_country'    => $iii++, 
'v_customers_telephone'    => $iii++, 
'v_customers_email_address'    => $iii++, 
'v_products_model'    => $iii++, 
'v_products_name'    => $iii++, 
'v_products_options'    => $iii++, 
'v_products_options_values'    => $iii++, 
			);

	$filelayout_sql = "SELECT 
zo.orders_id as v_orders_id,
customers_id as v_customers_id,
customers_name as v_customers_name,
customers_company as v_customers_company,
customers_street_address as v_customers_street_address,
customers_suburb as v_customers_suburb,
customers_city as v_customers_city,
customers_postcode as v_customers_postcode,
customers_country as v_customers_country,
customers_telephone as v_customers_telephone,
customers_email_address as v_customers_email_address,
date_purchased as v_date_purchased,
orders_status_name as v_orders_status_name,
products_model as v_products_model,
products_name as v_products_name,
products_options as v_products_options,
products_options_values as v_products_options_values
	FROM ".TABLE_ORDERS." zo JOIN ".TABLE_ORDERS_PRODUCTS." zop JOIN ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa
	ON(zo.orders_id = zop.orders_id), ".TABLE_ORDERS_STATUS." zos
	WHERE zop.orders_products_id = opa.orders_products_id
	AND zos.orders_status_id != '3'
	AND zo.orders_status = zos.orders_status_id	
	";
	break;		
	} //eof switch
	$filelayout_count = count($filelayout);
}
// end of if zen_not_null($mat_dltype) - file layout

$mat_dlmethod = isset($_GET['download']) ? $_GET['download'] : $mat_dlmethod;
if ($mat_dlmethod == 'stream' or  $mat_dlmethod == 'tempfile'){
	// DOWNLOAD FILE
	
	// this should hold the csv file
	$filestring = ""; 
	$result = mat_query($filelayout_sql);
	$row =  mysql_fetch_array($result);

	$filelayout_header = $filelayout; // if no mapping was spec'd use the internal field names for header names
		
	//We prepare the table heading with layout values
	foreach( $filelayout_header as $key => $value ){
		$filestring .= $key . $separator;
	}
	// now lop off the trailing tab
	$filestring = substr($filestring, 0, strlen($filestring)-1);

	// default to end of row
	$endofrow = $separator . 'ENDOFROW' . "\n";

	$filestring .= $endofrow;

	while ($row){
		// remove any bad things in the texts that could confuse the Export
		$therow = '';
		foreach( $filelayout as $key => $value ){
			$thetext = $row[$key];
			// kill the carriage returns and tabs in the texts, if any
			$thetext = str_replace("\r",' ',$thetext);
			$thetext = str_replace("\n",' ',$thetext);
			$thetext = str_replace("\t",' ',$thetext);
			// and put the text into the output separated by tabs
			$therow .= $thetext . $separator;
		}

		// lop off the trailing tab, then append the end of row indicator
		$therow = substr($therow,0,strlen($therow)-1) . $endofrow;

		$filestring .= $therow;
		// grab the next row from the db
		$row =  mysql_fetch_array($result);
	}
	//create the file name	
	$EXPORT_TIME = strftime('%Y%b%d-%H%I');
	switch ($mat_dltype) {
		case 'full':
		$EXPORT_TIME = "ordersExpFull" . $EXPORT_TIME;
		break;
		case 'fullb':
		$EXPORT_TIME = "ordersNewFull" . $EXPORT_TIME;
		break;
		case 'noAttribs':
		$EXPORT_TIME = "ordersNoAtt" . $EXPORT_TIME;
		break;
		case 'onlyAttribs':
		$EXPORT_TIME = "ordersAtt" . $EXPORT_TIME;
		break;
	}

	// now either stream it or put it in the oexport directory - welcome to my nightmare
	if ($mat_dlmethod == 'stream'){		
		// STREAM FILE		
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=$EXPORT_TIME.txt");
		// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
		if ($request_type== 'NONSSL'){
			header("Pragma: no-cache");
		} else {
			header("Pragma: ");
		}
		header("Expires: 0");
		echo $filestring;
		die();
	} else { //$mat_dlmethod == 'tempfile'		
		// SAVE FILE IN OEXPORT DIR
		$tmpfpath = DIR_FS_CATALOG . '' . $tempdir . "$EXPORT_TIME.txt";
		//unlink($tmpfpath);
		$fp = fopen( $tmpfpath, "w+");
		fwrite($fp, $filestring);
		fclose($fp);
		$messageStack->add(sprintf(ORDERSEXPORT_MSGSTACK_FILE_EXPORT_SUCCESS, $EXPORT_TIME, $tempdir), 'success');
	} 
	//initial if condition makes sure there is either stream or tempfile
}
// DOWNLOADING ENDS HERE

// mat_stack_sql_error value is redefined in mat_query function
if ($mat_stack_sql_error == true) {
	 $messageStack->add(sprintf(ORDERSEXPORT_MSGSTACK_ERROR_SQL, $mat_sql_errors_msgs), 'caution');
}

// THE HTML PAGE IS BELOW HERE 
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
<!-- body_text //-->
<?php
		echo zen_draw_separator('pixel_trans.gif', '1', '10');
?>
		<table align="center" border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="pageHeading" align="center"><?php echo ORDERSEXPORT_PAGE_HEADING; ?></td>
		</tr>
		</table>
<?php
		echo zen_draw_separator('pixel_trans.gif', '1', '10');
?>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
		<td valign="top">
				
		<table align="center" width="70%" border="0" cellpadding="8" valign="top">
			<tr>
				<td width="100%">
		<b><?php echo ORDERSEXPORT_PAGE_HEADING2; ?></b>
		<br /><br />
		<!-- Download file links -->
		<a href="ordersExport.php?download=stream&dltype=full"><?php echo ORDERSEXPORT_LINK_DOWNLOAD1; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=stream&dltype=fullb"><?php echo ORDERSEXPORT_LINK_DOWNLOAD1B; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=stream&dltype=noAttribs"><?php echo ORDERSEXPORT_LINK_DOWNLOAD2; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=stream&dltype=onlyAttribs"><?php echo ORDERSEXPORT_LINK_DOWNLOAD3; ?></a>
				</td>
			</tr>
		</table>
		
		<table align="center" width="70%" border="0" cellpadding="8" valign="top">
			<tr>
				<td width="100%">
		<b><?php echo ORDERSEXPORT_PAGE_HEADING3; ?></b>
		<br /><br />
		<!-- Download file links -->
		<a href="ordersExport.php?download=tempfile&dltype=full"><?php echo ORDERSEXPORT_LINK_SAVE1; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=tempfile&dltype=fullb"><?php echo ORDERSEXPORT_LINK_SAVE1B; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=tempfile&dltype=noAttribs"><?php echo ORDERSEXPORT_LINK_SAVE2; ?></a>
		<br /><br />
		<a href="ordersExport.php?download=tempfile&dltype=onlyAttribs"><?php echo ORDERSEXPORT_LINK_SAVE3; ?></a>
				</td>
			</tr>
		</table>		
		
		</td>
		</tr>
		</table>
		
		<table><tr><td>
		<?php if((isset($mat_dltype) && zen_not_null($mat_dltype)) OR (isset($mat_dlmethod) && zen_not_null($mat_dlmethod))) {
		echo '&nbsp&nbsp&nbsp&nbsp<a href="' . zen_href_link(FILENAME_ORDERSEXPORT, '', 'NONSSL') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>';		
		} ?>
		<br />
		</td></tr>
		</table>
<!-- body_text_eof //-->
<!-- body_eof //-->
	<br />
<center>
<?php echo ORDERSEXPORT_VERSION . $version; ?>
</center>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>