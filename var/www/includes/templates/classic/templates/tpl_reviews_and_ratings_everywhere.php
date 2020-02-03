<?php
/**
 * Page Template
 *
 * Displays Flexible Reviews and Ratings content.<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * Added by rbarbour (ZCAdditions.com), Product Rating & Review Count Module (26)
 */


  // 2P added BOF - Average Product Rating
    $reviews_query = "select count(*) as count from " . TABLE_REVIEWS . " r, "
                                                       . TABLE_REVIEWS_DESCRIPTION . " rd
                       where r.products_id = '" . (int)$prodRandR_id . "'
                       and r.reviews_id = rd.reviews_id
                       and rd.languages_id = '" . (int)$_SESSION['languages_id'] . "'" .
                       $review_status;

    $reviews = $db->Execute($reviews_query);
    $reviews_average_rating_query = "select avg(reviews_rating) as average_rating from " . TABLE_REVIEWS . " r, "
                                                       . TABLE_REVIEWS_DESCRIPTION . " rd
                       where r.products_id = '" . (int)$prodRandR_id . "'
                       and r.reviews_id = rd.reviews_id
                       and rd.languages_id = '" . (int)$_SESSION['languages_id'] . "'" .
                       $review_status;

    $reviews_average_rating = $db->Execute($reviews_average_rating_query);
    // 2P added EOF - Average Product Rating

  if ($reviews->fields['count'] > 1 ) {
    $reviews_count_suffix = TEXT_REVIEWS;
  } else {
    $reviews_count_suffix = TEXT_REVIEW;
}

$stars_image_suffix = str_replace('.', '_', zen_round($reviews_average_rating->fields['average_rating'] * 2, 0) / 2);
$average_rating = zen_round($reviews_average_rating->fields['average_rating'], 2);


$averageRatingResult_1 = '<br class="clearBoth" /><a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . (int)$prodRandR_id) . '">' . TEXT_REVIEW_FRONT . $reviews->fields['count'] . TEXT_REVIEW_TEXT . $reviews_count_suffix . '</a>' . '&nbsp;' . TEXT_CURRENT_REVIEWS_RATING . TEXT_STRONG_FRONT . $average_rating . TEXT_STRONG_END . '<br class="clearBoth" />' . zen_image(DIR_WS_TEMPLATE_IMAGES . TEXT_IMAGE_START . $stars_image_suffix . TEXT_IMAGE_END, sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $average_rating)) . '<br class="clearBoth" />';

$averageRatingResult_2 = '<br class="clearBoth" />' . TEXT_REVIEW_FRONT . $reviews->fields['count'] . TEXT_REVIEWS_TEXT . '&nbsp;' . '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . (int)$prodRandR_id) . '">' . TEXT_RATING_FIRST . '</a>' . '<br class="clearBoth" />';

?>






