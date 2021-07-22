<?php
/**
 * Page Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version GIT: $Id: Author: DrByte  Sun Aug 19 09:47:29 2012 -0400 Modified in v1.5.1 $
 */
?>
<div class="centerColumn" id="reviewsWrite">
  <h1 id="reviewsWriteHeading"><?php echo $products_name . $products_model; ?></h1>
<?php if ($messageStack->size('review_text') > 0) echo $messageStack->output('review_text'); ?>

<?php echo zen_draw_form('product_reviews_write', zen_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&products_id=' . $_GET['products_id'], ($_SESSION['customer_id']) ? 'SSL' : 'NONSSL'), 'post', 'onsubmit="return checkForm(product_reviews_write);"'); ?>
<!--bof Main Product Image -->
      <?php
        if (zen_not_null($products_image)) {
    ?>
    <div id="reviewWriteMainImage" class="centeredContent back">
<?php
   require($template->get_template_dir('/tpl_modules_main_product_image.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_main_product_image.php'); ?>
</div>
<?php
        }
      ?>
<!--eof Main Product Image-->
<div class="buttonWrapper">
  <div id="reviewsWriteProductPageLink" class="buttonRow forward"><?php echo '<a href="' . zen_href_link(zen_get_info_page($_GET['products_id']), zen_get_all_get_params()) . '">' . zen_image_button(BUTTON_IMAGE_GOTO_PROD_DETAILS_default , BUTTON_GOTO_PROD_DETAILS_ALT) . '</a>'; ?>
</div>  
</div>

<?php if (zen_not_null($products_price)) { ?>
<h2 id="reviewsWritePrice"><?php echo $products_price; ?></h2>
<?php
}
if ($_SESSION['customer_id']) {
?> 
  <h3 id="reviewsWriteReviewer">
<?php 
  echo SUB_TITLE_FROM . zen_output_string_protected($customer->fields['customers_firstname'] . ' ' . $customer->fields['customers_lastname']) . '</h3>';
}
?>
<div id="textAreaReviews"><?php echo sprintf(SUB_TITLE_REVIEW, $customer->fields['customers_firstname'], $customer->fields['customers_lastname']); ?></div>
<?php if (TEXT_NO_HTML != '' || REVIEWS_APPROVAL == '1') { ?>
<div id="reviewsWriteReviewsNotice" class="notice">
 <?php echo '<b>' . TEXT_NOTES . '</b>'; ?>
  <ul>
    <?php echo (TEXT_NO_HTML != '' ? '<li>' . TEXT_NO_HTML . '</li>' : ''); ?>
    <?php echo (REVIEWS_APPROVAL == '1' ? '<li>' . TEXT_APPROVAL_REQUIRED . '</li>' : ''); ?>
  </ul>
</div>
<br class="clearBoth" />
<?php } ?>
<hr/>
<?php
if (!$_SESSION['customer_id']) { 
?>
  <div id="reviewerName">
    <label id="textAreaName" for="review_name"><?php echo TEXT_REVIEW_NAME; ?></label>
    <input type="text" name="review_name" size="33" maxlength="62" id="review_name" />
  </div>
<?php
}
?>
<br/>
<div id="reviewsWriteReviewsRate" class="center"><?php echo SUB_TITLE_RATING; ?></div>

<div class="ratingRow">
<?php echo zen_draw_radio_field('rating', '1', '', 'id="rating-1"'); ?>
<?php echo '<label class="" for="rating-1">' . zen_image($template->get_template_dir(OTHER_IMAGE_REVIEWS_RATING_STARS_ONE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . OTHER_IMAGE_REVIEWS_RATING_STARS_ONE, OTHER_REVIEWS_RATING_STARS_ONE_ALT) . '</label> '; ?>

<?php echo zen_draw_radio_field('rating', '2', '', 'id="rating-2"'); ?>
<?php echo '<label class="" for="rating-2">' . zen_image($template->get_template_dir(OTHER_IMAGE_REVIEWS_RATING_STARS_TWO, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . OTHER_IMAGE_REVIEWS_RATING_STARS_TWO, OTHER_REVIEWS_RATING_STARS_TWO_ALT) . '</label>'; ?>

<?php echo zen_draw_radio_field('rating', '3', '', 'id="rating-3"'); ?>
<?php echo '<label class="" for="rating-3">' . zen_image($template->get_template_dir(OTHER_IMAGE_REVIEWS_RATING_STARS_THREE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . OTHER_IMAGE_REVIEWS_RATING_STARS_THREE, OTHER_REVIEWS_RATING_STARS_THREE_ALT) . '</label>'; ?>

<?php echo zen_draw_radio_field('rating', '4', '', 'id="rating-4"'); ?>
<?php echo '<label class="" for="rating-4">' . zen_image($template->get_template_dir(OTHER_IMAGE_REVIEWS_RATING_STARS_FOUR, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . OTHER_IMAGE_REVIEWS_RATING_STARS_FOUR, OTHER_REVIEWS_RATING_STARS_FOUR_ALT) . '</label>'; ?>

<?php echo zen_draw_radio_field('rating', '5', '', 'id="rating-5"'); ?>
<?php echo '<label class="" for="rating-5">' . zen_image($template->get_template_dir(OTHER_IMAGE_REVIEWS_RATING_STARS_FIVE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . OTHER_IMAGE_REVIEWS_RATING_STARS_FIVE, OTHER_REVIEWS_RATING_STARS_FIVE_ALT) . '</label>'; ?>
</div>

<label id="textAreaReviews" for="review-text"><?php echo TEXT_REVIEW_TEXT; ?></label>
<?php echo zen_draw_textarea_field('review_text', 60, 5, '', 'id="review-text"'); ?>
<?php echo zen_draw_input_field('should_be_empty', '', ' size="60" id="RAS" style="visibility:hidden; display:none;" autocomplete="off"'); ?>

<br class="clearBoth" />

<div id="buttonSubmit"><?php echo zen_image_submit(BUTTON_IMAGE_SUBMIT, BUTTON_SUBMIT_ALT); ?></div>
</form>
</div>
