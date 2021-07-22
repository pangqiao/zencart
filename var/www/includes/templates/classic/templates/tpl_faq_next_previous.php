<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_products_next_previous.php 6912 2007-09-02 02:23:45Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_faq_next_previous.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
/*
 WebMakers.com Added: Previous/Next through categories products
 Thanks to Nirvana, Yoja and Joachim de Boer
 Modifications: Linda McGrath osCommerce@WebMakers.com
*/

$faq_category_name = zen_get_faq_categories_name((int)$current_faq_category_id);
?>

<?php // only display when more than 1
  if ($faqs_ids->RecordCount() > 1) {
?>
<?php
// only display when more than 1
  if ($faqs_found_count > 1) {
?>
<p class="faqnavNextPrevCounter"><?php echo (PREV_NEXT_FAQ); ?><?php echo ($position+1 . "/" . $counter) . FAQ_CATEGORY_IN . $faq_category_name . FAQ_CATEGORY_FAQS; ?></p>
<div class="faqnavNextPrevList"><a href="<?php echo zen_href_link('faq_info', "fcPath=$fcPath&faqs_id=$previous"); ?>"><?php echo $previous_image . $previous_button; ?></a></div>
<div class="faqnavNextPrevList"><a href="<?php echo zen_href_link(FILENAME_FAQS, "fcPath=$fcPath"); ?>"><?php echo zen_image_button(BUTTON_IMAGE_RETURN_TO_FAQ_LIST, BUTTON_RETURN_TO_FAQ_LIST_ALT); ?></a></div>
<div class="faqnavNextPrevList"><a href="<?php echo zen_href_link('faq_info', "fcPath=$fcPath&faqs_id=$next_item"); ?>"><?php echo  $next_item_button . $next_item_image; ?></a></div>
<?php
  }
}
?>
<br class="clearBoth"/>