<?php
//MOD Product Listing Sorter, all three buttons modified
/**
 * Page Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_products_next_previous.php 6912 2007-09-02 02:23:45Z drbyte $
 */

/*
 WebMakers.com Added: Previous/Next through categories products
 Thanks to Nirvana, Yoja and Joachim de Boer
 Modifications: Linda McGrath osCommerce@WebMakers.com
*/

?>
<div class="navNextPrevWrapper centeredContent">
<?php
// only display when more than 1
  if ($products_found_count > 1) {
?>
<p class="navNextPrevCounter"><?php echo (PREV_NEXT_PRODUCT); ?><?php echo ($position+1 . "/" . $counter); ?></p>
<div class="navNextPrevList"><a href="<?php echo zen_href_link(zen_get_info_page($previous), "cPath=$cPath&products_id=$previous".'&'.zen_get_all_get_params(array('cPath','products_id','number_of_uploads'))); ?>"><?php echo $previous_image . $previous_button; ?></a></div>

<div class="navNextPrevList"><a href="<?php echo zen_href_link(FILENAME_DEFAULT, '&'.zen_get_all_get_params(array('products_id','number_of_uploads'))); ?>"><?php echo zen_image_button(BUTTON_IMAGE_RETURN_TO_PROD_LIST, BUTTON_RETURN_TO_PROD_LIST_ALT); ?></a></div>

<div class="navNextPrevList"><a href="<?php echo zen_href_link(zen_get_info_page($next_item), "cPath=$cPath&products_id=$next_item".'&'.zen_get_all_get_params(array('cPath','products_id','number_of_uploads'))); ?>"><?php echo  $next_item_button . $next_item_image; ?></a></div>
<?php
  }
?>
</div>