<?php
/**
 * Sitemap XML
 *
 * @package Sitemap XML
 * @copyright Copyright 2005-2012 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: sitemapxml_products.php, v 3.2.5 31.05.2012 14:52:55 AndrewBerezin $
 */
define('SITEMAPXML_PRODUCTS_IMAGES_SIZE', 'large');
define('SITEMAPXML_PRODUCTS_IMAGES_ADDITIONAL', 'false');
define('SITEMAPXML_PRODUCTS_IMAGES_FUNCTION', 'true');

echo '<h3>' . TEXT_HEAD_PRODUCTS . '</h3>';
// BOF hideCategories
if ($sitemapXML->dbTableExist('TABLE_HIDE_CATEGORIES')) {
  $from = " LEFT JOIN " . TABLE_HIDE_CATEGORIES . " h ON (p.master_categories_id = h.categories_id)";
  $where = " AND (h.visibility_status < 2 OR h.visibility_status IS NULL)";
} else {
  $from = '';
  $where = '';
}
// EOF hideCategories
$catsArray = array();
$last_date = $db->Execute("SELECT MAX(GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0))) AS last_date
                           FROM " . TABLE_PRODUCTS . " p
                           WHERE p.products_status = '1'");
$table_status = $db->Execute("SHOW TABLE STATUS LIKE '" . TABLE_PRODUCTS . "'");
$last_date = max($table_status->fields['Update_time'], $last_date->fields['last_date']);
if ($sitemapXML->SitemapOpen('products', $last_date)) {
  $file_main_product_image = DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE);
  $file_additional_images = DIR_WS_MODULES . zen_get_module_directory('additional_images.php');
  $select = '';
  $xtra = '';
  if (SITEMAPXML_PRODUCTS_IMAGES == 'true') {
    $select = ", p.products_image, pd.products_name";
  }
  $products = $db->Execute("SELECT p.products_id, p.master_categories_id, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, '0001-01-01 00:00:00')) AS last_date, p.products_sort_order AS priority, pd.language_id" . $select . "
                            FROM " . TABLE_PRODUCTS . " p
                              LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)" . $from . "
                            WHERE p.products_status = '1'" . $where . "
                              AND pd.language_id IN (" . $sitemapXML->getLanguagesIDs() . ") " .
                            (SITEMAPXML_PRODUCTS_ORDERBY != '' ? "ORDER BY " . SITEMAPXML_PRODUCTS_ORDERBY : ''));
  $sitemapXML->SitemapSetMaxItems($products->RecordCount());
  while (!$products->EOF) {
    $xtra = '';
    if (SITEMAPXML_PRODUCTS_IMAGES == 'true' && zen_not_null($products->fields['products_image']) && is_file(DIR_FS_CATALOG . DIR_WS_IMAGES . $products->fields['products_image'])) {
      $products_image = $products->fields['products_image'];
      $products_name = $products->fields['products_name'];
      $_GET['products_id'] = $products->fields['products_id'];
      require($file_main_product_image);
      if (SITEMAPXML_PRODUCTS_IMAGES_ADDITIONAL == 'true') {
        $flag_show_product_info_additional_images = 1;
        require($file_additional_images);
        echo '<pre>';var_dump(array('$images_array' => $images_array, '$list_box_contents' => $list_box_contents, ));echo '</pre>';
      }
      unset($_GET['products_id']);
      switch (SITEMAPXML_PRODUCTS_IMAGES_SIZE) {
        case 'small':
          $img = $products_image;
          $width = SMALL_IMAGE_WIDTH;
          $height = SMALL_IMAGE_HEIGHT;
          break;
        case 'medium':
          $img = $products_image_medium;
          $width = MEDIUM_IMAGE_WIDTH;
          $height = MEDIUM_IMAGE_HEIGHT;
          break;
        case 'large':
        default:
          $img = $products_image_large;
          $width = '';
          $height = '';
          break;
      }
      if (SITEMAPXML_PRODUCTS_IMAGES_FUNCTION == 'true') {
        preg_match('@src="([^"]*)"@', zen_image($img, '', $width, $height), $image_src);
        $img = $image_src[1];
      } else {
        $img = DIR_WS_IMAGES . $img;
      }
      $images = array(
                      array(
                           'file' => $img,
                           'title' => $products->fields['products_name'],
                           ),
                     );

      $xtra = $sitemapXML->imagesTags($images, SITEMAPXML_PRODUCTS_IMAGES_CAPTION, SITEMAPXML_PRODUCTS_IMAGES_LICENSE);
    } else {
      $xtra = '';
    }

    if (SITEMAPXML_PRODUCTS_USE_CPATH == 'true') {
      if (!isset($catsArray[$products->fields['master_categories_id']])) {
        $catsArray[$products->fields['master_categories_id']] = zen_get_generated_category_path_rev($products->fields['master_categories_id']);
      }
      $cPath_parm = 'cPath=' . $catsArray[$products->fields['master_categories_id']] . '&';
    } else {
      $cPath_parm = '';
    }
    $sitemapXML->writeItem(zen_get_info_page($products->fields['products_id']), $cPath_parm . 'products_id=' . $products->fields['products_id'], $products->fields['language_id'], $products->fields['last_date'], SITEMAPXML_PRODUCTS_CHANGEFREQ, $xtra);
    $products->MoveNext();
  }
  $sitemapXML->SitemapClose();
}
unset($products);
unset($catsArray);

// EOF