<?php
/**
 * advanced_search_ezpages.php module
 *
 * @package modules
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Iniebla Copyright 2008 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: advanced_search_ezpages.php  v1.0 2008-10-30 00:00:00 by iniebla $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

//To be compatible with EZ_pages multilingual (2 tables: ezpages and ezpages_text)
if ((EZPAGES_STATUS_HEADER == '1')  or (EZPAGES_STATUS_FOOTER == '1') or (EZPAGES_STATUS_SIDEBOX == '1')) {
	if (defined('TABLE_EZPAGES_TEXT')) {
	   $select_str_ezp = "SELECT e.pages_id, e.page_open_new_window, e.page_is_ssl, e.alt_url, e.alt_url_external, e.header_sort_order, e.sidebox_sort_order, e.footer_sort_order, e.toc_sort_order, e.toc_chapter, e.status_header, e.status_sidebox, e.status_footer, status_toc, 
						   et.pages_title, et.pages_html_text  
	                       from  " . TABLE_EZPAGES . " e, " . TABLE_EZPAGES_TEXT . " et ";
	   $where_str_ezp = " where e.pages_id = et.pages_id and (e.status_header = 1 or e.status_footer = 1 or  e.status_sidebox = 1)
	                      and et.languages_id = '" . (int)$_SESSION['languages_id'] . "'";
						  	
	if (isset($keywords) && zen_not_null($keywords)) {
	  if (zen_parse_search_string(stripslashes($_GET['keyword']), $search_keywords)) {
		    $where_str_ezp .= " AND (";		         
	    for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
	      switch ($search_keywords[$i]) {
	        case '(':
	        case ')':
	        case 'and':
	        case 'or':
	        $where_str_ezp .= " " . $search_keywords[$i] . " ";
	        break;
	        default:
	        $where_str_ezp .= "(et.pages_title LIKE '%:keywords%' OR  et.pages_html_text LIKE '%:keywords%'";		         
	        $where_str_ezp = $db->bindVars($where_str_ezp, ':keywords', $search_keywords[$i], 'noquotestring');       
	        $where_str_ezp .= ')';
	        break;
			}
	      }
	      $where_str_ezp .= " )";	
	    }
	  }
	  
	   $order_str_ezp = " order by e.toc_chapter, e.toc_sort_order, et.pages_title";
	
	} else {
	// End of code for campatibility; Ending } is also for compatibility
	   $select_str_ezp = "SELECT * from " . TABLE_EZPAGES . " e ";
	   $where_str_ezp = " where (e.status_header = 1 or e.status_footer = 1 or  e.status_sidebox = 1) and "; 
	   
	   if (isset($keywords) && zen_not_null($keywords)) {
	   if (zen_parse_search_string(stripslashes($_GET['keyword']), $search_keywords)) {
	     for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
	       switch ($search_keywords[$i]) {
	         case '(':
	         case ')':
	         case 'and':
	         case 'or':
	         $where_str_ezp .= " " . $search_keywords[$i] . " ";
	         break;
	         default:
	         $where_str_ezp .= "(e.pages_title LIKE '%:keywords%' OR  e.pages_html_text LIKE '%:keywords%'";
	         $where_str_ezp = $db->bindVars($where_str_ezp, ':keywords', $search_keywords[$i], 'noquotestring');       
	         $where_str_ezp .= ')';
	         break;
		   	 }
	       }
	     } 
	  } 
	  $order_str_ezp = " order by e.toc_chapter, e.toc_sort_order, e.pages_title"; 
	 }  // This ending } is also for compatibility
	
	$listing_sql_ezp = $select_str_ezp . $from_str_ezp . $where_str_ezp . $order_str_ezp;
	
	$page_query_ez = $db->Execute($listing_sql_ezp);
	if ($page_query_ez->RecordCount() > 0) {
	   $found_ezpages = true;                         
	}
}
?>