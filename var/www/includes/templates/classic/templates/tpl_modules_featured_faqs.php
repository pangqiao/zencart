<?php
/**
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_modules_featured_products.php 2935 2006-02-01 11:12:40Z birdbrain $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_modules_featured_faqs.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 
  $zc_show_featured = false;
  include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_FEATURED_FAQS));
?>

<!-- bof: featured faqs  -->
<?php if ($zc_show_featured == true) { ?>
<h2 id="featuredfaqHeading"><?php echo TABLE_HEADING_FEATURED_FAQS; ?></h2>
<div id="featuredFaqs">
<?php
if (is_array($list_box_contents) > 0 ) {
 for($row=0;$row<sizeof($list_box_contents);$row++) {
    $params = "";
    //if (isset($list_box_contents[$row]['params'])) $params .= ' ' . $list_box_contents[$row]['params'];
?>

<?php
    for($col=0;$col<sizeof($list_box_contents[$row]);$col++) {
      $r_params = "";
      if (isset($list_box_contents[$row][$col]['params'])) $r_params .= ' ' . (string)$list_box_contents[$row][$col]['params'];
      if (isset($list_box_contents[$row][$col]['text'])) {
?>
    <?php echo '<div' . $r_params . '>' . $list_box_contents[$row][$col]['text'] .  '</div>' . "\n"; ?>
<?php
      }
    }
?>
<?php
  }
}
?> 
</div>
<br class="clearBoth" />
<?php } ?>
<!-- eof: featured faqs  -->
