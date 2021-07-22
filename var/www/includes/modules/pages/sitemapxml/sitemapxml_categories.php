<?php
/**
 * Sitemap XML
 *
 * @package Sitemap XML
 * @copyright Copyright 2005-2012 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @link hideCategories http://www.zen-cart.com/downloads.php?do=file&id=254
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: sitemapxml_categories.php, v 3.2.2 07.05.2012 19:12 AndrewBerezin $
 */

echo '<h3>' . TEXT_HEAD_CATEGORIES . '</h3>';
// BOF hideCategories
if ($sitemapXML->dbTableExist('TABLE_HIDE_CATEGORIES')) {
  $from = " LEFT JOIN " . TABLE_HIDE_CATEGORIES . " h ON (c.categories_id = h.categories_id)";
  $where = " AND (h.visibility_status < 2 OR h.visibility_status IS NULL)";
} else {
  $from = '';
  $where = '';
}
// EOF hideCategories
$last_date = $db->Execute("SELECT MAX(GREATEST(IFNULL(c.date_added, 0), IFNULL(c.last_modified, 0))) AS last_date
                           FROM " . TABLE_CATEGORIES . " c
                           WHERE c.categories_status = '1'");
$table_status = $db->Execute("SHOW TABLE STATUS LIKE '" . TABLE_CATEGORIES . "'");
$last_date = max($table_status->fields['Update_time'], $last_date->fields['last_date']);
$select = '';
$xtra = '';
if ($sitemapXML->SitemapOpen('categories', $last_date)) {
  if (SITEMAPXML_CATEGORIES_IMAGES == 'true') {
    $select = ", c.categories_image, cd.categories_name";
  }
  $categories = $db->Execute("SELECT c.categories_id, GREATEST(c.date_added, IFNULL(c.last_modified, '0001-01-01 00:00:00')) AS last_date, c.sort_order AS priority, cd.language_id" . $select . "
                              FROM " . TABLE_CATEGORIES . " c
                                LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON (cd.categories_id = c.categories_id)" . $from . "
                              WHERE c.categories_status = '1'" . $where . "
                                AND cd.language_id IN (" . $sitemapXML->getLanguagesIDs() . ") " .
                              (SITEMAPXML_CATEGORIES_ORDERBY != '' ? "ORDER BY " . SITEMAPXML_CATEGORIES_ORDERBY : ''));
  $sitemapXML->SitemapSetMaxItems($categories->RecordCount());
  while (!$categories->EOF) {
    if (SKIP_SINGLE_PRODUCT_CATEGORIES=='True') {
      $products = $db->Execute("SELECT COUNT(*) AS total
                                FROM " . TABLE_PRODUCTS . " p
                                  LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON (p.products_id = p2c.products_id)
                                WHERE p.products_status = '1'
                                  AND p2c.categories_id = '" . (int)$categories->fields['categories_id'] . "'");
    } else {
      $products->fields['total'] = 2;
    }
    if ($products->fields['total'] != 1) {
      $xtra = '';
      if (SITEMAPXML_CATEGORIES_IMAGES == 'true' && zen_not_null($categories->fields['categories_image']) && is_file(DIR_FS_CATALOG . DIR_WS_IMAGES . $categories->fields['categories_image'])) {
        $images = array(
                        array(
                             'file' => DIR_WS_IMAGES . $categories->fields['categories_image'],
                             'title' => $categories->fields['categories_name'],
                             ),
                       );
        $xtra = $sitemapXML->imagesTags($images, SITEMAPXML_CATEGORIES_IMAGES_CAPTION, SITEMAPXML_CATEGORIES_IMAGES_LICENSE);
      } else {
        $xtra = '';
      }
      $sitemapXML->writeItem(FILENAME_DEFAULT, 'cPath=' . $sitemapXML->GetFullcPath($categories->fields['categories_id']), $categories->fields['language_id'], $categories->fields['last_date'], SITEMAPXML_CATEGORIES_CHANGEFREQ, $xtra);
    }
    $categories->MoveNext();
  }
  $sitemapXML->SitemapClose();
}
unset($categories);

// EOF