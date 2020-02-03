<?php
/**MOD Product Listing Sorter
 * Page Template
 *
 * Loaded automatically by index.php?main_page=advanced_search_result.<br />
 * Displays results of advanced search
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_advanced_search_result_default.php 4182 2006-08-21 02:11:37Z ajeh $
 */
?>
<div class="centerColumn" id="advSearchResultsDefault">

<!-- <h1 id="advSearchResultsDefaultHeading"><?php //echo HEADING_TITLE; ?></h1> -->
<h1 id="advSearchResultsDefaultHeading"><?php echo TEXT_DISPLAY_FOUND_PRODUCTS; ?></h1>

<?php
//================================ Begin of Advanced Search Plus
if ($found_products_ad == true) {
//================================ En of Advanced Search Plus 

  if ($do_filter_list || PRODUCT_LIST_ALPHA_SORTER == 'true') {
//  $form = zen_draw_form('filter', zen_href_link(FILENAME_ADVANCED_SEARCH_RESULT), 'get') . '<label class="inputLabel">' .TEXT_SHOW . '</label>';
  $form = zen_draw_form('filter', zen_href_link(FILENAME_ADVANCED_SEARCH_RESULT), 'get');
?>
<?php echo $form; ?>
<?php
  echo zen_hide_session_id();

/* Re-Get all GET'ed variables */
      $hidden_get_variables = '';
      reset($_GET);
      while (list($key, $value) = each($_GET)) {
        if ( ($key != 'currency') && ($key != zen_session_name()) && ($key != 'x') && ($key != 'y') && ($key != 'alpha_filter_id') && ($key != 'product_listing_sorter_id') ) {
          $hidden_get_variables .= zen_draw_hidden_field($key, $value);
        }
      }
      echo $hidden_get_variables;

  require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING_ALPHA_SORTER));

// MOD Product Listing Sorter
  require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING_SORTER));
?>
</form>
<?php
  }
?>
<?php
/**
 * Used to collate and display products from advanced search results
 */
 require($template->get_template_dir('tpl_modules_product_listing.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_product_listing.php');
?>

<?php 
//================================ Begin of Advanced Search Plus 
} else {
   echo TEXT_DISPLAY_NOT_FOUND_IN_PRODUCTS. "<br/><br/> \n";
}

if ($found_categories) {
//Retrieve $list_box_contents to be used on required modules
require($template->get_template_dir('tpl_modules_advanced_search_categories.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_advanced_search_categories.php');
} else {
echo '<br/><br/><h1 id="advSearchResultsDefaultHeading">' . TEXT_DISPLAY_FOUND_CATEGORIES . '</h1>';
   echo TEXT_DISPLAY_NOT_FOUND_IN_CATEGORIES. "<br/><br/> \n";
   }

if ($found_ezpages) {
require($template->get_template_dir('tpl_modules_advanced_search_ezpages.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_advanced_search_ezpages.php');
} else {
echo '<br/><br/><h1 id="advSearchResultsDefaultHeading">' . TEXT_DISPLAY_FOUND_EZPAGES . '</h1>';
   echo TEXT_DISPLAY_NOT_FOUND_IN_EZPAGES." <br/><br/> \n";
   }
//=============================== End of Advanced Search Plus
?> 

<div class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_ADVANCED_SEARCH, zen_get_all_get_params(array('sort', 'page', 'x', 'y')), 'NONSSL', true, false) . '">' . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

</div>