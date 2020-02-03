<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: product.php 19330 2011-08-07 06:32:56Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . 'english/faq_categories.php');
  
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (zen_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if (isset($_GET['pID'])) {
            zen_set_faq_status($_GET['pID'], $_GET['flag']);
          }
        }

        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $_GET['fcPath'] . '&pID=' . $_GET['pID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        break;

      case 'delete_faq_confirm':
      $delete_linked = 'true';
      if ($_POST['delete_linked'] == 'delete_linked_no') {
        $delete_linked = 'false';
      } else {
        $delete_linked = 'true';
      }
		$faqs->fields['faqs_id'] = $pInfo->faqs_id;
        require(DIR_WS_MODULES . 'faq' . '/delete_faq_confirm.php');
        break;
      case 'move_faq_confirm':
        require(DIR_WS_MODULES . 'faq' . '/move_faq_confirm.php');
      break;
      case 'insert_faq':
      case 'update_faq':
        require(DIR_WS_MODULES . 'faq' . '/update_faq.php');
      break;
      case 'copy_to_confirm':
        require(DIR_WS_MODULES . 'faq' . '/copy_to_confirm.php');
        break;
      case 'new_faq_preview':
          require(DIR_WS_MODULES . 'faq' . '/preview_info.php');
      break;

    }
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
if (typeof _editor_url == "string") HTMLArea.replaceAll();
 }
 // -->
</script>
<?php if ($editor_handler != '') include ($editor_handler); ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
<?php
  if ($action == 'new_faq') {
  require(DIR_WS_MODULES . 'faq' . '/collect_info.php');
  } elseif ($action == 'new_faq_preview') {
  require(DIR_WS_MODULES . 'faq' . '/preview_info.php');
  } else {
  require(DIR_WS_MODULES . 'faq_category_faq_listing.php');

    $heading = array();
    $contents = array();
    switch ($action) {
      case 'new_faq_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_FAQ_CATEGORY . '</b>');

        $contents = array('form' => zen_draw_form('newfaq_category', FILENAME_FAQ_CATEGORIES, 'action=insert_faq_category&fcPath=' . $fcPath, 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_NEW_FAQ_CATEGORY_INTRO);

        $faq_category_inputs_string = '';
        $languages = zen_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $faq_category_inputs_string .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('faq_categories_name[' . $languages[$i]['id'] . ']', '', zen_set_field_length(TABLE_FAQ_CATEGORIES_DESCRIPTION, 'faq_categories_name'));
        }

        $contents[] = array('text' => '<br />' . TEXT_FAQ_CATEGORIES_NAME . $faq_category_inputs_string);
        $contents[] = array('text' => '<br />' . TEXT_SORT_ORDER . '<br />' . zen_draw_input_field('sort_order', '', 'size="4"'));
        $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'edit_faq_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY . '</b>');

        $contents = array('form' => zen_draw_form('faq_categories', FILENAME_FAQ_CATEGORIES, 'action=update_faq_category&fcPath=' . $fcPath, 'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('faq_categories_id', $cInfo->faq_categories_id));
        $contents[] = array('text' => TEXT_EDIT_INTRO);

        $faq_category_inputs_string = '';
        $languages = zen_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $faq_category_inputs_string .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('faq_categories_name[' . $languages[$i]['id'] . ']', zen_get_faq_category_name($cInfo->faq_categories_id, $languages[$i]['id']), zen_set_field_length(TABLE_FAQ_CATEGORIES_DESCRIPTION, 'faq_categories_name'));
        }
        $contents[] = array('text' => '<br />' . TEXT_EDIT_FAQ_CATEGORIES_NAME . $faq_category_inputs_string);
        $contents[] = array('text' => '<br />' . TEXT_EDIT_SORT_ORDER . '<br />' . zen_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $cInfo->faq_categories_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
	
      case 'delete_faq':
          require(DIR_WS_MODULES . 'faq' . '/sidebox_delete_faq.php');
        break;
	
      case 'move_faq':
          require(DIR_WS_MODULES . 'faq' . '/sidebox_move_faq.php');
        break;
		
      case 'copy_to':
         $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');
// WebMakers.com Added: Split Page
        if (empty($pInfo->faqs_id)) {
          $pInfo->faqs_id= $pID;
        }
        $type_faq_admin_handler = faq;
        $contents = array('form' => zen_draw_form('copy_to', $type_faq_admin_handler, 'action=copy_to_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $pInfo->faqs_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_FAQ . '<br /><b>' . $pInfo->faqs_name  . ' ID#' . $pInfo->faqs_id . '</b>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_FAQ_CATEGORIES . '<br /><b>' . zen_output_generated_faq_category_path($pInfo->faqs_id, 'faq') . '</b>');
        $contents[] = array('text' => '<br />' . TEXT_FAQ_CATEGORIES . '<br />' . zen_draw_pull_down_menu('faq_categories_id', zen_get_faq_category_tree(), $current_faq_category_id));
        $contents[] = array('text' => '<br />' . TEXT_HOW_TO_COPY . '<br />' . zen_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br />' . zen_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('text' => '<br />' . zen_image(DIR_WS_IMAGES . 'pixel_black.gif','','100%','3'));
        $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $pInfo->faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;


    } // switch

    if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>

          </tr>
          <tr>
<?php
// Split Page
if ($faqs_query_numrows > 0) {
  if (empty($pInfo->faqs_id)) {
    $pInfo->faqs_id= $pID;
  }
?>
            <td class="smallText" align="right"><?php echo $faqs_split->display_count($faqs_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FAQS) . '<br>' . $faqs_split->display_links($faqs_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y')) ); ?></td>

<?php
}
// Split Page
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  }
?>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>