<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: collect_info.php 19330 2011-08-07 06:32:56Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @collect_info.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
    $parameters = array('faqs_contact_name' => '',
                       'faqs_answer' => '',
                       'faqs_id' => '',
                       'faqs_date_added' => '',
                       'faqs_last_modified' => '',
                       'faqs_status' => '',
                       'faqs_sort_order' => '0',
                       'master_faq_categories_id' => ''
                       );

    $pInfo = new objectInfo($parameters);

    if (isset($_GET['pID']) && empty($_POST)) {
      $faq = $db->Execute("select pd.faqs_name, pd.faqs_answer, p.faqs_id,
                              p.faqs_date_added, p.faqs_last_modified,
                              p.faqs_status, p.faqs_sort_order,
                              p.master_faq_categories_id
                              from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd
                              where p.faqs_id = '" . (int)$_GET['pID'] . "'
                              and p.faqs_id = pd.faqs_id
                              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");

      $pInfo->objectInfo($faq->fields);
    } elseif (zen_not_null($_POST)) {
      $pInfo->objectInfo($_POST);
      $faqs_name = $_POST['faqs_name'];
      $faqs_answer = $_POST['faqs_answer'];
    }
    $languages = zen_get_languages();
    if (!isset($pInfo->faqs_status)) $pInfo->faqs_status = '1';
    switch ($pInfo->faqs_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
      break;
    }
// set to off if faq_categories_status is off and new faq or existing faqs_status is off
    if (zen_get_faq_categories_status($current_faq_category_id) == '0' and $pInfo->faqs_status != '1') {
      $pInfo->faqs_status = 1;
      $in_status = false;
      $out_status = true;
    }
?>
<?php
$form_action = (isset($_GET['pID'])) ? 'update_faq' : 'insert_faq';
$type_faq_admin_handler = faq;
echo zen_draw_form($form_action, $type_faq_admin_handler, 'fcPath=' . $fcPath . '&faq_type=1' . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=' . $form_action . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'enctype="multipart/form-data"'); ?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf(TEXT_NEW_FAQ, zen_output_generated_faq_category_path($current_faq_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
// hidden fields not changeable on faqs page
echo zen_draw_hidden_field('master_faq_categories_id', $pInfo->master_faq_categories_id);
?>
          <tr>
            <td colspan="2" class="main" align="left"><?php echo (zen_get_faq_categories_status($current_faq_category_id) == '0' ? TEXT_FAQ_CATEGORIES_STATUS_INFO_OFF : '') . ($out_status == true ? ' ' . TEXT_FAQS_STATUS_INFO_OFF : ''); ?></td>
          </tr>
          <tr>
          <tr>
            <td class="main"><?php echo TEXT_FAQS_STATUS; ?></td>
                        <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . 
						zen_draw_radio_field('faqs_status', '1', $in_status) . '&nbsp;' . TEXT_FAQ_AVAILABLE . '&nbsp;' . 
						zen_draw_radio_field('faqs_status', '0', $out_status) . '&nbsp;' . TEXT_FAQ_NOT_AVAILABLE; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_FAQS_NAME; ?></td>
            <td class="main"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('faqs_name[' . $languages[$i]['id'] . ']', (isset($faqs_name[$languages[$i]['id']]) ? stripslashes($faqs_name[$languages[$i]['id']]) : zen_get_faqs_name($pInfo->faqs_id, $languages[$i]['id'])), zen_set_field_length(TABLE_FAQS_DESCRIPTION, 'faqs_name')); ?></td>
          </tr>
<?php
    }
?>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_FAQS_ANSWER; ?></td>
            <td colspan="2"><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" width="25" valign="top"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main" width="100%">
<?php
          echo zen_draw_textarea_field('faqs_answer[' . $languages[$i]['id'] . ']', 'soft', '100%', '15', htmlspecialchars((isset($faqs_answer[$languages[$i]['id']])) ? stripslashes($faqs_answer[$languages[$i]['id']]) : zen_get_faqs_answer($pInfo->faqs_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE));
?>
        </td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FAQS_SORT_ORDER; ?></td>
            <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_input_field('faqs_sort_order', $pInfo->faqs_sort_order); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right">
<?php
      echo zen_draw_hidden_field('faqs_date_added', (zen_not_null($pInfo->faqs_date_added) ? $pInfo->faqs_date_added : date('Y-m-d')));
	  if (isset($_GET['pID'])) {
        echo zen_image_submit('button_update.gif', IMAGE_UPDATE);
      } else {
        echo zen_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      echo '&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?>
        </td>
      </tr>
    </table></form>
