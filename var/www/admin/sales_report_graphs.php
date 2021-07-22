<?php
/*
  $Id: sales_report_graphs.php,v 0.01 2002/11/27 19:02:22 cwi Exp $
  Released under the GNU General Public License
*/
  require('includes/application_top.php');
  
//define('SALES_REPORT_GRAPHS_FILTER_DEFAULT', '00000000110000000000');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  if ( ($_GET['report']) && (zen_not_null($_GET['report'])) ) {
    $sales_report_view = $_GET['report'];
  }
   
  switch($_GET['report']){
    case('1'):
      $summary1 = 'Average Hourly';
      $summary2 = TODAY_TO_DATE;
      $report_desc = 'Hourly';
      break;     
    case('2'):
      $summary1 = 'Average Daily';
      $summary2 = WEEK_TO_DATE;
      $report_desc = 'Daily';
      break;    
    case('3'):
      $summary1 = 'Average Weekly';
      $summary2 = WEEK_TO_DATE;
      $report_desc = 'Weekly';
      break;      
      
    case('4'):
      $summary1 = 'Average Monthly';
      $summary2 = MONTH_TO_DATE;
      $report_desc = 'Monthly';
      break;
            
    case('5'):
      $summary1 = 'Average Yearly';
      $summary2 = 'Yearly Total';
      $report_desc = 'Yearly';
      break;
           
    default:
      // default view (monthly)
      $sales_report_view = 4;
      // report views (1:hourly, 2:daily, 3:weekly, 4:monthly, 5:yearly)    
      $report = 4;
      $summary1 = 'Average monthly';
      $summary2 = MONTH_TO_DATE;
      $report_desc = 'Monthly';
      break;   
             
  }
       
  // check start and end Date
  $startDate = "";
  if ( ($_GET['startDate']) && (zen_not_null($_GET['startDate'])) ) {
    $startDate = $_GET['startDate'];
  }
  $endDate = "";
  if ( ($_GET['endDate']) && (zen_not_null($_GET['endDate'])) ) {
    $endDate = $_GET['endDate'];
  }
  // check filters
  if (($_GET['filter']) && (zen_not_null($_GET['filter']))) {
    $sales_report_filter = $_GET['filter'];
    $sales_report_filter_link = "&filter=$sales_report_filter";
  }elseif(defined('SALES_REPORT_GRAPHS_FILTER_DEFAULT')){
    $sales_report_filter = SALES_REPORT_GRAPHS_FILTER_DEFAULT;
    $sales_report_filter_link = "&filter=$sales_report_filter";      
  }
  require(DIR_WS_CLASSES . 'sales_report_graphs.php');
  $report = new sales_report($sales_report_view, $startDate, $endDate, $sales_report_filter);
  if (strlen($sales_report_filter) == 0) {
    $sales_report_filter = $report->filter;
    $sales_report_filter_link = "";
  }
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
<script language="javascript" src="includes/javascript/graph.js"></script>
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
<body onload="init()">
<!-- header //-->
<div class="header-area">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
</div>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                        <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
                        <!-- left_navigation //-->
                        <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
                        <!-- left_navigation_eof //-->
                </table>
                </td>
<!-- body_text //-->
                <td width="100%" valign="top">
                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <tr>
                                        <td colspan=2>
                                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                                <td class="pageHeading"><?php echo $report_desc . ' ' . HEADING_TITLE; ?></td>
                                                                <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                                        </tr>
                                                </table>
                                        </td>
                                </tr>
                            <tr>
                                        <td colspan=2>
                                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                                        <tr>
<td align=right class="menuBoxHeading">
<?php
  echo '<a href="' . zen_href_link('sales_report_graphs.php', 'report=1' . $sales_report_filter_link, 'NONSSL') . '">' . 'Hourly' .'</a> | ';
  echo '<a href="' . zen_href_link('sales_report_graphs.php', 'report=2' . $sales_report_filter_link, 'NONSSL') . '">' . 'Daily' .'</a> | ';
  echo '<a href="' . zen_href_link('sales_report_graphs.php', 'report=3' . $sales_report_filter_link, 'NONSSL') . '">' . 'Weekly' . '</a> | ';
  echo '<a href="' . zen_href_link('sales_report_graphs.php', 'report=4' . $sales_report_filter_link, 'NONSSL') . '">' . 'Monthly' . '</a> | ';
  echo '<a href="' . zen_href_link('sales_report_graphs.php', 'report=5' . $sales_report_filter_link, 'NONSSL') . '">' . 'Yearly' . '</a>';
?>
</td>
                                                        </tr>
                                                </table>
                                        </td>
                                </tr>
                                <tr>
                                        <td valign=top width=200 align=center>
<?php 
if ($sales_report_view > 1) {
if ($report->size > 1) {
  echo zen_draw_separator('pixel_trans.gif', 250,10).'<br>';
  $last_value = 0;
  $order_cnt = 0;
  $sum = 0;
  for ($i = 0; $i < $report->size; $i++) {
    if ($last_value != 0) {
      $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;
    } else {
      $percent = "0";
    }
    $sum += $report->info[$i]['sum'];
    $avg += $report->info[$i]['avg'];
    $order_cnt += $report->info[$i]['count'];
    $last_value = $report->info[$i]['sum'];
}
}
//define variables for graph
if ($report->size > 1) {
$scale_x = ($sum / $report->size) / 4;
$scale_y = $scale_x + 50;
$scale_z = $scale_y / 100;
$scale = round($scale_z) * 100;
?>
<SCRIPT LANGUAGE="JavaScript1.2">
var g = new Graph(<?php 
if ($report->size > 2){
echo '200';
} else {
echo ($report->size * 50);} ?>,100,true);
g.addRow(<?php
  for ($i = 0; $i < $report->size; $i++) {
if ($report->info[$i]['sum'] == ""){
        echo '0';
        }else{
        echo $report->info[$i]['sum'] - $report->info[$i]['avg'];
}
          if (($i+1) < $report->size) {
                echo ',';
          }
        }
        echo ');';
        echo '
        ';
?>
<?php  if ($sales_report_view == 2){
echo 'g.addRow(';
  for ($i = 0; $i < $report->size; $i++) {
if ($report->info[$i]['sum'] == ""){
        echo '0';
        }else{
        echo $report->info[$i]['avg'];
}
          if (($i+1) < $report->size) {
                echo ',';
          }
        }
        echo ');';
        echo '
        ';
echo 'g.setLegend("daily total","avg. order");';
        echo '
        ';
}?>
<?php
 echo 'g.setXScaleValues("';
  for ($i = 0; $i < $report->size; $i++) {
 if (($sales_report_view == 5) && ($report->size > 5)) {
 echo substr($report->info[$i]['text'] . $date_text[$i], 0,1);
  }else{
  if ($sales_report_view == 4){
 echo substr($report->info[$i]['text'] . $date_text[$i], 0,3);
  }else{
   if ($report->size > 5) {
 echo substr($report->info[$i]['text'] . $date_text[$i], 3,2);
 } else {
 echo substr($report->info[$i]['text'] . $date_text[$i], 0,5);
 }
}
}
   if (($i+1) < $report->size) {
  echo '","';
   }
 }
 echo '");';
?>
g.scale = <?php echo $scale; ?>;
g.build();
</SCRIPT>
<?php
}
}
?>
                                        </td>
                                <td width=100% valign=top>
                                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                        <tr>
                                                                <td valign="top">
                                                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                                                <tr class="dataTableHeadingRow">
                                                                                        <td class="dataTableHeadingContent"></td>
                                                                                        <td class="dataTableHeadingContent" align=center><?php echo 'Orders'; ?></td>
                                                                                        <td class="dataTableHeadingContent" align=right><?php echo 'Conversion per order'; ?></td>
                                                                                        <td class="dataTableHeadingContent" align=right><?php echo 'Conversion'; ?></td>
                                                                                        <td class="dataTableHeadingContent" align=right><?php echo 'Variance'; ?></td>
                                                                                </tr>
<?php
  $last_value = 0;
  $sum = 0;
  for ($i = 0; $i < $report->size; $i++) {
    if ($last_value != 0) {
      $percent = 100 * $report->info[$i]['sum'] / $last_value - 100;
    } else {
      $percent = "0";
    }
    $sum += $report->info[$i]['sum'];
    $avg += $report->info[$i]['avg'];
    $last_value = $report->info[$i]['sum'];
?>
                                                                                <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
                                                                        <td class="dataTableContent">
<?php
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '<a href="' . zen_href_link('sales_report_graphs.php', $report->info[$i]['link'], 'NONSSL') . '">';
    }
    echo $report->info[$i]['text'] . $date_text[$i];
    if (strlen($report->info[$i]['link']) > 0 ) {
      echo '</a>';
    }
?></td>
                                                                                        <td class="dataTableContent" align=center><?php echo $report->info[$i]['count']?></td>
                                                                                        <td class="dataTableContent"align=right><?php echo $currencies->format($report->info[$i]['avg'])?></td>
                                                                                        <td class="dataTableContent" align=right><?php echo $currencies->format($report->info[$i]['sum'])?></td>
                                                                                        <td class="dataTableContent" align=right>
<?php
    if ($percent == 0){
      echo "---";
    } else {
      echo number_format($percent,0) . "%";
    }
?>
</td>
                                                                                </tr>
<?php
 }
?>
<?php
  if (strlen($report->previous . " " . $report->next) > 1) {
?>
                                                                                <tr>
                                                                                        <td width=100% colspan=5>
                                                                                                <table width=100%>
                                                                                                        <tr>
                                                                                                                <td align=left>
<?php
    if (strlen($report->previous) > 0) {
      echo '<a href="' . zen_href_link('sales_report_graphs.php', $report->previous, 'NONSSL') . '">&lt;&lt;&nbsp;Previous</a>';
    }
?>
                                                                                                                </td>
                                                                                                <td align=right>
<?php
    if (strlen($report->next) > 0) {
      echo '<a href="' . zen_href_link('sales_report_graphs.php', $report->next, 'NONSSL') . '">Next&nbsp;&gt;&gt;</a>';
      echo "";
    }
?>
                                                                                                                </td>
                                                                                                        </tr>
                                                                                                </table>
                                                                                        </td>
                                                                                </tr>
<?php
  }
?>
                  </table>
                  <p>
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php if ($order_cnt != 0){
?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. AVERAGE_ORDER . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum / $order_cnt) ?></td>
                    </tr>
<?php } 
  if ($report->size != 0) {
?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary1 . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum / $report->size) ?></td>
                    </tr>
<?php } ?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" width=100% align=right><?php echo '<b>'. $summary2 . ' </b>' ?></td>
                      <td class="dataTableContent" align=right><?php echo $currencies->format($sum) ?></td>
                    </tr>
                  </table>
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr class="dataTableRow">
                      <td class="dataTableContent" align="left"><?php echo FILTER_STATUS  ?></td>
                      <td class="dataTableContent" align="right"><?php echo FILTER_VALUE ?></td>
                    </tr>
<?php
  if (($sales_report_filter) == 0) {
    for ($i = 0; $i < $report->status_available_size; $i++) {
      $sales_report_filter .= "0";
    }
  }
  for ($i = 0; $i < $report->status_available_size; $i++) {
?>
                    <tr>
                      <td class="dataTableContent" align="left"><?php echo $report->status_available[$i]['value'] ?></a></td>
<?php
    if (substr($sales_report_filter,$i,1) ==  "0") {
      $tmp = substr($sales_report_filter, 0, $i) . "1" . substr($sales_report_filter, $i+1, $report->status_available_size - ($i + 1));
      $tmp = zen_href_link('sales_report_graphs.php', $report->filter_link . "&filter=". $tmp, 'NONSSL');
?>
      <td class="dataTableContent" width="100%" align="right"><?php echo zen_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) ?>&nbsp;<a href="<?php echo $tmp; ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) ?></a></td>
<?php
    } else {
      $tmp = substr($sales_report_filter, 0, $i) . "0" . substr($sales_report_filter, $i+1);
      $tmp = zen_href_link('sales_report_graphs.php', $report->filter_link . "&filter=". $tmp, 'NONSSL');
?>
      <td class="dataTableContent" width="100%" align="right"><a href="<?php echo $tmp; ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) ?></a>&nbsp;<?php echo zen_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) ?></td>
<?php
    }
?>
                    </tr>
<?php
  }
?>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
