<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: category_product_listing.php 18695 2011-05-04 05:24:19Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_category_faq_listing.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  $faq_manager_link_id = $db->Execute("select configuration_group_id from " . TABLE_CONFIGURATION_GROUP . "
            where configuration_group_title = 'FAQ Manager'");
  $faq_manager_link = $faq_manager_link_id->fields['configuration_group_id'];
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?>&nbsp;-&nbsp;<?php echo zen_output_generated_faq_category_path($current_faq_category_id); ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" align="right">
<?php
    echo zen_draw_form('goto', FILENAME_FAQ_CATEGORIES, '', 'get');
	echo zen_hide_session_id();
    echo HEADING_TITLE_GOTO . ' ' . zen_draw_pull_down_menu('fcPath', zen_get_faq_category_tree(), $current_faq_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
?>

                </td>
              </tr>
			  <tr>
			    <td class="pageHeading"><?php echo zen_draw_separator('pixel_trans.gif', 1, 5); ?></td>
			  </tr>
			  <tr>
				<td class="pageHeading" align="right"><?php echo '<a href="' . zen_href_link(FILENAME_FAQ_MANAGER, 'gID=' . $faq_manager_link) . '">' . zen_image_button('faq_manager_links_categories.gif', TEXT_FAQ_CONFIGURATION) . '</a>' . '&nbsp;&nbsp;&nbsp;' . '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS) . '">' . zen_image_button('faq_manager_links_featured.gif', TEXT_FEATURED_FAQS) . '</a>'; ?></td>
				</td>
			  </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
<?php  if ($action == '') { ?>
                <td class="dataTableHeadingContent" width="30" align="center"><?php echo TABLE_HEADING_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FAQ_CATEGORIES_FAQS; ?></td>
                <td class="dataTableHeadingContent" align="left"></td>
                <td class="dataTableHeadingContent" align="right"></td>
                <td class="dataTableHeadingContent" align="right"></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_QUANTITY; ?>&nbsp;&nbsp;&nbsp;</td>
                <td class="dataTableHeadingContent" width="50" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_FAQ_CATEGORIES_SORT_ORDER; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
<?php  } // action == '' ?>
              </tr>
<?php
    $faq_categories_count = 0;
    $rows = 0;
      $faq_categories = $db->Execute("select c.faq_categories_id, cd.faq_categories_name, cd.faq_categories_description,
                                         c.parent_id, c.sort_order, c.date_added, c.last_modified,
                                         c.faq_categories_status
                                  from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                  where c.parent_id = '" . (int)$current_faq_category_id . "'
                                  and c.faq_categories_id = cd.faq_categories_id
                                  and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                  order by c.sort_order, cd.faq_categories_name");

    while (!$faq_categories->EOF) {
      $faq_categories_count++;
      $rows++;

      if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $faq_categories->fields['faq_categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
        $cInfo = new objectInfo($faq_categories->fields);
      }

      if (isset($cInfo) && is_object($cInfo) && ($faq_categories->fields['faq_categories_id'] == $cInfo->faq_categories_id)) {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\''  . zen_href_link(FILENAME_FAQ_CATEGORIES, zen_get_faq_category_path($faq_categories->fields['faq_categories_id'])) . '\'">' . "\n";
      } else {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_FAQ_CATEGORIES, zen_get_faq_category_path($faq_categories->fields['faq_categories_id'])) . '\'">' . "\n";
      }
?>
<?php if ($action == '') { ?>
                <td class="dataTableContent" width="30" align="center"><?php echo $faq_categories->fields['faq_categories_id']; ?></td>
                <td class="dataTableContent"><?php echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, zen_get_faq_category_path($faq_categories->fields['faq_categories_id'])) . '">' . zen_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $faq_categories->fields['faq_categories_name'] . '</b>'; ?></td>
                <td class="dataTableContent" align="center">&nbsp;</td>
                <td class="dataTableContent" align="right">&nbsp;<?php // echo zen_get_faqs_sale_discount('', $faq_categories->fields['faq_categories_id'], true); ?></td>
                <td class="dataTableContent" align="center">&nbsp;</td>
                <td class="dataTableContent" align="right" valign="bottom">
                  <?php
                    // show counts
                    $total_faqs = zen_get_faqs_to_faq_categories($faq_categories->fields['faq_categories_id'], true);
                    $total_faqs_on = zen_get_faqs_to_faq_categories($faq_categories->fields['faq_categories_id'], false);
                    echo $total_faqs_on . TEXT_FAQS_STATUS_ON_OF . $total_faqs . TEXT_FAQS_STATUS_ACTIVE;
                  ?>
                  &nbsp;&nbsp;
                </td>
                <td class="dataTableContent" width="50" align="center">
<?php
      if ($faq_categories->fields['faq_categories_status'] == '1') {
        echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'action=setflag_faq_categories&flag=0&cID=' . $faq_categories->fields['faq_categories_id'] . '&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON) . '</a>';
      } else {
        echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'action=setflag_faq_categories&flag=1&cID=' . $faq_categories->fields['faq_categories_id'] . '&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF) . '</a>';
      }
      if (zen_get_faqs_to_faq_categories($faq_categories->fields['faq_categories_id'], true, 'faqs_active') == 'true') {
        echo '&nbsp;&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_FAQ_CAT_LINKED);
      }
?>
                </td>
                <td class="dataTableContent" align="center"><?php echo $faq_categories->fields['sort_order']; ?></td>
                <td class="dataTableContent" width="100" align="center">
                  <?php echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $faq_categories->fields['faq_categories_id'] . '&action=edit_faq_category') . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>'; ?>
                  <?php echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $faq_categories->fields['faq_categories_id'] . '&action=delete_faq_category') . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>'; ?>
                  <?php echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $faq_categories->fields['faq_categories_id'] . '&action=move_faq_category') . '">' . zen_image(DIR_WS_IMAGES . 'icon_move.gif', ICON_MOVE) . '</a>'; ?>
                </td>
<?php } // action == '' ?>
              </tr>
<?php
      $faq_categories->MoveNext();
    }

    $faqs_count = 0;
    $faqs_query_raw = ("select p.*, pd.*, p2c.*
                               from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                               where p.faqs_id = pd.faqs_id
                               and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                               and p.faqs_id = p2c.faqs_id
                               and p2c.faq_categories_id = '" . (int)$current_faq_category_id . "'
                               order by p.faqs_sort_order, pd.faqs_name");

// Split Page
// reset page when page is unknown
if (($_GET['page'] == '1' or $_GET['page'] == '') and $_GET['pID'] != '') {
  $old_page = $_GET['page'];
  $check_page = $db->Execute($faqs_query_raw);
  if ($check_page->RecordCount() > MAX_DISPLAY_RESULTS_FAQ_CATEGORIES) {
    $check_count=1;
    while (!$check_page->EOF) {
      if ($check_page->fields['faqs_id'] == $_GET['pID']) {
        break;
      }
      $check_count++;
      $check_page->MoveNext();
    }
    $_GET['page'] = round((($check_count/MAX_DISPLAY_RESULTS_FAQ_CATEGORIES)+(fmod($check_count,MAX_DISPLAY_RESULTS_FAQ_CATEGORIES) !=0 ? .5 : 0)),0);
    $page = $_GET['page'];
    if ($old_page != $_GET['page']) {
    }
  } else {
    $_GET['page'] = 1;
  }
}
    $faqs_split = new splitPageResults($_GET['page'], MAX_DISPLAY_RESULTS_FAQ_CATEGORIES, $faqs_query_raw, $faqs_query_numrows);
    $faqs = $db->Execute($faqs_query_raw);
// Split Page
    while (!$faqs->EOF) {
      $faqs_count++;
      $rows++;

      if ((!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['pID']) && ($_GET['pID'] == $faqs->fields['faqs_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
// can't figure out why I have to leave this here if we aren't including faqs reviews, but you can't delete/move/copy a faq without it so I changed it to pull something from a table that still exsists with the re-do of this mod.
        $faq_reviews = $db->Execute("select faqs_answer
                                     from " . TABLE_FAQS_DESCRIPTION . "
                                     where faqs_id = '" . (int)$faqs->fields['faqs_id'] . "'");
        $pInfo_array = array_merge($faqs->fields, $faq_reviews->fields);
        $pInfo = new objectInfo($pInfo_array);
      }

// Split Page
      $type_handler = faq;
      if (isset($pInfo) && is_object($pInfo) && ($faqs->fields['faqs_id'] == $pInfo->faqs_id) ) {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link($type_handler , 'page=' . $_GET['page'] . '&faq_type=1' . '&fcPath=' . $fcPath . '&pID=' . $faqs->fields['faqs_id'] . '&action=new_faq') . '\'">' . "\n";
      } else {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link($type_handler , 'page=' . $_GET['page'] . '&faq_type=1' . '&fcPath=' . $fcPath . '&pID=' . $faqs->fields['faqs_id'] . '&action=new_faq') . '\'">' . "\n";
      }
// Split Page
?>
                <td class="dataTableContent" width="50" align="center"><?php echo $faqs->fields['faqs_id']; ?></td>
                <td class="dataTableContent"><?php echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $faqs->fields['faqs_id'] . '&action=new_faq_preview&read=only' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $faqs->fields['faqs_name']; ?></td>
                <td class="dataTableContent"></td>
                <td colspan="2" class="dataTableContent" align="right"></td>
                <td class="dataTableContent" align="right"><?php echo $faqs->fields['faqs_quantity']; ?></td>
                <td class="dataTableContent" width="50" align="center">
<?php
      if ($faqs->fields['faqs_status'] == '1') {
        echo zen_draw_form('setflag_faqs', FILENAME_FAQ_CATEGORIES, 'action=setflag&pID=' . $faqs->fields['faqs_id'] . '&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''));?>
        <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_green_on.gif" title="<?php echo IMAGE_ICON_STATUS_ON; ?>" />
        <input type="hidden" name="flag" value="0" />
        </form>
<?php
      } else {
        echo zen_draw_form('setflag_products', FILENAME_FAQ_CATEGORIES, 'action=setflag&pID=' . $faqs->fields['faqs_id'] . '&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''));?>
        <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_red_on.gif" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>"/>
        <input type="hidden" name="flag" value="1" />
        </form>
<?php
      }
      if (zen_get_faq_is_linked($faqs->fields['faqs_id']) == 'true') {
        echo '&nbsp;&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_FAQ_LINKED) . '<br>';
      }
?>
                </td>
<?php if ($action == '') { ?>
                <td class="dataTableContent" align="center"><?php echo $faqs->fields['faqs_sort_order']; ?></td>
                <td class="dataTableContent" width="100" align="center">
        <?php echo '<a href="' . zen_href_link($type_handler, 'fcPath=' . $fcPath . '&faq_type=1' . '&pID=' . $faqs->fields['faqs_id']  . '&action=new_faq' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>'; ?>
        <?php echo '<a href="' . zen_href_link($type_handler, 'fcPath=' . $fcPath . '&faq_type=1' . '&pID=' . $faqs->fields['faqs_id'] . '&action=delete_faq' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>'; ?>
        <?php echo '<a href="' . zen_href_link($type_handler, 'fcPath=' . $fcPath . '&faq_type=1' . '&pID=' . $faqs->fields['faqs_id'] . '&action=move_faq' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_move.gif', ICON_MOVE) . '</a>'; ?>
        <?php echo '<a href="' . zen_href_link($type_handler, 'fcPath=' . $fcPath . '&faq_type=1' . '&pID=' . $faqs->fields['faqs_id'] .'&action=copy_to' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_copy_to.gif', ICON_COPY_TO) . '</a>'; ?>
<?php
//} // EOF: Attribute commands
?>
<?php } // action == '' ?>

                </td>
              </tr>
<?php
      $faqs->MoveNext();
    }

    $fcPath_back = '';
    if (sizeof($fcPath_array) > 0) {
      for ($i=0, $n=sizeof($fcPath_array)-1; $i<$n; $i++) {
        if (empty($fcPath_back)) {
          $fcPath_back .= $fcPath_array[$i];
        } else {
          $fcPath_back .= '_' . $fcPath_array[$i];
        }
      }
    }

    $fcPath_back = (zen_not_null($fcPath_back)) ? 'fcPath=' . $fcPath_back . '&' : '';
?>
<?php if ($action == '') { ?>
    <tr>
      <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td align="left" class="smallText">
	    <?php if (sizeof($fcPath_array) > 0) echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, $fcPath_back . 'cID=' . $current_faq_category_id) . '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;';
	    
		if (zen_output_generated_faq_category_path($current_faq_category_id) == TEXT_TOP) {	
	       if (!isset($_GET['search'])) echo '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&action=new_faq_category') . '">' . zen_image_button('button_new_faq_category.gif', IMAGE_NEW_FAQ_CATEGORY) . '</a>&nbsp;'; 
		}?>

        <?php if ($zc_skip_faqs == false) { ?>
          <form name="newfaq" action="<?php echo zen_href_link(FILENAME_FAQ_CATEGORIES, '', 'NONSSL'); ?>" method = "get"><?php echo zen_image_submit('button_new_faq.gif', IMAGE_NEW_FAQ); ?>
          <input type="hidden" name="fcPath" value="<?php echo $fcPath; ?>">
          <input type="hidden" name="action" value="new_faq">
      </form>
<?php
  } else {
?>
<?php } // hide has cats ?>
          &nbsp;</td>
                  </tr>
                </table></td>
              </tr>
<?php } // turn off when editing ?>
            </table></td>
