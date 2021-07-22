<?php
/**
 * @package admin
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: products_price_manager.php 6561 2007-07-05 17:12:31Z drbyte $
 */

  require('includes/application_top.php');

  // verify products exist
  $chk_products = $db->Execute("select * from " . TABLE_CATEGORIES . " limit 1");
 
  if ($chk_products->RecordCount() < 1) {
    $messageStack->add_session(ERROR_DEFINE_PRODUCTS, 'caution');
    zen_redirect(zen_href_link(FILENAME_CATEGORIES));
  }

  require(DIR_WS_CLASSES . 'currencies.php');
  
  $currencies = new currencies();

  $action = (isset($_POST['action']) ? $_POST['action'] : '');
  // Product is product discount type - None, Percentage, Actual Price, $$ off
  $discount_type_array = array(array('id' => '0', 'text' => DISCOUNT_TYPE_DROPDOWN_0),
                                array('id' => '1', 'text' => DISCOUNT_TYPE_DROPDOWN_1),
                                array('id' => '2', 'text' => DISCOUNT_TYPE_DROPDOWN_2),
                                array('id' => '3', 'text' => DISCOUNT_TYPE_DROPDOWN_3));

  // Product is product discount type from price or special
  $discount_type_from_array = array(array('id' => '0', 'text' => DISCOUNT_TYPE_FROM_DROPDOWN_0),
                              array('id' => '1', 'text' => DISCOUNT_TYPE_FROM_DROPDOWN_1));

  if (isset($_POST['cat_id_input']) && $_POST['cat_id_input'] != ''){
    $category_id = $_POST['cat_id_input'] ;
  }else {
  	$category_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : '';
  }
  if ($action == 'delete'){ 
    $db->Execute("delete from " . TABLE_CATEGORIES_DISCOUNT_QUANTITY . " where categories_id='" . $category_id . "'");
  }
  if ($action == 'update'){
  	$_POST[''];
  	$update_query = "";
  	$sql_data_array = "";
  	$db->Execute("update " . TABLE_CATEGORIES . " set
            categories_discount_type='" . (int)zen_db_prepare_input($_POST['categories_discount_type']) . "',
            categories_discount_type_from='" . (int)zen_db_prepare_input($_POST['categories_discount_type_from']) . "',
            categories_mixed_discount_quantity='" . (int)zen_db_prepare_input($_POST['categories_mixed_discount_quantity']) . "'
            where categories_id='" . $category_id . "'");
  	$db->Execute("delete from " . TABLE_CATEGORIES_DISCOUNT_QUANTITY . " where categories_id='" . $category_id . "'");
    $i=1;
    $new_id = 0;
    $discount_cnt = 0;
    $n=sizeof($_POST['discount_qty']);
    for ($i=1; $i<=$n; $i++) {
      if ($_POST['discount_qty'][$i] > 0) {
            $new_id++;
            $db->Execute("insert into " . TABLE_CATEGORIES_DISCOUNT_QUANTITY . "
                          (discount_id, categories_id, discount_qty, discount_price)
                          values ('" . $new_id . "', '" . $category_id . "', '" . $_POST['discount_qty'][$i] . "', '" . $_POST['discount_price'][$i] . "')");
            $discount_cnt++;
          } else {
            loop;
          }
        }
  	$messageStack->add_session('Successfully set Categories Qty Discounts','success');
  	zen_redirect(zen_href_link(FILENAME_QUANTITIY_DISCOUNTS));
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
<script language="javascript" src="includes/general.js"></script>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>

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
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="0" cellpadding="5" >
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">

      <tr>
        <td align="center">
        <?php if (isset($category_id) && $category_id != ''){ 
              echo zen_draw_form('quantity_discounts',FILENAME_QUANTITIY_DISCOUNTS,'','post');
              echo zen_draw_pull_down_menu('cat_id',zen_get_category_tree(),(isset($category_id) ? $category_id : ''),' onchange="this.form.submit();"');
              echo '</form>';
              ?>

        </td>
      </tr>
      <tr>
        <td align="center">
        <?php
          echo zen_draw_form('quantity_discounts',FILENAME_QUANTITIY_DISCOUNTS,'','post');
          echo zen_draw_hidden_field('cat_id',$category_id);
          echo zen_draw_hidden_field('action','update');
          $category_info_query = "SELECT c.`categories_discount_type`, c.`categories_discount_type_from`, c.`categories_mixed_discount_quantity` FROM " . TABLE_CATEGORIES ." c WHERE c.`categories_id` = ".$category_id ." LIMIT 1";
          $category_info = $db->Execute($category_info_query);
          if ($category_info->RecordCount()>0){
            $categories_discount_type = $category_info->fields['categories_discount_type'];
            $categories_discount_type_from = $category_info->fields['categories_discount_type_from'];
            if ($category_info->fields['categories_mixed_discount_quantity'] == 1){
            	$in_categories_mixed_discount_quantity = 1;
            	$out_categories_mixed_diescount_quantity = 0;
            }else{
            	$in_categories_mixed_discount_quantity = 0;
            	$out_categories_mixed_diescount_quantity = 1;
            }
          }
          
          ?>
        
        
        <table cellspacing="0" cellpadding="5" id="table_info">
          <tr>
            <th>
              <?php echo zen_get_category_name($category_id,$_SESSION['languages_id']) ?> - Qty Discounts
            </th>      
          </tr>

          <tr>
            <td>
              <table border="0" cellspacing="0" cellpadding="5" width="100%">
              <tr>
              <td colspan="3" class="main" valign="top"  style="border-right:0;"><?php echo TEXT_PRODUCTS_MIXED_DISCOUNT_QUANTITY; ?>&nbsp;&nbsp;<?php echo zen_draw_radio_field('categories_mixed_discount_quantity', '1', $in_categories_mixed_discount_quantity==1) . '&nbsp;' . TEXT_YES . '&nbsp;&nbsp;' . zen_draw_radio_field('categories_mixed_discount_quantity', '0', $out_categories_mixed_diescount_quantity) . '&nbsp;' . TEXT_NO; ?></td>
               </tr>
            <tr>
              <td colspan="3"  style="border-right:0;"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
            </tr>
            <tr>
              <td class="main">
                <?php echo TEXT_DISCOUNT_TYPE_INFO; ?>
              </td>
              <td class="main">
                <?php echo TEXT_DISCOUNT_TYPE . ' ' . zen_draw_pull_down_menu('categories_discount_type', $discount_type_array, $categories_discount_type); ?>
              </td>
              <td class="main" style="border-right:0;">
                <?php echo TEXT_DISCOUNT_TYPE_FROM . ' ' . zen_draw_pull_down_menu('categories_discount_type_from', $discount_type_from_array, $categories_discount_type_from); ?>
              </td>
            </tr>
            <tr>
              <td class="main" align="left"><?php echo TEXT_PRODUCTS_DISCOUNT_QTY_TITLE; ?></td>
              <td class="main" align="left"><?php echo TEXT_PRODUCTS_DISCOUNT_QTY; ?></td>
              <td class="main" align="left" style="border-right:0;"><?php echo TEXT_PRODUCTS_DISCOUNT_PRICE; ?></td>
            </tr>
  <?php
  
    $display_priced_by_attributes = zen_get_products_price_is_priced_by_attributes($category_id);
    $display_price = zen_get_products_base_price($category_id);
    $display_specials_price = zen_get_products_special_price($category_id, true);
    
    $discount_query = "SELECT discount_id,discount_qty,discount_price FROM ". TABLE_CATEGORIES_DISCOUNT_QUANTITY . " WHERE categories_id = ". $category_id;

    $discount = $db->Execute($discount_query);
    $i = 0;
    while (!$discount->EOF){
      $i++;
      $discount_name[] = array('id' => $i,
                                   'discount_qty' => $discount->fields['discount_qty'],
                                   'discount_price' => $discount->fields['discount_price']);
      $discount->MoveNext();
    }
      for ($i=0, $n=6; $i<$n; $i++) {
        switch ($pInfo->products_discount_type) {
          // none
          case '0':
            $discounted_price = 0;
            break;
          // percentage discount
          case '1':
            if ($pInfo->products_discount_type_from == '0') {
              $discounted_price = $display_price - ($display_price * ($discount_name[$i]['discount_price']/100));
            } else {
              if (!$display_specials_price) {
                $discounted_price = $display_price - ($display_price * ($discount_name[$i]['discount_price']/100));
              } else {
                $discounted_price = $display_specials_price - ($display_specials_price * ($discount_name[$i]['discount_price']/100));
              }
            }
  
            break;
          // actual price
          case '2':
            if ($pInfo->products_discount_type_from == '0') {
              $discounted_price = $discount_name[$i]['discount_price'];
            } else {
              $discounted_price = $discount_name[$i]['discount_price'];
            }
            break;
          // amount offprice
          case '3':
            if ($pInfo->products_discount_type_from == '0') {
              $discounted_price = $display_price - $discount_name[$i]['discount_price'];
            } else {
              if (!$display_specials_price) {
                $discounted_price = $display_price - $discount_name[$i]['discount_price'];
              } else {
                $discounted_price = $display_specials_price - $discount_name[$i]['discount_price'];
              }
            }
            break;
        }
  ?>
            <tr >
              <td class="main" algin="left"><?php echo TEXT_PRODUCTS_DISCOUNT . ' ' . ($i+1); ?></td>
              <td class="main" algin="left"><?php echo zen_draw_input_field('discount_qty[' . ($i+1) . ']', $discount_name[$i]['discount_qty']); ?></td>
              <td class="main" algin="left" style="border-right:0;"><?php echo zen_draw_input_field('discount_price[' . ($i+1) . ']', $discount_name[$i]['discount_price']); ?></td>
            </tr>
  <?php
      }
  ?>
            <tr>
              <td colspan="3" style="color:#900;text-align:center;border-right:none;">All 0 Quantity Discounts will be removed when Updated </td>
            </tr>
               </table>
            </td>
            </tr>

            <tr>
            <td>
              <?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE_PRICE_CHANGES); ?>
            </td>
          </tr>
        </table>
<?php echo '</form>';?>   
        <?php }else{ ?>
        <?php echo zen_draw_form('quantity_discounts',FILENAME_QUANTITIY_DISCOUNTS,'','post');?>
        
        <table cellspacing="0" cellpadding="5" id="table_info">
          <tr>
            <th>
              Categories Qty Discounts
            </th>      
          </tr>
          <tr>
            <td>
              <?php echo zen_draw_pull_down_menu('cat_id',zen_get_category_tree(),(isset($category_id) ? $category_id : ''),' onchange="this.form.submit();"'); ?>
              <br/><br/>Or Input Categoriy Id <br/><br/>
              <?php echo zen_draw_input_field('cat_id_input',''); ?>
            </td>
          </tr>
         </table>
        <?php
           echo '</form>';
          } 
        ?>
        

        </td>
    </tr>
</table>

      
<!-- body_text_eof //-->
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
