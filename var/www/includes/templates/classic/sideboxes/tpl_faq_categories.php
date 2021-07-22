<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_categories.php 4162 2006-08-17 03:55:02Z ajeh $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_faq_categories.php updated 2012-10-12 to be v1.5 compatible kamelion0927
 */
 
  $content = "";
  
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";
  for ($i=0;$i<sizeof($box_faq_categories_array);$i++) {
    switch(true) {
// to make a specific category stand out define a new class in the stylesheet example: A.category-holiday
// uncomment the select below and set the cPath=3 to the cPath= your_categories_id
// many variations of this can be done
//      case ($box_categories_array[$i]['path'] == 'cPath=3'):
//        $new_style = 'category-holiday';
//        break;
      case ($box_faq_categories_array[$i]['top'] == 'true'):
        $new_style = 'category-top';
        break;
      case ($box_faq_categories_array[$i]['has_sub_cat']):
        $new_style = 'category-subs';
        break;
      default:
        $new_style = 'category-products';
      }
     if ($box_faq_categories_array[$i]['top'] != 'true' and SHOW_FAQ_CATEGORIES_SUBFAQ_CATEGORIES_ALWAYS != 1) {
      } else {
      $content .= '<a class="' . $new_style . '" href="' . zen_href_link(FILENAME_FAQS, $box_faq_categories_array[$i]['path']) . '">';

      if ($box_faq_categories_array[$i]['current']) {
        if ($box_faq_categories_array[$i]['has_sub_cat']) {
          $content .= '<span class="category-subs-parent">' . $box_faq_categories_array[$i]['name'] . '</span>';
        } else {
          $content .= '<span class="category-subs-selected">' . $box_faq_categories_array[$i]['name'] . '</span>';
        }
      } else {
        $content .= $box_faq_categories_array[$i]['name'];
      }

      if (SHOW_FAQ_COUNTS == 'true') {
        if ((FAQ_CATEGORIES_COUNT_ZERO == '1' and $box_faq_categories_array[$i]['count'] == 0) or $box_faq_categories_array[$i]['count'] >= 1) {
          $content .= FAQ_CATEGORIES_COUNT_PREFIX . $box_faq_categories_array[$i]['count'] . FAQ_CATEGORIES_COUNT_SUFFIX;
        }
      }
      $content .= '</a>';
      $content .= '<br />';
    }
  }
$content .= '</div>' . "\n";
?>