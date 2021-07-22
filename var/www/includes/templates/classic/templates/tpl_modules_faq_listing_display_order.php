<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_modules_listing_display_order.php 3369 2006-04-03 23:09:13Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_modules_faq_listing_display_order.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
?>
<?php
// NOTE: to remove a sort order option add an HTML comment around the option to be removed
?>

<div id="faqSorter">
<label id="faqSort" for="faq-disp-order-sorter"><?php echo TEXT_INFO_SORT_BY; ?></label>
<?php
  echo zen_draw_form('sorter_form', zen_href_link($_GET['main_page']), 'get');
  echo zen_draw_hidden_field('main_page', $_GET['main_page']);
?>
    <select name="disp_order" onChange="this.form.submit();">
<?php if ($disp_order != $disp_order_default) { ?>
    <option value="<?php echo $disp_order_default; ?>" <?php echo ($disp_order == $disp_order_default ? 'selected="selected"' : ''); ?>><?php echo PULL_DOWN_ALL_RESET; ?></option>
<?php } // reset to store default ?>
    <option value="1" <?php echo ($disp_order == '1' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_FAQS_NAME; ?></option>
    <option value="2" <?php echo ($disp_order == '2' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_FAQS_NAME_DESC; ?></option>
    <option value="3" <?php echo ($disp_order == '3' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_FAQS_CATEGORY; ?></option>
    <option value="6" <?php echo ($disp_order == '6' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_FAQS_DATE_DESC; ?></option>
    <option value="7" <?php echo ($disp_order == '7' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_FAQS_DATE; ?></option>
    </select></form></div>

