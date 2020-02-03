<?php
/**
 * @package addon
 * @copyright Portions Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: global_category_price.php   diptimoy $
 */
 
  require('includes/application_top.php');

  $deduction_type_array = array(array('id' => '1', 'text' => 'Decrease ( - )'),
                                array('id' => '2', 'text' => 'Increase ( + )'));
								
  $percent_type_array = array(array('id' => '1', 'text' => 'Percentage'),
                                array('id' => '2', 'text' => 'Amount'));								
								
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
 
  
  if (zen_not_null($action)) {
  switch ($action) {
  case 'update':
  
  $global_data_array = array('master_categories_id' => zen_db_prepare_input($_POST['cPath']),
                              'type' => zen_db_prepare_input($_POST['type']),
							  'percent' => zen_db_prepare_input($_POST['percent']),
                              'global' => zen_db_prepare_input($_POST['amount']));
										  
	if($global_data_array['master_categories_id'] ==0)
	   {
		 $messageStack->add_session(SELECT_CATEGORY, 'caution');
		  zen_redirect(zen_href_link(FILENAME_CATEGORY_GLOBAL_PRICE ));
	   }							 
	   if($global_data_array['global'] =='')
	   {
		 $messageStack->add_session(ENTER_PRICE, 'caution');
		  zen_redirect(zen_href_link(FILENAME_CATEGORY_GLOBAL_PRICE ));
	   }else
	   {
		if (zen_has_category_subcategories(zen_db_prepare_input($_POST['cPath'])))
	     {
		 
			 
      $subcategories_query = "select categories_id
                            from " . TABLE_CATEGORIES . "
                            where parent_id = '" . $global_data_array['master_categories_id'] . "'";

      $subcategories = $db->Execute($subcategories_query);
	
	  while (!$subcategories->EOF) {
	 
	         if($global_data_array['percent'] == 1) 
			 
			 {
			        if($global_data_array['type']== 1 )
                   {
				   
				   	   $db->Execute("update " . TABLE_PRODUCTS . "
                        SET products_price = products_price  *  ".(1 -($global_data_array['global']/100))."
                        where master_categories_id = '" .  $subcategories->fields['categories_id']. "'" );
					  
					 
				   
				   }else
				   {
				      $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price  *   " .((1 +  $global_data_array['global']/100))."
                       where master_categories_id = '" . $subcategories->fields['categories_id']. "'" );
				   }
			 }else
			 {
			       if($global_data_array['type']== 1 )
                   {
				   	  $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price  -   ".$global_data_array['global']."
                       where master_categories_id = '" .$subcategories->fields['categories_id']. "'" );
				   }else
				   {
				      $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price + ".$global_data_array['global']."
                       where master_categories_id = '" . $subcategories->fields['categories_id']. "'" );
				   }
			 }
 
 
	   $subcategories->MoveNext();
	   
	 }
				 
				 
	    }else
			  {
                 if($global_data_array['percent'] == 1) 
				 {
				 
                   if($global_data_array['type']== 1 )
                   {
 
			         $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price  *  ".(1 -($global_data_array['global']/100))."
                       where master_categories_id = '" . $global_data_array['master_categories_id']. "'" );
					} else  
					{
 
					  $db->Execute("update " . TABLE_PRODUCTS . "
                      SET products_price = products_price  *   " .((1 +  $global_data_array['global']/100))."
                      where master_categories_id = '" . $global_data_array['master_categories_id']. "'" );
					} 
					
				  }
				if($global_data_array['percent'] == 2) 
				 {
				 
				   if($global_data_array['type']== 1 )
                   {
				      $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price  -   ".$global_data_array['global']."
                       where master_categories_id = '" . $global_data_array['master_categories_id']. "'" );
				   }
				    else
                   {
				      $db->Execute("update " . TABLE_PRODUCTS . "
                       SET products_price = products_price + ".$global_data_array['global']."
                       where master_categories_id = '" . $global_data_array['master_categories_id']. "'" );
				   }
					   
 

				  }
				  
				  
			  }
			  
			   $messageStack->add_session(GLOBAL_PRICE_SUCCESS, 'success');
			   
 
              $db->Execute("insert into " .DB_PREFIX ."catalog_price_history  (change_date, catalog ,percent ,type , value) values ('" . date("Y-m-d"). "', '" . 
			  $global_data_array['master_categories_id'] . "' ,  '".$global_data_array['percent']."' ,  '".$global_data_array['type']."','".$global_data_array['global']
			  ."')");		   
											
              zen_redirect(zen_href_link(FILENAME_CATEGORY_GLOBAL_PRICE ));
			  
			}							  
								 
											
			 case 'delete_price_history':  
            $remove_price_history = "DELETE from " . TABLE_CATALOG_PRICE_HISTORY . " WHERE id = '" . zen_db_prepare_input($_GET['dID']). "' ";
            $db->Execute($remove_price_history);
		    $messageStack->add_session(CATALOG_PRICE_HISTORY, 'success');
		     zen_redirect(zen_href_link(FILENAME_CATEGORY_GLOBAL_PRICE ));
 
	   }
	
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
  
  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
  
	  if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
	{
        return false;
    }
    return true;
}
  // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<!-- header //-->
<div class="header-area">
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
</div>

<!-- body //-->
<?php echo zen_draw_form("globalprice", FILENAME_CATEGORY_GLOBAL_PRICE,   'action=update'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
<td width="20%" valign="top"  style="text-align:center;" >&nbsp;</td>
    <td width="80%" valign="top"  style="text-align:left;" > 
    
    <table width="100%" border="0" align="center">
          <tr>
            <td colspan="3" align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left" width="20%"><?php echo DEFINE_CATEGORY ; ?></td>
            <td width="80%" colspan="2" align="left"><?php echo zen_draw_pull_down_menu('cPath', zen_get_category_tree() ); ?></td>
          </tr>   
          <tr>
            <td colspan="3" align="left">&nbsp;</td>
          </tr>
         <tr>
            <td align="left" width="20%"><?php echo DEFINE_VARIATION_1 ; ?></td>
            <td width="80%" colspan="2" align="left"><?php echo zen_draw_pull_down_menu('type', $deduction_type_array); ?></td>
          </tr> 
           <tr>
            <td colspan="3" align="left">&nbsp;</td>
          </tr>
           <tr>
            <td align="left" width="20%"><?php echo DEFINE_VARIATION_2 ;?></td>
            <td width="80%" colspan="2" align="left"><?php echo zen_draw_pull_down_menu('percent', $percent_type_array); ?></td>
          </tr> 
           <tr>
            <td colspan="3" align="left">&nbsp;</td>
          </tr>        
          <tr>
            <td align="left" width="20%"><?php echo DEFINE_ENTRY ; ?></td>
            <td align="left" width="10%"><?php echo zen_draw_input_field('amount', $amount, 'onkeypress="return isNumber(event)"'); ?>
            </td>
            <td align="left" width="70%"><?php echo DEFINE_MESSAGE  ;?></td>
          </tr>
         <tr>
            <td colspan="3" align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left" width="20%"> </td>
            <td width="80%" colspan="2" align="left"> 
            <?php  echo zen_image_submit('button_update.gif', IMAGE_UPDATE) ?>            </td>
          </tr>
     </table>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
</form>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
<!-- body_text //-->

    <td width="100%" valign="top"    align="center" > 
      <table width="80%" border="1" cellpadding="10" cellspacing="10" align="center" style="border-collapse:collapse;margin-top:20px;">
  <tr>
    <td align="center"><strong><?php echo COLUMN_HEADING_1 ;?></strong>(dd/mm/YYYY)</td>
    <td align="center"><strong><?php echo COLUMN_HEADING_2 ;?></strong></td>
    <td align="center"><strong><?php echo COLUMN_HEADING_3 ;?></strong></td>
    <td align="center"><strong><?php echo COLUMN_HEADING_4 ;?></strong></td>
    <td align="center"><strong><?php echo COLUMN_HEADING_5 ;?></strong></td>
    <td align="center"><strong><?php echo COLUMN_HEADING_6 ;?></strong></td>
  </tr>

    
    <?php
	
	 
      $global_price_query = "select *
                            from ".TABLE_CATALOG_PRICE_HISTORY."
                            order by id DESC";
							
							
      $global_price = $db->Execute($global_price_query);
	 
	 if ($global_price->RecordCount() > 0) {	
	 while (!$global_price->EOF) {
	?>
 
  <tr>
    <td align="center"><?php echo date('d/m/Y' ,strtotime($global_price->fields['change_date'])); ?> </td>
    <td align="center"><?php 
	     $get_categories = "select  categories_name
                         from  " . TABLE_CATEGORIES_DESCRIPTION . "  
                         where categories_id = '" . (int)$global_price->fields['catalog'] . "' and language_id = '" . $_SESSION['languages_id'] . "'";

      $catname = $db->Execute($get_categories);
	
	echo    $catname->fields['categories_name']?></td>
    <td align="center"><?php if( $global_price->fields['percent']== 1) { echo 'Percentage';}else { echo 'Amount';} ?></td>
    <td align="center"><?php if( $global_price->fields['type']== 1) { echo ' - ';}else { echo ' + ';} ?></td>
    <td align="center"><?php echo $global_price->fields['value'] ?></td>
    <td align="center"> <?php echo '<a href="' . zen_href_link(FILENAME_CATEGORY_GLOBAL_PRICE, 'dID=' . $global_price->fields['id'] . '&action=delete_price_history') . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>'; ?></td>
  </tr>

  <?php
   $global_price->MoveNext();
     } 
   }else
     {
 	 
	   echo '<tr><td colspan =5 align="center">No Data Found</td></tr>';
	 }
  ?>
</table>

     
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
