<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_products_all_default.php 2603 2005-12-19 20:22:08Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_faqs_all_default.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 
?>
<div class="centerColumn" id="faqsIndex">
<h1 id="faqsIndexDefaultHeading"><?php echo HEADING_TITLE; ?></h1>

<?php
$show_display_category = $db->Execute(SQL_SHOW_FAQ_INDEX_LISTING);
while (!$show_display_category->EOF) {
?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_FEATURED_FAQS_INDEX') { ?>
<?php
/**
 * display the Featured Faqs Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_featured_faqs.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_featured_faqs.php'); ?>
<?php } ?>
<?php
  $show_display_category->MoveNext();
} // !EOF
?>

<?php
if (FAQ_ALL_DISPLAY_SORT_ORDER == 1) {
require($template->get_template_dir('/tpl_modules_faq_listing_display_order.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_faq_listing_display_order.php');?>
<?php } ?>

<?php
  if ($faqs_all_split->number_of_rows > MAX_DISPLAY_FAQS_ALL && FAQ_INDEX_PREVIOUS_NEXT_STATUS == 1 && (FAQ_INDEX_PREVIOUS_NEXT_POSITION == 1 or FAQ_INDEX_PREVIOUS_NEXT_POSITION == 3)) {
?>
<div class="faqSplitPageResultsWrapper">
  <div id="allFaqsListingTopNumber" class="faqSplitPagesResult back"><?php echo $faqs_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_FAQS_ALL); ?></div>
  <div id="allFaqsListingTopLinks" class="faqSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $faqs_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
</div>
<br class="clearBoth" />
  <?php
  }
?>

<?php
/**
 * display the faqs
 */
require($template->get_template_dir('/tpl_modules_faqs_all_listing.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_faqs_all_listing.php'); ?>
<br class="clearBoth" />

<?php
  if ($faqs_all_split->number_of_rows > MAX_DISPLAY_FAQS_ALL && FAQ_INDEX_PREVIOUS_NEXT_STATUS == 1 && (FAQ_INDEX_PREVIOUS_NEXT_POSITION == 2 or FAQ_INDEX_PREVIOUS_NEXT_POSITION == 3)) {
?>
<div class="faqSplitPageResultsWrapper">
  <div id="allFaqsListingBottomNumber" class="navSplitPagesResult back"><?php echo $faqs_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_FAQS_ALL); ?></div>
  <div id="allFaqsListingBottomLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $faqs_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
</div>
<br class="clearBoth" />
<?php
  }
?>
</div>