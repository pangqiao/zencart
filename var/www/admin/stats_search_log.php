<?php
   // Search Log v2.2
   // Written By C.J.Pinder (c) 2007
   // Portions Copyright 2003-2007 Zen Cart Development Team
   // Portions Copyright 2003 osCommerce
   //
   // This source file is subject to version 2.0 of the GPL license, 
   // that is bundled with this package in the file LICENSE, and is
   // available through the world-wide-web at the following url:
   // http://www.zen-cart.com/license/2_0.txt
   // If you did not receive a copy of the zen-cart license and are unable
   // to obtain it through the world-wide-web, please send a note to
   // license@zen-cart.com so we can mail you a copy immediately.    
   
   require('includes/application_top.php');
   
   function export_search_log()
   {
   $file = urlencode('searchlog') . ".csv";      
   	  if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))//stolen from admin_activity.php
   	  {
   		header('Content-Type: application/octetstream');
   //              header('Content-Type: '.$content_type);
   //              header('Content-Disposition: inline; filename="' . $file . '"');
   		header('Content-Disposition: attachment; filename=' . $file);
   		header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
   		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   		header("Cache-Control: must_revalidate, post-check=0, pre-check=0");
   		header("Pragma: public");
   		header("Cache-control: private");
   	  } else
   	  {
   		header('Content-Type: application/x-octet-stream');
   //              header('Content-Type: '.$content_type);
   		header('Content-Disposition: attachment; filename=' . $file);
   		header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
   		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   		header("Pragma: no-cache");
   	  }
   $sql = "select search_time, search_term, search_results from " . TABLE_SEARCH_LOG . " order by search_time desc";
   $search_data = mysql_query($sql);
   
   echo '"'.TABLE_HEADING_DATE . '","' . TABLE_HEADING_SEARCH_TERM . '","' . TABLE_HEADING_RESULTS . '"' . "\n";
   
   while($row = mysql_fetch_row($search_data))
   {
     echo $row[0].',';
     $phrase = str_replace('"', '""', $row[1]);
     echo '"' . $phrase . '",';
     echo $row[2]."\n";
   }
   }
   
   $action = (isset($_GET['action']) ? $_GET['action'] : '');
   
   switch($action)
   {
   case 'clear_search_log':
     $db->Execute("DELETE FROM ".TABLE_SEARCH_LOG." WHERE search_time < DATE_SUB(curdate(), INTERVAL ".(int)$_POST['days']." DAY)");
     $db->Execute("optimize table " . TABLE_SEARCH_LOG);
     $messageStack->add_session(SUCCESS_CLEAN_SEARCH_LOG, 'success');
     zen_redirect(zen_href_link(FILENAME_STATS_SEARCH_LOG));
     break;
   
   case 'export_search_log':
     export_search_log();
     break;
   }
   
   if ($action != 'export_search_log'){
   ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<!--doctype changed to stop quirks mode -->
<html <?php echo HTML_PARAMS; ?>>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
      <title><?php echo TITLE.' - Admin - '. HEADING_TITLE; ?></title>
      <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
      <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
      <script language="javascript" type="text/javascript" src="includes/menu.js"></script>
      <script language="javascript" type="text/javascript" src="includes/general.js"></script>
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
      <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
      <!-- header_eof //--> 
      <!-- body //-->
      <table border="0" width="100%" cellspacing="2" cellpadding="2" summary="body outer table">
         <tr>
            <!-- body_text //-->
            <td width="100%" valign="top">
               <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="body inner table">
                  <tr>
                     <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="title table">
                           <tr>
                              <td class="pageHeading"><?php echo HEADING_TITLE.' '.STATS_SEARCH_LOG_VERSION; ?></td>
                              <td></td>
                              <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                           </tr>
                           <tr>
                              <td>
                                 <?php echo zen_draw_form('clear_search_log', FILENAME_STATS_SEARCH_LOG, 'action=clear_search_log','post');//, 'onsubmit="return check_form(clear_search_log);"'); ?>
                                 <table border="0" cellspacing="0" cellpadding="2" summary="clear search log">
                                    <tr>
                                       <td class="main" align="left" valign="top"><?php printf (TEXT_DELETE_OLD_RECORDS, zen_draw_input_field('days','7','size="4"')); ?></td>
                                       <td class="main" align="right" valign="middle"><?php echo zen_image_submit('button_delete.gif', IMAGE_DELETE); ?></td>
                                    </tr>
                                 </table>
                                 </form>
                              </td>
                              <td>
                                 <?php echo zen_draw_form('export_search_log', FILENAME_STATS_SEARCH_LOG, 'action=export_search_log','post');//, 'onsubmit="return check_form(export_search_log);"'); ?>
                                 <table border="0" cellspacing="0" cellpadding="2" summary="export search log">
                                    <tr>
                                       <td class="main" align="left" valign="top"><?php echo TEXT_EXPORT_SEARCH_LOG; ?></td>
                                       <td class="main" align="right" valign="middle"><?php echo zen_image_submit('button_save.gif', IMAGE_SAVE); ?></td>
                                    </tr>
                                 </table>
                                 </form>
                              </td>
                              <td>
                                 <p><a href="http://www.zen-cart.com/downloads.php?do=file&amp;id=485" target="_blank"><?php echo TEXT_SEARCH_LOG_PLUGIN_LINK;?></a><br />
                                    <a href="http://www.zen-cart.com/showthread.php?73640-Customer-Search-Log" target="_blank"><?php echo TEXT_SEARCH_LOG_SUPPORT_LINK;?></a>
                                 </p>
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="Search Logs table">
                           <tr>
                              <td valign="top">
                                 <table border="0" width="100%" cellspacing="0" cellpadding="2" summary="">
                                    <tr class="dataTableHeadingRow">
                                       <td class="dataTableHeadingContent" align="left" valign="top" width="20%"><?php echo (($_GET['list_order']=='searchdate' or $_GET['list_order']=='searchdate-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_DATE . '</span>' : TABLE_HEADING_DATE); ?><br />
                                          <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchdate', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchdate' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp; <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchdate-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchdate-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                                       </td>
                                       <td class="dataTableHeadingContent" align="left" valign="top"><?php echo (($_GET['list_order']=='searchterm' or $_GET['list_order']=='searchterm-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_SEARCH_TERM . '</span>' : TABLE_HEADING_SEARCH_TERM); ?><br />
                                          <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchterm', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchterm' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp; <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchterm-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchterm-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                                       </td>
                                       <td class="dataTableHeadingContent" width="20%"><?php echo (($_GET['list_order']=='searchresults' or $_GET['list_order']=='searchresults-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_RESULTS . '</span>' : TABLE_HEADING_RESULTS); ?><br />
                                          <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchresults', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchresults' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</span>'); ?></a>&nbsp; <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=searchresults-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='searchresults-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                                       </td>
                                    </tr>
                                    <?php
                                       switch ($_GET['list_order'])
                                       {
                                         case "searchdate":
                                       	  $disp_order = "search_time";
                                       	  break;
                                         case "searchdate-desc":
                                       	  $disp_order = "search_time DESC";
                                       	  break;
                                         case "searchterm":
                                       	  $disp_order = "search_term, search_time DESC";
                                       	  break;
                                         case "searchterm-desc":
                                       	  $disp_order = "search_term DESC, search_time DESC";
                                       	  break;
                                         case "searchresults":
                                       	  $disp_order = "search_results, search_time DESC";
                                       	  break;
                                         case "searchresults-desc":
                                       	  $disp_order = "search_results DESC, search_time DESC";
                                       	  break;
                                         default:
                                       	  $disp_order = "search_time DESC";
                                       	  break;
                                       }
                                       
                                       $search_query_raw = "select search_results, search_term, search_time from " . TABLE_SEARCH_LOG . " order by " . $disp_order;
                                       $search_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $search_query_raw, $search_query_numrows);
                                       $search_terms = $db->Execute($search_query_raw);
                                       while (!$search_terms->EOF)
                                       {
                                       ?>
                                    <tr class="dataTableRow">
                                       <td class="dataTableContent"><?php echo $search_terms->fields['search_time']; ?>&nbsp;&nbsp;</td>
                                       <td class="dataTableContent"><?php echo htmlspecialchars(stripslashes($search_terms->fields['search_term'])); ?>&nbsp;&nbsp;</td>
                                       <td class="dataTableContent"><?php echo $search_terms->fields['search_results']; ?></td>
                                    </tr>
                                    <?php
                                       $search_terms->MoveNext();
                                       }
                                       ?>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td colspan="3">
                                 <table border="0" width="100%" cellspacing="0" cellpadding="2" summary="">
                                    <tr>
                                       <td class="smallText" valign="top"><?php echo $search_split->display_count($search_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SEARCHES); ?></td>
                                       <td class="smallText" align="right"><?php echo $search_split->display_links($search_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page'))); ?></td>
                                    </tr>
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
      <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
      <!-- footer_eof //-->
   </body>
</html>
<?php } ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>