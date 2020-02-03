<?php
//MOD Product Listing Sorter
$debug = '';//1 or 0, show debugging information 
$debug_prefix = '('.str_replace(DIR_FS_CATALOG, '', str_replace('\\','/',__FILE__)).' line ';
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @package productTypes
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: default_filter.php 14870 2009-11-19 22:36:24Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  
  if (isset($_GET['sort']) && strlen($_GET['sort']) > 3) {
    $_GET['sort'] = substr($_GET['sort'], 0, 3);
  }

if (isset($_GET['alpha_filter_id']) && (int)$_GET['alpha_filter_id'] > 0) {
    $alpha_sort = " and pd.products_name LIKE '" . chr((int)$_GET['alpha_filter_id']) . "%' ";
  } else {
    $alpha_sort = '';
  }
//MOD Product Listing Sorter
  if (isset($_GET['product_listing_sorter_id']) && (int)$_GET['product_listing_sorter_id'] > 0) {
      $sorter_list_search = explode(';', '0:reset_placeholder;' . PRODUCT_LISTING_SORTER_LIST);
      for ($j=0, $n=sizeof($sorter_list_search); $j<$n; $j++) {
        if ((int)$_GET['product_listing_sorter_id'] == $j) {
          $elements_sorter_list = explode(':', $sorter_list_search[$j]);
          $pattern_multi = str_replace(',', '', $elements_sorter_list[1]);
          $product_listing_sorter = " order by " . $pattern_multi;
		  if ($debug) echo $debug_prefix.__LINE__.') '.'$product_listing_sorter='.$product_listing_sorter.'<br />'; 
          break;
        }
      }
	  
if ($debug) echo $debug_prefix.__LINE__.') '.'Built $product_listing_sorter: ' . $product_listing_sorter . '<br>';

  } 
  
  {
      ///////////////////////////////////////////
	///    attributs filter plugin code    ///
	/////////////////////////////////////////
	$sql_products_attr = '';
	$where_products_attr = '';
	if (zen_not_null($attr_id_array)){
		$sql_products_attr = ',(select products_id from '.TABLE_PRODUCTS_ATTRIBUTES.' where options_values_id in('.implode(',',$attr_id_array).') group by products_id having count(*)>='.count($attr_id_array).') pa ';
		$where_products_attr = ' and pa.products_id = p.products_id ';
	}
	/////////////////////////////////////// 
  }
if ($debug) echo $debug_prefix.__LINE__.') '.'zen_get_all_get_params(): ' . zen_get_all_get_params() . '<br>';

  if (!isset($select_column_list)) $select_column_list = "";
   // show the products of a specified manufacturer
  if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '' ) {if ($debug) echo $debug_prefix.__LINE__.'<br />';	  
    if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {if ($debug) echo $debug_prefix.__LINE__.'<br />';			
// We are asked to show only a specific category
      $listing_sql = "select " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, if(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
       from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id , " .
       TABLE_PRODUCTS_DESCRIPTION . " pd, " .
       TABLE_MANUFACTURERS . " m, " .
       TABLE_PRODUCTS_TO_CATEGORIES . " p2c
	   ".$sql_products_attr."
       where p.products_status = 1
	   ".$where_products_attr."
         and p.manufacturers_id = m.manufacturers_id
         and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'
         and p.products_id = p2c.products_id
         and pd.products_id = p2c.products_id
         and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
         and p2c.categories_id = '" . (int)$_GET['filter_id'] . "'" .
         $alpha_sort.$product_listing_sorter;
    } else {if ($debug) echo $debug_prefix.__LINE__.'<br />';	
// We show them all
      $listing_sql = "select " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
      from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " .
      TABLE_PRODUCTS_DESCRIPTION . " pd, " .
      TABLE_MANUFACTURERS . " m
	  ".$sql_products_attr."
      where p.products_status = 1
	  ".$where_products_attr."
        and pd.products_id = p.products_id
        and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p.manufacturers_id = m.manufacturers_id
        and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'" .
        $alpha_sort.$product_listing_sorter;
    }
  } else {if ($debug) echo $debug_prefix.__LINE__.')<br />';
// show the products in a given category
    if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {if ($debug) echo $debug_prefix.__LINE__.'<br />';	
// We are asked to show only specific category
      $listing_sql = "select " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
      from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " .
      TABLE_PRODUCTS_DESCRIPTION . " pd, " .
      TABLE_MANUFACTURERS . " m, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c
	  ".$sql_products_attr."
      where p.products_status = 1
	  ".$where_products_attr."
        and p.manufacturers_id = m.manufacturers_id
        and m.manufacturers_id = '" . (int)$_GET['filter_id'] . "'
        and p.products_id = p2c.products_id
        and pd.products_id = p2c.products_id
        and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p2c.categories_id = '" . (int)$current_category_id . "'" .
        $alpha_sort.$product_listing_sorter;if ($debug) echo $debug_prefix.__LINE__.') $listing_sql:<br />' . $listing_sql . '<br /><br />';
    } else {if ($debug) echo $debug_prefix.__LINE__.')<br />';	
// We show them all
      $listing_sql = "select " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status =1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
       from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " .
       TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " .
       TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS . " s on p2c.products_id = s.products_id
	   ".$sql_products_attr."
       where p.products_status = 1
	   ".$where_products_attr."
         and p.products_id = p2c.products_id
         and pd.products_id = p2c.products_id
         and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
         and p2c.categories_id = '" . (int)$current_category_id . "'" .
         $alpha_sort.$product_listing_sorter;
    }
//	}
  }

// set the default sort order setting from the Admin when not defined by customer
  if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
    $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;if ($debug) echo $debug_prefix.__LINE__.') '.'$_GET[\'sort\']='.$_GET['sort'].'<br />';   
  }
  if ($debug) echo $debug_prefix.__LINE__.')<br />'; 
  if (isset($column_list) && (!isset($_GET['product_listing_sorter_id']) || $_GET['product_listing_sorter_id']==0)) {//are some columns defined? //MOD Product Listing Sorter
  if ($debug) echo $debug_prefix.__LINE__.')<br />';    
    if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
if ($debug) echo $debug_prefix.__LINE__.') '.'$i='.$i.'<br />';       
        if (isset($column_list[$i]) && $column_list[$i] == 'PRODUCT_LIST_NAME') {//get the column with the PRODUCT_LIST_NAME in it and set the $_GET['sort'] to match its position
          $_GET['sort'] = $i+1 . 'a';
if ($debug) echo $debug_prefix.__LINE__.') '.'$_GET[\'sort\']='.$_GET['sort'].'<br />';
          $listing_sql .= " order by p.products_sort_order, pd.products_name";
          break;//break out of the for loop when PRODUCT_LIST_NAME is found
        } else {
if ($debug) echo $debug_prefix.__LINE__.') '.'$_GET[\'sort\']='.$_GET['sort'].'<br />';
// sort by products_sort_order when PRODUCT_LISTING_DEFAULT_SORT_ORDER is left blank
// for reverse, descending order use:
//       $listing_sql .= " order by p.products_sort_order desc, pd.products_name";
          $listing_sql .= " order by p.products_sort_order, pd.products_name";
          break;
        }
      }
// if set to nothing use products_sort_order and PRODUCTS_LIST_NAME is off
      if (PRODUCT_LISTING_DEFAULT_SORT_ORDER == '') {
        $_GET['sort'] = '20a';
      }
    } else {if ($debug) echo $debug_prefix.__LINE__.') '.'SWITCH CASE $_GET[\'sort\']='.$_GET['sort'].'<br />';
      $sort_col = substr($_GET['sort'], 0 , 1);
      $sort_order = substr($_GET['sort'], -1, 1);
      switch ($column_list[$sort_col-1]) {
        case 'PRODUCT_LIST_MODEL':
          $listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_NAME':
          $listing_sql .= " order by pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $listing_sql .= " order by m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $listing_sql .= " order by p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_IMAGE':
          $listing_sql .= " order by pd.products_name";
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $listing_sql .= " order by p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_PRICE':
          $listing_sql .= " order by p.products_price_sorter " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
      }
if ($debug) echo $debug_prefix.__LINE__.') '.'SWITCH CASE BREAK on '.$column_list[$sort_col-1].' $listing_sql:<br />'.$listing_sql.'<br /><br />';
    }
  }
// optional Product List Filter
  if (PRODUCT_LIST_FILTER > 0) {//Admin->Product Listing->Display Category/Manufacturer Filter (0=off; 1=on)
    if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '') {if ($debug) echo $debug_prefix.__LINE__.'<br />';
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_CATEGORIES . " c, " .
      TABLE_CATEGORIES_DESCRIPTION . " cd
      where p.products_status = 1
        and p.products_id = p2c.products_id
        and p2c.categories_id = c.categories_id
        and p2c.categories_id = cd.categories_id
        and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'
      order by cd.categories_name";
if ($debug) echo $debug_prefix.__LINE__.') $filterlist_sql:<br />'.$filterlist_sql.'<br />';
    } else {
      $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_MANUFACTURERS . " m
      where p.products_status = 1
        and p.manufacturers_id = m.manufacturers_id
        and p.products_id = p2c.products_id
        and p2c.categories_id = '" . (int)$current_category_id . "'
      order by m.manufacturers_name";
if ($debug) echo $debug_prefix.__LINE__.') $filterlist_sql:<br />'.$filterlist_sql.'<br />';
    }
// Show display SQL
if ($debug) echo $debug_prefix.__LINE__.') FINAL $listing_sql:<br />' . $listing_sql . '<br /><br />';
if (($_GET['sort']!=0) || ($alpha_sort!=0) || ($product_listing_sorter !=0)) {//MOD Product Listing Sorter: to make available for product info page prev/next
		 $_SESSION['listing_sql'] = $listing_sql;
}
    $do_filter_list = false;
    $filterlist = $db->Execute($filterlist_sql);
    if ($filterlist->RecordCount() > 1) {
        $do_filter_list = true;
		 if ($debug) echo $debug_prefix.__LINE__.') $do_filter_list:<br />'.$do_filter_list.'<br />';
      if (isset($_GET['manufacturers_id'])) {
        $getoption_set =  true;
        $get_option_variable = 'manufacturers_id';
        $options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
      } else {
        $options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
      }
      while (!$filterlist->EOF) {
        $options[] = array('id' => $filterlist->fields['id'], 'text' => $filterlist->fields['name']);
        $filterlist->MoveNext();
      }
    }
  }