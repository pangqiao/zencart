<?php
/*
  $Id: stats_monthly_sales.php, v 1.4 2011/11/24  $

  Copyright 2003-2005 Zen Cart Development Team
  Portions Copyright 2004 osCommerce
  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2004 osCommerce
  Released under the GNU General Public License

  Orginal OSC contributed by Fritz Clapp <fritz@sonnybarger.com>

  Ported to ZenCart 1.50.RC1/2 SkipWater <skip@ccssinc.net> 11.24.2011
  Ported to ZenCart 1.3.8a SkipWater <skip@ccssinc.net> 06.23.08

	This report displays a summary of monthly or daily totals:
	gross income (order totals)
	subtotals of all orders in the selected period
	nontaxed sales subtotals
	taxed sales subtotals
	tax collected
	shipping/handling charges
	low order fees (if present)
	gift vouchers (or other addl order total component, if present)

The data comes from the orders and orders_total tables.

Data is reported as of order purchase date.

If an order status is chosen, the report summarizes orders with that status.

The capability to "drill down" on any month to report the daily summary for that month.  

Report rows are initially shown in newest to oldest, top to bottom, 
but this order may be inverted by clicking the "Invert" control button.

A popup display that lists the various types (and their
subtotals) comprising the tax values in the report rows.

Columns that summarize nontaxed and taxed order subtotals.
The taxed column summarizes subtotals for orders in which tax was charged.
The nontaxed column is the subtotal for the row less the taxed column value.

used class="pageHeading"
	 class="smallText"
	 class="dataTableRow"
 	 class="dataTableHeadingRow"
 	 class="dataTableHeadingContent"

A popup help display window on how to use.

*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

// Function used to format report
  function mirror_out ($field) {
	global $csv_accum;
	echo $field;
	$field = strip_tags($field);
	$field = str_replace (",","",$field);
	if ($csv_accum=='') $csv_accum=$field; 
	else 
	{if (strrpos($csv_accum,chr(10)) == (strlen($csv_accum)-1)) $csv_accum .= $field;
		else $csv_accum .= "," . $field; }  // ;
	return;
}

// entry for help popup window
if (isset($_GET['help'])){ 
  echo TEXT_HELP;
  exit;
}

if (isset($_POST['help'])){ 
  echo TEXT_HELP;
  exit;
}

if($_GET['status'] != ''){
    $get_status = zen_db_prepare_input($_GET['status']);
}
if($_GET['print'] != ''){
    $get_print = zen_db_prepare_input($_GET['print']);
}
if($_GET['invert'] != ''){
    $get_invert = zen_db_prepare_input($_GET['invert']);
}

if($_POST['status'] != ''){
    $get_status = zen_db_prepare_input($_POST['status']);
}
if($_POST['print'] != ''){
    $get_print = zen_db_prepare_input($_POST['print']);
}
if($_POST['invert'] != ''){
    $get_invert = zen_db_prepare_input($_POST['invert']);
}

// entry for bouncing csv string back as file
if (isset($_POST['csv'])) {
if ($_POST['saveas']) {  // rebound posted csv as save file
		$savename= $_POST['saveas'] . ".csv";
		}
		else $savename='unknown.csv';
$csv_string = '';
if ($_POST['csv']) $csv_string=$_POST['csv'];
  if (strlen($csv_string)>0){
  header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
  header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: Application/octet-stream");
  header("Content-Disposition: attachment; filename=$savename");
  echo $csv_string;
  }
  else echo "CSV string empty";
exit;
}

    //Zone Only SQL appendage
    $zone_only_sql = '';
    $zone_selected = false;
    if ($_POST['zone_only'] != '' && !in_array('all', $_POST['zone_only'])) {
        $get_zone_only = $_POST['zone_only'];
        $zone_selected = true;
        $zoned_sql = '';
        foreach ($get_zone_only as $selectedZone) {
            $zoned_sql .= "o.delivery_state LIKE '" . $selectedZone . "' OR ";
        }
        $zoned_sql = rtrim($zoned_sql, "OR ");
        $zone_only_sql = " AND (" . $zoned_sql . ") ";
    }
    
// entry for popup display of tax detail
// show=ot_tax 
if (isset($_GET['show']) || isset($_POST['show'])) {
	$ot_type = zen_db_prepare_input($_GET['show']);
	$sel_month = zen_db_prepare_input($_GET['month']);
	$sel_year = zen_db_prepare_input($_GET['year']);
	$sel_day = 0;
	
	if (isset($_GET['day'])) $sel_day = $_GET['day'];
        
        $ot_type = zen_db_prepare_input($_POST['show']);
	$sel_month = zen_db_prepare_input($_POST['month']);
	$sel_year = zen_db_prepare_input($_POST['year']);
	$sel_day = 0;
	
	if (isset($_POST['day'])) $sel_day = $_POST['day'];
	$status = '';
	
	if ($get_status) $status = $get_status;
	// construct query for selected detail
	$detail_query_raw = "SELECT ot.value amount, ot.title description, ot.orders_id ordernumber from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) WHERE ";
	
	if ($status<>'') $detail_query_raw .= "o.orders_status ='" . $status . "' AND ";
	$detail_query_raw .= "ot.class = '" . $ot_type . "' AND month(o.date_purchased)= '" . $sel_month . "' AND year(o.date_purchased)= '" . $sel_year . "'";
	$detail_query_raw .= $zone_only_sql;
	if ($sel_day<>0) $detail_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sel_day . "'";
	// $detail_query_raw .= " group by ot.title";

	$detail_query = $db->Execute($detail_query_raw);

	echo "<!doctype html public \"-//W3C//DTD HTML 4.01 Transitional//EN\"><html " . HTML_PARAMS . "><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . CHARSET ;?> "> <?php echo "<title>" . TEXT_DETAIL . "</title><link rel=\"stylesheet\" type=\"text/css\" href=\"includes/stylesheet.css\"></head><body><br><table width=\"80%\" align=center><caption align=center>";

	if ($sel_day<>0) echo $sel_day . " Day - " ;
	echo $sel_year . " Year - " . $sel_month . " Month";

	if ($status<>'') echo "<br>" . HEADING_TITLE_STATUS . ":" . "&nbsp;" . $status;
	echo "</caption>";
 	$detail_line = $detail_query;
	while (!$detail_line->EOF) {
	echo "<tr class=dataTableRow><td align=left width='45%'>Order #: " . $detail_line->fields['ordernumber'] . "</td><td align=right>" . $detail_line->fields['description'] . "&nbsp;</td><td align=right>" . number_format($detail_line->fields['amount'],3) . "</td></tr>";
        $detail_line->MoveNext();
        }
	echo "</table></body>";
exit;
}

    //Array of States
    $state_list = $db->Execute("SELECT DISTINCT delivery_state FROM " . TABLE_ORDERS . " ORDER BY delivery_state");
    $state_array[] = array('id' => 'all',
        'text' => 'all');
    while (!$state_list->EOF) {
        if ($state_list->fields['delivery_state'] != '') {
            $state_array[] = array('id' => $state_list->fields['delivery_state'],
                'text' => $state_list->fields['delivery_state']);
        }
        $state_list->MoveNext();
    }
//
// main entry for report display
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" media="print" href="includes/stylesheet_print.css">
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
<style>
  .dataTableContent  input[type="submit"]
{
    background:none; 
    border-width:0px; 
    color:blue;
  background: transparent;
  cursor: pointer;
}
</style>
</head>
<body onload="init()">
<?php
// set printer-friendly toggle
(zen_db_prepare_input($get_print=='yes')) ? $print=true : $print=false;
// set inversion toggle
(zen_db_prepare_input($get_invert=='yes')) ? $invert=true : $invert=false;
?>
<!-- header //-->
<?php
 if(!$print) require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>

<!-- body_text //-->
    <td width="100%" valign="top">
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php if ($print) {
	echo "<tr><td class=\"pageHeading\">" . STORE_NAME ."</td></tr>";
	}
?>
		  <tr>
            <td class="pageHeading">
			<?php echo HEADING_TITLE; ?></td>
<?php 
// detect whether this is monthly detail request
$sel_month = 0;
	if ($_GET['month'] && $_GET['year']) {
	$sel_month = zen_db_prepare_input($_GET['month']);
	$sel_year = zen_db_prepare_input($_GET['year']);
	}
        if ($_POST['month'] && $_POST['year']) {
	$sel_month = zen_db_prepare_input($_POST['month']);
	$sel_year = zen_db_prepare_input($_POST['year']);
	}

// get list of orders_status names for dropdown selection
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status = $db->Execute("select orders_status_id, orders_status_name
                                 from " . TABLE_ORDERS_STATUS . "
                                 where language_id = '" . (int)$_SESSION['languages_id'] . "'");
  while (!$orders_status->EOF) {
    $orders_statuses[] = array('id' => $orders_status->fields['orders_status_id'],
                               'text' => $orders_status->fields['orders_status_name'] . ' [' . $orders_status->fields['orders_status_id'] . ']');
    $orders_status_array[$orders_status->fields['orders_status_id']] = $orders_status->fields['orders_status_name'];
    $orders_status->MoveNext();
  }
// name of status selection
$orders_status_text = TEXT_ALL_ORDERS;
if ($get_status) {
  $status = zen_db_prepare_input($get_status);
  $orders_status_query = $db->Execute("SELECT orders_status_name from " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . $languages_id . "' AND orders_status_id =" . $status.$zone_only_sql);
 $orders_status = $orders_status_query;
  while (!$orders_status->EOF) {
	  $orders_status_text = $orders_status->fields['orders_status_name'];
          
  }
				}
if (!$print) { ?>
			<td align="right">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
			  <tr><td class="smallText" align="right">
				<?php echo zen_draw_form('status', FILENAME_STATS_MONTHLY_SALES, '', 'POST');
			 	// get list of orders_status names for dropdown selection
				    $orders_statuses = array();
  $orders_status_array = array();
  $orders_status = $db->Execute("select orders_status_id, orders_status_name
                                 from " . TABLE_ORDERS_STATUS . "
                                 where language_id = '" . (int)$_SESSION['languages_id'] . "'");
  while (!$orders_status->EOF) {
    $orders_statuses[] = array('id' => $orders_status->fields['orders_status_id'],
                               'text' => $orders_status->fields['orders_status_name'] . ' [' . $orders_status->fields['orders_status_id'] . ']');
    $orders_status_array[$orders_status->fields['orders_status_id']] = $orders_status->fields['orders_status_name'];
    $orders_status->MoveNext();
  }
                echo HEADING_TITLE_STATUS . ': ' . zen_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), '', 'onChange="this.form.submit();"'); ?>
				<input type="hidden" name="selected_box" value="reports">
				<?php
					if ($sel_month<>0) 
					echo "<input type='hidden' name='month' value='" . $sel_month . "'><input type='hidden' name='year' value='" . $sel_year . "'>";
					if ($invert) echo "<input type='hidden' name='invert' value='yes'>";
			//fix bug :Alabama;
			//QiaoWei	echo '<select name="zone_only[]" multiple="multiple" size="7">';
    foreach ($state_array as $state) {
        $selected_multi_zone = '';
        if(isset($_POST['zone_only'])){
            if ((in_array('all', $_POST['zone_only']) || $zone_selected == false) && $state['text'] == 'all') {
                $selected_multi_zone = 'selected="selected"';
            }
            if (in_array($state['text'], $_POST['zone_only']) || $state['text'] == $_POST['zone_only']) {
                $selected_multi_zone = 'selected="selected"';
            }
        }
	//fix bug :Alabama;
     	//QiaoWei   echo '<option value="' . $state['text'] . '" ' . $selected_multi_zone . ' >' . $state['text'] . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" value="Filter">';
    echo '</form>';
                                        ?>
                                
				</td>
              </tr>
             </table>
			 </td>
<?php		} 
?>

<?php if ($print) { ?>
			<td>
			</td>
		<tr><td>
				<table>
				<tr><td class="smallText"><?php echo HEADING_TITLE_REPORTED . ": "; ?></td>
				<td width="8"></td>
				<td class="smallText" align="left"><?php echo date(ltrim(TEXT_REPORT_DATE_FORMAT)); ?></td>
				</tr>
				<tr><td class="smallText" align="left">
				<?php echo HEADING_TITLE_STATUS . ": ";  ?></td>
				<td width="8"></td>
				<td class="smallText" align="left">
				<?php echo $orders_status_text;?>
				</td>
				</tr>
				<table>
			</td><td></td>
		</tr>
<?php 	}	 
?>
        </table></td>
      </tr>
<?php if(!$print) { ?>
<!--
row for buttons to print, save, and help
-->
			<tr>
				<td  align="right">
				<table align=right cellspacing="10"><tr>
				<td align="left" class="smallText">
				<?php  // back button if monthly detail
				if ($sel_month<>0)	 {
				echo "<a href='" . $_SERVER['PHP_SELF'] . "?&selected_box=reports";
				if (isset($get_status)) echo "&status=" . $status;
				if (isset($get_invert)) echo "&invert=yes";
				echo "' title='" . TEXT_BUTTON_REPORT_BACK_DESC ;?> "> <?php echo TEXT_BUTTON_REPORT_BACK . "</a>";
				}
				?>
				</td>
				<td class="smallText"><a href="<?php  
				echo $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "&print=yes";
				?>" target="print" title="<?php echo TEXT_BUTTON_REPORT_PRINT_DESC ;?> "> <?php echo TEXT_BUTTON_REPORT_PRINT; ?></a>
				</td>
				<td class="smallText"><a href="<?php echo $_SERVER['PHP_SELF'] . "?" . str_replace('&invert=yes','',$_SERVER['QUERY_STRING']);
				if (!$invert) echo "&invert=yes";?>
				" title=" <?php echo TEXT_BUTTON_REPORT_INVERT_DESC ;?> "> <?php echo TEXT_BUTTON_REPORT_INVERT; ?></a>
				</td>
				<td class="smallText"><a href="#" onClick="window.open('<?php  
				echo $_SERVER['PHP_SELF'] . "?&help=yes";	?>','help',config='height=400,width=600,scrollbars=1, resizable=1')" title="<?php echo TEXT_BUTTON_REPORT_HELP_DESC ;?> "> <?php echo TEXT_BUTTON_REPORT_HELP; ?></a>
				</td>
				</tr></table>
				</td>
			</tr>
<?php	}	
//
// determine if loworder fee is enabled in configuration, include/omit the column
$loworder_query_raw = "SELECT configuration_value from " . TABLE_CONFIGURATION . " WHERE configuration_key =" . "'MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE'";
$loworder = false;
$loworder_query = $db->Execute($loworder_query_raw);
if ($loworder_query->RecordCount()>0) {
	$low_setting=$loworder_query;
	if ($low_setting->fields['configuration_value']=='true') $loworder=true;
}
//
// if there are extended class values in orders_table
// create extra column so totals are comprehensively correct
$class_val_subtotal = "'ot_subtotal'";
$class_val_tax = "'ot_tax'";
$class_val_shiphndl = "'ot_shipping'";
$class_val_loworder = "'ot_loworderfee'";
$class_val_total = "'ot_total'";
	$extra_class_query_raw = "SELECT value from " . TABLE_ORDERS_TOTAL . " WHERE class <> " . $class_val_subtotal . " AND class <>" . $class_val_tax . " AND class <>" . $class_val_shiphndl . " AND class <>" . $class_val_loworder . " AND class <>" . $class_val_total;
	$extra_class = false;
	$extra_class_query = $db->Execute($extra_class_query_raw);
	if ($extra_class_query->RecordCount()>0) $extra_class = true;
// start accumulator for the report content mirrored in CSV
$csv_accum = '';
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top">
			<table border="0" width='100%' cellspacing="0" cellpadding="2">
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" width='45' align='left' valign="bottom"><?php 
if ($sel_month == 0) mirror_out(TABLE_HEADING_MONTH); else mirror_out(TABLE_HEADING_MONTH); ?>
</td>
<td class="dataTableHeadingContent" width='35' align='left' valign="bottom"><?php 
if ($sel_month == 0) mirror_out(TABLE_HEADING_YEAR); else mirror_out(TABLE_HEADING_DAY); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_INCOME); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_SALES); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_NONTAXED); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_TAXED); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_TAX_COLL); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_SHIPHNDL); ?></td>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_SHIP_TAX); ?></td>
<?php 
if ($loworder) { ?>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_LOWORDER); ?></td>
<?php }
?>
<?php
if ($extra_class) { ?>
<td class="dataTableHeadingContent" width='70' align='right' valign="bottom"><?php mirror_out(TABLE_HEADING_OTHER); ?></td>
<?php }
?>
</tr>
<?php 
// clear footer totals
	$footer_gross = 0;
	$footer_sales = 0;
	$footer_sales_nontaxed = 0;
	$footer_sales_taxed = 0;
	$footer_tax_coll = 0;
	$footer_shiphndl = 0;
	$footer_shipping_tax = 0;
	$footer_loworder = 0;
	$footer_other = 0;
// new line for CSV
$csv_accum .= "\n";
// order totals, the driving force 
$status = '';
$sales_query_raw = "SELECT sum(ot.value) gross_sales, monthname(o.date_purchased) row_month, year(o.date_purchased) row_year, month(o.date_purchased) i_month, dayofmonth(o.date_purchased) row_day  from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) WHERE ";
if ($get_status) {
  $status = zen_db_prepare_input($get_status);
  $sales_query_raw .= "o.orders_status =" . $status . " AND ";
	}
$sales_query_raw .= "ot.class = " . $class_val_total;
$sales_query_raw .= $zone_only_sql;
if ($sel_month<>0) $sales_query_raw .= " AND month(o.date_purchased) = " . $sel_month;
$sales_query_raw .= " group by year(o.date_purchased), month(o.date_purchased)";
if ($sel_month<>0) $sales_query_raw .= ", dayofmonth(o.date_purchased)";
$sales_query_raw .=  " order by o.date_purchased ";
if ($invert) $sales_query_raw .= "asc"; else $sales_query_raw .= "desc";
$sales_query = $db->Execute($sales_query_raw);
$num_rows = $sales_query->RecordCount();
if ($num_rows==0) echo '<tr><td class="smalltext">' . TEXT_NOTHING_FOUND . '</td></tr>';
$rows=0;
//
// loop here for each row reported
$sales = $sales_query;
while (!$sales->EOF) {
	$rows++;
	if ($rows>1 && $sales->fields['row_year']<>$last_row_year) {  // emit annual footer
?>
<tr class="dataTableHeadingRow">
<td class="dataTableHeadingContent" align="left">
<?php 
	if ($sales->fields['row_year']==date("Y")) mirror_out(TABLE_FOOTER_YTD); 
	else 
		if ($sel_month==0) mirror_out(TABLE_FOOTER_YEAR);
		else
			mirror_out(strtoupper(substr($sales->fields['row_month'],0,3)));
?>
</td>
<td class="dataTableHeadingContent" align="left">
<?php mirror_out($last_row_year); ?></td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_gross,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_sales,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_sales_nontaxed,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_sales_taxed,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_tax_coll,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_shiphndl,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format(($footer_shipping_tax <= 0) ? 0 : $footer_shipping_tax,2)); ?>
</td>
<?php if ($loworder) { ?>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_loworder,2)); ?>
</td>
<?php }
?>
<?php if ($extra_class) { ?>
<td class="dataTableHeadingContent" width='70' align="right">
<?php mirror_out(number_format($footer_other,2)); ?>
</td>
<?php }
// clear footer totals
$footer_gross = 0;
$footer_sales = 0;
$footer_sales_nontaxed = 0;
$footer_sales_taxed = 0;
$footer_tax_coll = 0;
$footer_shiphndl = 0;
$footer_shipping_tax = 0;
$footer_loworder = 0;
$footer_other = 0;
// new line for CSV
$csv_accum .= "\n";
?>
</tr>
<?php }
//
// determine net sales for row

// Retrieve totals for products that are zero VAT rated
$net_sales_query_raw = "SELECT sum( op.final_price * op.products_quantity ) net_sales FROM " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_PRODUCTS . " op ON ( o.orders_id = op.orders_id ) WHERE op.products_tax =0 AND";

if ($status<>'') $net_sales_query_raw .= " o.orders_status ='" . $status . "' AND ";

$net_sales_query_raw .= " o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;

if ($sel_month<>0) $net_sales_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";

$net_sales_query = $db->Execute($net_sales_query_raw);
$net_sales_this_row = 0;
if ($net_sales_query->RecordCount() > 0)
	$zero_rated_sales_this_row = $net_sales_query;

// Retrieve totals for products that are NOT zero VAT rated
$net_sales_query_raw = "SELECT sum(op.final_price * op.products_quantity) net_sales, sum(op.final_price * op.products_quantity * (1 + (op.products_tax / 100.0))) gross_sales, sum((op.final_price * op.products_quantity * (1 + (op.products_tax / 100.0))) - (op.final_price * op.products_quantity)) tax FROM " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_PRODUCTS . " op ON (o.orders_id = op.orders_id) WHERE op.products_tax <> 0 AND ";
if ($status<>'') $net_sales_query_raw .= "o.orders_status ='" . $status . "' AND ";
$net_sales_query_raw .= " o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;
if ($sel_month<>0) $net_sales_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";

$net_sales_query = $db->Execute($net_sales_query_raw);
$net_sales_this_row = 0;
if ($net_sales_query->RecordCount() > 0)
	$net_sales_this_row = $net_sales_query;

// Total tax. This is needed so we can calculate any tax that has been added to the postage
$tax_coll_query_raw = "SELECT sum(ot.value) tax_coll FROM " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id) WHERE ";
if ($status<>'') $tax_coll_query_raw .= "o.orders_status ='" . $status . "' AND ";
$tax_coll_query_raw .= "ot.class = " . $class_val_tax . " AND o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;
if ($sel_month<>0) $tax_coll_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";
$tax_coll_query = $db->Execute($tax_coll_query_raw);
$tax_this_row = 0;
if ($tax_coll_query->RecordCount()>0)	
	$tax_this_row = $tax_coll_query;

// shipping AND handling charges for row
$shiphndl_query_raw = "SELECT sum(ot.value) shiphndl from " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id) WHERE ";
if ($status<>'') $shiphndl_query_raw .= "o.orders_status ='" . $status . "' AND ";
$shiphndl_query_raw .= "ot.class = " . $class_val_shiphndl . " AND o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;
if ($sel_month<>0) $shiphndl_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";
$shiphndl_query = $db->Execute($shiphndl_query_raw);
$shiphndl_this_row = 0;
if ($shiphndl_query->RecordCount()>0)	
	$shiphndl_this_row = $shiphndl_query;

// low order fees for row
$loworder_this_row = 0;
if ($loworder) {
	$loworder_query_raw = "SELECT sum(ot.value) loworder from " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id) WHERE ";
	if ($status<>'') $loworder_query_raw .= "o.orders_status ='" . $status . "' AND ";
	$loworder_query_raw .= "ot.class = " . $class_val_loworder . " AND o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;
	if ($sel_month<>0) $loworder_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";
	$loworder_query = $db->Exccute($loworder_query_raw);
	if ($loworder_query->RecordCount()>0)	
	$loworder_this_row = $loworder_query;
}

// additional column if extra class value in orders_total table
$other_this_row = 0;
if ($extra_class) { 
	$other_query_raw = "SELECT sum(ot.value) other from " . TABLE_ORDERS . " o INNER JOIN " . TABLE_ORDERS_TOTAL . " ot ON (o.orders_id = ot.orders_id) WHERE ";
	if ($status<>'') $other_query_raw .= "o.orders_status ='" . $status . "' AND ";
	$other_query_raw .= "ot.class <> " . $class_val_subtotal . " AND class <> " . $class_val_tax . " AND class <> " . $class_val_shiphndl . " AND class <> " . $class_val_loworder . " AND class <> " . $class_val_total . " AND o.date_purchased BETWEEN '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-01' AND '" . $sales->fields['row_year'] . "-" . $sales->fields['i_month'] . "-31 23:59'".$zone_only_sql;
	if ($sel_month<>0) $other_query_raw .= " AND dayofmonth(o.date_purchased) = '" . $sales->fields['row_day'] . "'";
	$other_query = $db->Execute($other_query_raw);
	if ($other_query->RecordCount()>0)	
	$other_this_row = $other_query;
	}

// Correct any rounding errors
	$net_sales_this_row->fields['net_sales'] = (floor(($net_sales_this_row->fields['net_sales'] * 100) + 0.5)) / 100;
	$net_sales_this_row->fields['tax'] = (floor(($net_sales_this_row->fields['tax'] * 100) + 0.5)) / 100;
	$zero_rated_sales_this_row->fields['net_sales'] = (floor(($zero_rated_sales_this_row->fields['net_sales'] * 100) + 0.5)) / 100;
	$tax_this_row->fields['tax_coll'] = (floor(($tax_this_row->fields['tax_coll'] * 100) + 0.5)) / 100;

// accumulate row results in footer
	$footer_gross += $sales->fields['gross_sales']; // Gross Income
	$footer_sales += $net_sales_this_row->fields['net_sales'] + $zero_rated_sales_this_row->fields['net_sales']; // Product Sales
	$footer_sales_nontaxed += $zero_rated_sales_this_row->fields['net_sales']; // Nontaxed Sales
	$footer_sales_taxed += $net_sales_this_row->fields['net_sales']; // Taxed Sales
	$footer_tax_coll += $net_sales_this_row->fields['tax']; // Taxes Collected
	$footer_shiphndl += $shiphndl_this_row->fields['shiphndl']; // Shipping & handling
        $footer_shipping_tax += ($tax_this_row->fields['tax_coll'] - $net_sales_this_row->fields['tax']); // Shipping Tax
	$footer_loworder += $loworder_this_row->fields['loworder'];
	if ($extra_class) $footer_other += $other_this_row->fields['other'];
?>
<tr class="dataTableRow">
<td class="dataTableContent" align="left">
	<?php  // live link to report monthly detail
		if ($sel_month == 0	&& !$print) {
                    echo zen_draw_form('month', FILENAME_STATS_MONTHLY_SALES, '', 'POST');
                    foreach($_POST as $field=>$post){
                            if($field != 'zone_only'){
                            echo zen_draw_hidden_field($field, $post);
                            }
                        }
                        if(is_array($_POST['zone_only'])){
                            foreach($_POST['zone_only'] as $zones){
                                echo zen_draw_hidden_field('zone_only[]', $zones);
                            }
                        }
                        elseif(isset($_POST['zone_only'])){
                            echo zen_draw_hidden_field('zone_only[]', $_POST['zone_only']);
                        }
                    echo zen_draw_hidden_field('month', $sales->fields['i_month']);
                    echo zen_draw_hidden_field('year', $sales->fields['row_year']);
                    echo '<input type="submit" value="'.$sales->fields['row_month'].'">';
                    echo '<span style="display:none;">';
                    mirror_out($sales->fields['row_month']);
                    echo '</span>';
                    echo '</form>';
		}
	?>
</td>
<td class="dataTableContent" align="left">
	<?php 
		if ($sel_month==0) mirror_out($sales->fields['row_year']);
			else mirror_out($sales->fields['row_day']);
		$last_row_year = $sales->fields['row_year']; // save this row's year to check for annual footer
	?>
</td>
<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($sales->fields['gross_sales'],2)); ?></td>
<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($net_sales_this_row->fields['net_sales'] + $zero_rated_sales_this_row->fields['net_sales'],2)); ?></td>
<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($zero_rated_sales_this_row->fields['net_sales'],2)); ?></td>
<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($net_sales_this_row->fields['net_sales'],2)); ?></td>
<td class="dataTableContent" width='70' align="right">
	<?php 
		// make this a link to the detail popup if nonzero
		if (!$print && ($net_sales_this_row->fields['tax']>0)) {
                        echo zen_draw_form('day', FILENAME_STATS_MONTHLY_SALES, " ", 'POST','target="_blank"');
                        foreach($_POST as $field=>$post){
                            if($field != 'zone_only'){
                            echo zen_draw_hidden_field($field, $post);
                            }
                        }
                        if(is_array($_POST['zone_only'])){
                            foreach($_POST['zone_only'] as $zones){
                                echo zen_draw_hidden_field('zone_only[]', $zones);
                            }
                        }
                        elseif(isset($_POST['zone_only'])){
                            echo zen_draw_hidden_field('zone_only[]', $_POST['zone_only']);
                        }
                        echo zen_draw_hidden_field('month', $sales->fields['i_month']);
                        echo zen_draw_hidden_field('year', $sales->fields['row_year']);
                        echo zen_draw_hidden_field('show', 'ot_tax');
                        echo zen_draw_hidden_field('day', $sales->fields['row_day']);
                        echo '<input type="submit" value="'.number_format($net_sales_this_row->fields['tax'],2).'">';
                        echo '<span style="display:none;">';
                        mirror_out(number_format($net_sales_this_row->fields['tax'],2));
                        echo '</span>';
                        echo '</form>';
			
		}
	?>
</td>
<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($shiphndl_this_row->fields['shiphndl'],2)); ?></td>
<td class="dataTableContent" width='70' align="right"><?php $sh_tax = $tax_this_row->fields['tax_coll'] - $net_sales_this_row->fields['tax']; mirror_out(number_format(($sh_tax <= 0) ? 0 : $sh_tax,2)); ?></td>
	<?php if ($loworder) { ?>
		<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($loworder_this_row->fields['loworder'],2)); ?></td>
	<?php } 
	?>
	<?php
	if ($extra_class) { ?>
		<td class="dataTableContent" width='70' align="right"><?php mirror_out(number_format($other_this_row->fields['other'],2)); ?></td>
	<?php }
	?>
</tr>
	<?php 
		// new line for CSV
		$csv_accum .= "\n";

		// output footer below ending row
	if ($rows==$num_rows){
		?>
		<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent" align="left">
	<?php 
		if ($sel_month<>0) 
			mirror_out(strtoupper(substr($sales->fields['row_month'],0,3)));
		else
			{if ($sales->fields['row_year']==date("Y")) mirror_out(TABLE_FOOTER_YTD); 
	 			else mirror_out(TABLE_FOOTER_YEAR);}
	?>
</td>
<td class="dataTableHeadingContent" align="left">
	<?php mirror_out($sales->fields['row_year']); ?></td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_gross,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_sales,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_sales_nontaxed,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_sales_taxed,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_tax_coll,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_shiphndl,2)); ?>
</td>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format(($footer_shipping_tax <= 0) ? 0 : $footer_shipping_tax,2)); ?>
</td>
	<?php if ($loworder) { ?>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_loworder,2)); ?>
</td>
	<?php }
	?>
	<?php if ($extra_class) { ?>
<td class="dataTableHeadingContent" width='70' align="right">
	<?php mirror_out(number_format($footer_other,2)); ?>
</td>
	<?php }

// clear footer totals
$footer_gross = 0;
$footer_sales = 0;
$footer_sales_nontaxed = 0;
$footer_sales_taxed = 0;
$footer_tax_coll = 0;
$footer_shiphndl = 0;
$footer_shipping_tax = 0;
$footer_loworder = 0;
$footer_other = 0;
// new line for CSV
$csv_accum .= "\n";
?>
</tr>
<?php 
	}
      $sales->MoveNext();  
   }  

// done with report body
// button for Save CSV
if ($num_rows>0 && !$print) {
?>
<tr>
				<td class="smallText" colspan="4"><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method=post><input type='hidden' name='csv' value='<?php echo $csv_accum; ?>'><input type='hidden' name='saveas' value='sales_report_<?php
					//suggested file name for csv, include year and month if detail
					//include status if selected, end with date and time of report
				if ($sel_month<10) $sel_month_2 = "0" . $sel_month; 
				else $sel_month_2 = $sel_month;
				if ($sel_month<>0) echo $sel_year . $sel_month_2 . "_";
				if (strpos($orders_status_text,' ')) echo substr($orders_status_text, 0, strpos($orders_status_text,' ')) . "_" . date("YmdHi"); else echo $orders_status_text . "_" . date("YmdHi"); 
				?>'><input type="submit" value="<?php echo TEXT_BUTTON_REPORT_SAVE ;?>"></form>
				</td>
</tr>
<?php }
// end button for Save CSV 
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>

<?php
  	// suppress footer for printer-friendly version
	if(!$print) require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
