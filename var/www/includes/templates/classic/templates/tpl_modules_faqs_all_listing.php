<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_modules_products_all_listing.php 6096 2007-04-01 00:43:21Z ajeh $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_modules_faqs_all_listing.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
<?php
  $group_id = zen_get_configuration_key_value('FAQ_LISTS_GROUP_ID_ALL');
  if ($faqs_all_split->number_of_rows > 0) {
    $faqs_all = $db->Execute($faqs_all_split->sql_query);
	$category_displayed = '';
    while (!$faqs_all->EOF) {
      $fcPath= zen_get_faq_path($faqs_all->fields['faqs_id']);
	  if ($category_displayed != $faqs_all->fields['faq_categories_name']) {
	   $category_displayed = $faqs_all->fields['faq_categories_name'];
	   echo "<tr><td colspan='3'><h3 class='faq_cat_title'>" . $category_displayed . "</h3></td></tr>";
	  }
      if (FAQ_ALL_LIST_NAME != '0') {
        $display_faqs_name = '<a href="' . zen_href_link('faq_info', 'fcPath=' . $fcPath . '&faqs_id=' . $faqs_all->fields['faqs_id']) . '">' . $faqs_all->fields['faqs_name'] . '</a>' . str_repeat('<br clear="all" />', substr(FAQ_ALL_LIST_NAME, 3, 1));
      } else {
        $display_faqs_name = '';
      }
?>
            <tr>
            <?php
                $disp_sort_order = $db->Execute("select configuration_key, configuration_value from " . TABLE_CONFIGURATION . " where configuration_group_id='" . $group_id . "' and (configuration_value >= 1000 and configuration_value <= 1999) order by LPAD(configuration_value,11,0)");
                while (!$disp_sort_order->EOF) { ?>
		        <td valign="top" class="faqQuestion" align="left">
				<?php
                  if ($disp_sort_order->fields['configuration_key'] == 'FAQ_ALL_LIST_NAME') {
                    echo $display_faqs_name;
                  }
                $disp_sort_order->MoveNext();
                }
              ?>
              <td valign="top" class="faqQuestion" align="left">
              <?php
                $disp_sort_order = $db->Execute("select configuration_key, configuration_value from " . TABLE_CONFIGURATION . " where configuration_group_id='" . $group_id . "' and (configuration_value >= 2000 and configuration_value <= 2999) order by LPAD(configuration_value,11,0)");
                while (!$disp_sort_order->EOF) {
                  if ($disp_sort_order->fields['configuration_key'] == 'FAQ_ALL_LIST_NAME') {
                    echo $display_faqs_name;
                  }
                  $disp_sort_order->MoveNext();
                }
    		  ?>
                </td>
              </tr>
<?php
      $faqs_all->MoveNext();
    }
  } else {
?>
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NO_FAQS; ?></td>
          </tr>
<?php
  }
?> 
</table>
