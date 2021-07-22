<?php
/**
 * Page Template
 *
 * Displays EZ-Pages listing in search function
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_advanced_search_ezpages.php v1.0 2008-10-30 00:00:00  by iniebla $
 */

?> 

<h1 id="advSearchResultsDefaultHeading"><?php echo "<br/><br/>".TEXT_DISPLAY_FOUND_EZPAGES; ?></h1>

<?php
$list_box_contents = '';

$lc_text = TEXT_HEADING_TITLE_FOUND_EZPAGES;  
$lc_align = 'left';
$list_box_contents[0]= array('params' => 'class="productListing-rowheading"');
$list_box_contents[0][1] = array('align' => $lc_align,
                                      'params' => 'class="productListing-heading"',
                                      'text' => $lc_text );
$lc_text = TEXT_HEADING_TEXT_FOUND_EZPAGES;  
$lc_align = 'left';
$list_box_contents[0][2] = array('align' => $lc_align,
                                      'params' => 'class="productListing-heading"',
                                      'text' => $lc_text );                                      
                                      
// SQL for selecting records already in $listing_sql_ezp

$listing_split = new splitPageResults($listing_sql_ezp, MAX_DISPLAY_PRODUCTS_LISTING, 'e.pages_id', 'page');

$how_many = 0;
if ($listing_split->number_of_rows > 0) {
  $rows = 0;
  $page_query_ez = $db->Execute($listing_split->sql_query);

  $extra_row = 0;
  while (!$page_query_ez->EOF) {
    $page_query_list = array ();
    $rows++;
    if ((($rows-$extra_row)/2) == floor(($rows-$extra_row)/2)) {
      $list_box_contents[$rows] = array('params' => 'class="productListing-even"');
    } else {
      $list_box_contents[$rows] = array('params' => 'class="productListing-odd"');
    }
    
    $cur_row = sizeof($list_box_contents) - 1;
    
//Way to get URL EZPage:  Taken from other module related to EZ Pages.  
    $page_query_list['altURL'] = '';

      // if altURL is specified, check to see if it starts with "http", and if so, create direct URL, otherwise use a zen href link
   switch (true) {
     // external link new window or same window
     case ($page_query_ez->fields['alt_url_external'] != ''):
     $page_query_list['altURL']  = $page_query_ez->fields['alt_url_external'];
     break;
     // internal link new window
     case ($page_query_ez->fields['alt_url'] != '' and $page_query_ez->fields['page_open_new_window'] == '1'):
     $page_query_list['altURL']  = (substr($page_query_ez->fields['alt_url'],0,4) == 'http') ?
     $page_query_ez->fields['alt_url'] :
     ($page_query_ez->fields['alt_url']=='' ? '' : zen_href_link($page_query_ez->fields['alt_url'], '', ($page_query_ez->fields['page_is_ssl']=='0' ? 'NONSSL' : 'SSL'), true, true, true));
     break;
     // internal link same window
     case ($page_query_ez->fields['alt_url'] != '' and $page_query_ez->fields['page_open_new_window'] == '0'):
     $page_query_list['altURL']  = (substr($page_query_ez->fields['alt_url'],0,4) == 'http') ?
     $page_query_ez->fields['alt_url'] :
     ($page_query_ez->fields['alt_url']=='' ? '' : zen_href_link($page_query_ez->fields['alt_url'], '', ($page_query_ez->fields['page_is_ssl']=='0' ? 'NONSSL' : 'SSL'), true, true, true));
     break;
   }

   // if altURL is specified, use it; otherwise, use EZPage ID to create link
   $page_query_list['link'] = ($page_query_list['altURL'] =='') ?
   zen_href_link(FILENAME_EZPAGES, 'id=' . $page_query_ez->fields['pages_id'] . ($page_query_ez->fields['toc_chapter'] > 0 ? '&chapter=' . $page_query_ez->fields['toc_chapter'] : ''), ($page_query_ez->fields['page_is_ssl']=='0' ? 'NONSSL' : 'SSL')) :
   $page_query_list['altURL'];
   $page_query_list['link'] .= ($page_query_ez->fields['page_open_new_window'] == '1' ? '" target="_blank' : '');
// En of get EZpage URL



   $lc_align = 'left';        
   $lc_text1 = '<h3 class="itemTitle"><a href="' . $page_query_list['link']. '">' . $page_query_ez->fields['pages_title'] . '</a></h3>';	  
   $lc_text2 = '<div class="listingDescription">'. zen_trunc_string(zen_clean_html(stripslashes($page_query_ez->fields['pages_html_text']))) . '</div>' ;	                 
   $list_box_contents[$rows][1] = array('align' => $lc_align,
                                              'params' => 'class="productListing-data"',
                                              'text'  => $lc_text1);
   $list_box_contents[$rows][2] = array('align' => $lc_align,
                                              'params' => 'class="productListing-data"',
                                              'text'  => $lc_text2);                                               
   $page_query_ez->MoveNext();

   }
}   
?>

<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_XX_OF_YY); ?></div>
<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />
<?php
}
?>
<?php
  require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
?>

<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_XX_OF_YY); ?></div>
<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />
<?php
}
?>