<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: categories.php 19330 2011-08-07 06:32:56Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_categories.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

  require('includes/application_top.php');
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (isset($_GET['page'])) $_GET['page'] = (int)$_GET['page'];

  if (zen_not_null($action)) {
    switch ($action) {
      case 'set_editor':
      // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
      $action='';
      zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES,  'fcPath=' . $_GET['fcPath'] . ((isset($_GET['pID']) and !empty($_GET['pID'])) ? '&pID=' . $_GET['pID'] : '') . ((isset($_GET['page']) and !empty($_GET['page'])) ? '&page=' . $_GET['page'] : '')));
      break;
	  
      case 'update_faq_category_status':
        // disable faq_category and faqs including subfaq_categories
        if (isset($_POST['faq_categories_id'])) {
          $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);

          $faq_categories = zen_get_faq_category_tree($faq_categories_id, '', '0', '', true);

          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {
            $faq_ids = $db->Execute("select faqs_id
                                         from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                         where faq_categories_id = '" . (int)$faq_categories[$i]['id'] . "'");

            while (!$faq_ids->EOF) {
              $faqs[$faq_ids->fields['faqs_id']]['faq_categories'][] = $faq_categories[$i]['id'];
              $faq_ids->MoveNext();
            }
          }

// change the status of faq_categories and faqs
          zen_set_time_limit(600);
          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {
            if ($_POST['faq_categories_status'] == '1') {
              $faq_categories_status = '0';
              $faqs_status = '0';
            } else {
              $faq_categories_status = '1';
              $faqs_status = '1';
            }

              $sql = "update " . TABLE_FAQ_CATEGORIES . " set faq_categories_status='" . $faq_categories_status . "'
                      where faq_categories_id='" . $faq_categories[$i]['id'] . "'";
              $db->Execute($sql);

            // set faqs_status based on selection
            if ($_POST['set_faqs_status'] == 'set_faqs_status_nochange') {
              // do not change current faq status
            } else {
              if ($_POST['set_faqs_status'] == 'set_faqs_status_on') {
                $faqs_status = '1';
              } else {
                $faqs_status = '0';
              }

              $sql = "select faqs_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " where faq_categories_id='" . $faq_categories[$i]['id'] . "'";
              $faq_category_faqs = $db->Execute($sql);

              while (!$faq_category_faqs->EOF) {
                $sql = "update " . TABLE_FAQS . " set faqs_status='" . $faqs_status . "' where faqs_id='" . $faq_category_faqs->fields['faqs_id'] . "'";
                $db->Execute($sql);
                $faq_category_faqs->MoveNext();
              }
            }
          } // for

        }
      zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $_GET['fcPath'] . '&cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
        break;

      case 'setflag':
      if (isset($_POST['flag']) && ($_POST['flag'] == '0') || ($_POST['flag'] == '1') ) {
        if (isset($_GET['pID'])) {
          zen_set_faq_status($_GET['pID'], $_POST['flag']);
        }
      }
      zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $_GET['fcPath'] . '&pID=' . $_GET['pID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;
	
      case 'insert_faq_category':
      case 'update_faq_category':
      if (isset($_POST['faq_categories_id'])) $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);
      $sort_order = zen_db_prepare_input($_POST['sort_order']);
      $sql_data_array = array('sort_order' => (int)$sort_order);
      if ($action == 'insert_faq_category') {
        $insert_sql_data = array('parent_id' => (int)$current_faq_category_id,
                                 'date_added' => 'now()');
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        zen_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array);
        $faq_categories_id = zen_db_insert_id();

      } elseif ($action == 'update_faq_category') {
        $update_sql_data = array('last_modified' => 'now()');
        $sql_data_array = array_merge($sql_data_array, $update_sql_data);
        zen_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array, 'update', "faq_categories_id = '" . (int)$faq_categories_id . "'");
      }

        $languages = zen_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $faq_categories_name_array = $_POST['faq_categories_name'];
          $faq_categories_description_array = $_POST['faq_categories_description'];
          $language_id = $languages[$i]['id'];

// clean $faq_categories_description when blank or just <p /> left behind
          $sql_data_array = array('faq_categories_name' => zen_db_prepare_input($faq_categories_name_array[$language_id]),
                                  'faq_categories_description' => ($faq_categories_description_array[$language_id] == '<p />' ? '' : zen_db_prepare_input($faq_categories_description_array[$language_id])));

        if ($action == 'insert_faq_category') {
          $insert_sql_data = array('faq_categories_id' => (int)$faq_categories_id,
                                   'language_id' => (int)$languages[$i]['id']);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          zen_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array);
        } elseif ($action == 'update_faq_category') {
          zen_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "faq_categories_id = '" . (int)$faq_categories_id . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
          }
        }

      zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $faq_categories_id));
      break;
	  
     case 'delete_faq_category_confirm_old':
        // demo active test
        if (zen_admin_demo()) {
          $_GET['action']= '';
          $messageStack->add_session(ERROR_ADMIN_DEMO, 'caution');
        	zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
        }
        if (isset($_POST['faq_categories_id'])) {
          $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);

          $faq_categories = zen_get_faq_category_tree($faq_categories_id, '', '0', '', true);
          $faqs = array();
          $faqs_delete = array();

          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {
            $faq_ids = $db->Execute("select faqs_id
                                         from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                         where faq_categories_id = '" . (int)$faq_categories[$i]['id'] . "'");

            while (!$faq_ids->EOF) {
              $faqs[$faq_ids->fields['faqs_id']]['faq_categories'][] = $faq_categories[$i]['id'];
              $faq_ids->MoveNext();
            }
          }

          reset($faqs);
          while (list($key, $value) = each($faqs)) {
            $faq_category_ids = '';

            for ($i=0, $n=sizeof($value['faq_categories']); $i<$n; $i++) {
              $faq_category_ids .= "'" . (int)$value['faq_categories'][$i] . "', ";
            }
            $faq_category_ids = substr($faq_category_ids, 0, -2);

            $check = $db->Execute("select count(*) as total
                                         from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                         where faqs_id = '" . (int)$key . "'
                                         and faq_categories_id not in (" . $faq_category_ids . ")");
            if ($check->fields['total'] < '1') {
              $faqs_delete[$key] = $key;
            }
          }

// removing faq_categories can be a lengthy process
          zen_set_time_limit(600);
          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {
            zen_remove_faq_category($faq_categories[$i]['id']);
          }

          reset($faqs_delete);
          while (list($key) = each($faqs_delete)) {
            zen_remove_faq($key);
          }
        }


        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
        break;

//////////////////////////////////
// delete new

      case 'delete_faq_category_confirm':
        // demo active test
        if (zen_admin_demo()) {
          $_GET['action']= '';
          $messageStack->add_session(ERROR_ADMIN_DEMO, 'caution');
        	zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
        }

// future cat specific deletion
        $delete_linked = 'true';
        if ($_POST['delete_linked'] == 'delete_linked_no') {
          $delete_linked = 'false';
        } else {
          $delete_linked = 'true';
        }

        // delete faq_category and faqs
        if (isset($_POST['faq_categories_id'])) {
          $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);

          $faq_categories = zen_get_faq_category_tree($faq_categories_id, '', '0', '', true);

          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {
            $faq_ids = $db->Execute("select faqs_id
                                         from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                         where faq_categories_id = '" . (int)$faq_categories[$i]['id'] . "'");

            while (!$faq_ids->EOF) {
              $faqs[$faq_ids->fields['faqs_id']]['faq_categories'][] = $faq_categories[$i]['id'];
              $faq_ids->MoveNext();
            }
          }

// change the status of faq_categories and faqs
          zen_set_time_limit(600);
          for ($i=0, $n=sizeof($faq_categories); $i<$n; $i++) {

            // set faqs_status based on selection

              $sql = "select faqs_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " where faq_categories_id='" . $faq_categories[$i]['id'] . "'";
              $faq_category_faqs = $db->Execute($sql);

              while (!$faq_category_faqs->EOF) {
                // future cat specific use for
                zen_remove_faq($faq_category_faqs->fields['faqs_id'], $delete_linked);
                $faq_category_faqs->MoveNext();
              }

            zen_remove_faq_category($faq_categories[$i]['id']);

          } // for
        }
        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
        break;

// eof delete new
/////////////////////////////////
      case 'move_faq_category_confirm':
      if (isset($_POST['faq_categories_id']) && ($_POST['faq_categories_id'] != $_POST['move_to_faq_category_id'])) {
        $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);
        $new_parent_id = zen_db_prepare_input($_POST['move_to_faq_category_id']);
        $path = explode('_', zen_get_generated_faq_category_path_ids($new_parent_id));
        if (in_array($faq_categories_id, $path)) {
          $messageStack->add_session(ERROR_CANNOT_MOVE_FAQ_CATEGORY_TO_PARENT, 'error');
          zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
          } else {
          $db->Execute("update " . TABLE_FAQ_CATEGORIES . "
                            set parent_id = '" . (int)$new_parent_id . "', last_modified = now()
                            where faq_categories_id = '" . (int)$faq_categories_id . "'");
          $messageStack->add_session(SUCCESS_FAQ_CATEGORY_MOVED, 'success');							
          zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $new_parent_id));
		  }
       } else {
        $messageStack->add_session(ERROR_CANNOT_MOVE_FAQ_CATEGORY_TO_CATEGORY_SELF, 'error');
        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
      }
      break;

      $_GET['action']= '';
      zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $_GET['faqs_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
      break;
    case 'new_faq':
          zen_redirect(zen_href_link('faq.php', zen_get_all_get_params()));
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<?php if ($action == '') { ?>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="smallText" valign="top" align="center" width="55"><?php echo TEXT_LEGEND; ?></td>
            <td class="smallText" align="center" width="75"><?php echo TEXT_LEGEND_STATUS_OFF . '<br />' . zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF); ?></td>
            <td class="smallText" align="center" width="75"><?php echo TEXT_LEGEND_STATUS_ON . '<br />' . zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON); ?></td>
            <td class="smallText" align="center" width="75"><?php echo TEXT_LEGEND_FAQ_LINKED . '<br />' . zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_FAQ_LINKED); ?></td>
          </tr>
        </table></td>
      </tr>

  <tr>
    <td class="smallText" width="100%" align="right">
<?php
      // toggle switch for editor
      echo TEXT_EDITOR_INFO . zen_draw_form('set_faq_editor_form', FILENAME_FAQ_CATEGORIES, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onChange="this.form.submit();"') . zen_hide_session_id() .
            zen_draw_hidden_field('cID', $fcPath) .
            zen_draw_hidden_field('fcPath', $fcPath) .
            zen_draw_hidden_field('pID', $_GET['pID']) .
            zen_draw_hidden_field('page', $_GET['page']) .
            zen_draw_hidden_field('action', 'set_editor') .
      '</form>';
?>
    </td>
  </tr>

  <tr>
    <td class="smallText" width="100%" align="right">
      <?php
      // check for which buttons to show for categories and products
      $check_faq_categories = zen_has_faq_category_subfaq_categories($current_faq_category_id);
      $check_faqs = zen_faqs_in_faq_category_count($current_faq_category_id, false, false, 1);

      $zc_skip_faqs = false;
      $zc_skip_faq_categories = false;

      if ($check_faqs == 0) {
        $zc_skip_faqs = false;
        $zc_skip_faq_categories = false;
      }
      if ($check_faq_categories == true) {
        $zc_skip_faqs = true;
        $zc_skip_faq_categories = false;
      }
      if ($check_products > 0) {
        $zc_skip_faqs = false;
        $zc_skip_faq_categories = true;
      }

      ?>
    </td>
  </tr>

<?php } ?>
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
<?php
    require(DIR_WS_MODULES . 'faq_category_faq_listing.php');

    $heading = array();
    $contents = array();

  if (isset($_GET['fcPath'])) {
    $fcPath = $_GET['fcPath'];
  }
    switch ($action) {
    case 'setflag_faq_categories':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_STATUS_FAQ_CATEGORY . '</b>');
    $contents = array('form' => zen_draw_form('faq_categories', FILENAME_FAQ_CATEGORIES, 'action=update_faq_category_status&fcPath=' . $_GET['fcPath'] . '&cID=' . $_GET['cID'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('faq_categories_id', $cInfo->faq_categories_id) . zen_draw_hidden_field('faq_categories_status', $cInfo->faq_categories_status));
    $contents[] = array('text' => zen_get_faq_category_name($cInfo->faq_categories_id, $_SESSION['languages_id']));
    $contents[] = array('text' => '<br />' . TEXT_FAQ_CATEGORIES_STATUS_WARNING . '<br /><br />');
    $contents[] = array('text' => TEXT_FAQ_CATEGORIES_STATUS_INTRO . ' ' . ($cInfo->faq_categories_status == '1' ? TEXT_FAQ_CATEGORIES_STATUS_OFF : TEXT_FAQ_CATEGORIES_STATUS_ON));
    if ($cInfo->faq_categories_status == '1') {
      $contents[] = array('text' => '<br />' . TEXT_FAQS_STATUS_INFO . ' ' . TEXT_FAQS_STATUS_OFF . zen_draw_hidden_field('set_faqs_status_off', true));
    } else {
      $contents[] = array('text' => '<br />' . TEXT_FAQS_STATUS_INFO . '<br />' .
      zen_draw_radio_field('set_faqs_status', 'set_faqs_status_on', true) . ' ' . TEXT_FAQS_STATUS_ON . '<br />' .
      zen_draw_radio_field('set_faqs_status', 'set_faqs_status_off') . ' ' . TEXT_FAQS_STATUS_OFF . '<br />' .
      zen_draw_radio_field('set_faqs_status', 'set_faqs_status_nochange') . ' ' . TEXT_FAQS_STATUS_NOCHANGE);
    }
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;

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
    $faq_category_inputs_string = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $faq_category_inputs_string .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;';
      $faq_category_inputs_string .= zen_draw_textarea_field('faq_categories_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_faq_category_description($cInfo->faq_categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE));
        }
    $contents[] = array('text' => '<br />' . TEXT_FAQ_CATEGORIES_DESCRIPTION . $faq_category_inputs_string);
    $contents[] = array('text' => '<br />' . TEXT_SORT_ORDER . '<br />' . zen_draw_input_field('sort_order', '', 'size="6"'));
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
		
    case 'edit_faq_category':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY . '</b>');
    $contents[] = array('text' => zen_draw_form('faq_categories', FILENAME_FAQ_CATEGORIES, 'action=update_faq_category&fcPath=' . $fcPath, 'post', 'enctype="multipart/form-data"') . zen_draw_hidden_field('faq_categories_id', $cInfo->faq_categories_id));
    $contents[] = array('text' => TEXT_EDIT_INTRO);
    $languages = zen_get_languages();
    $faq_category_inputs_string = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $faq_category_inputs_string .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . zen_draw_input_field('faq_categories_name[' . $languages[$i]['id'] . ']', zen_get_faq_category_name($cInfo->faq_categories_id, $languages[$i]['id']), zen_set_field_length(TABLE_FAQ_CATEGORIES_DESCRIPTION, 'faq_categories_name'));
    }
    $contents[] = array('text' => '<br />' . TEXT_EDIT_FAQ_CATEGORIES_NAME . $faq_category_inputs_string);
    $faq_category_inputs_string = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $faq_category_inputs_string .= '<br />' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' ;
      $faq_category_inputs_string .= zen_draw_textarea_field('faq_categories_description[' . $languages[$i]['id'] . ']', 'soft', '100%', '20', htmlspecialchars(zen_get_faq_category_description($cInfo->faq_categories_id, $languages[$i]['id']), ENT_COMPAT, CHARSET, TRUE));
    }
    $contents[] = array('text' => '<br />' . TEXT_FAQ_CATEGORIES_DESCRIPTION . $faq_category_inputs_string);
    $contents[] = array('text' => '<br />' . TEXT_EDIT_SORT_ORDER . '<br />' . zen_draw_input_field('sort_order', $cInfo->sort_order, 'size="6"'));
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $cInfo->faq_categories_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
		
    case 'delete_faq_category':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ_CATEGORY . '</b>');
    $contents = array('form' => zen_draw_form('faq_categories', FILENAME_FAQ_CATEGORIES, 'action=delete_faq_category_confirm&fcPath=' . $fcPath) . zen_draw_hidden_field('faq_categories_id', $cInfo->faq_categories_id));
    $contents[] = array('text' => TEXT_DELETE_FAQ_CATEGORY_INTRO);
    $contents[] = array('text' => '<br /><b>' . $cInfo->faq_categories_name . '</b>');
    if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br />' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
    if ($cInfo->faqs_count > 0) $contents[] = array('text' => '<br />' . sprintf(TEXT_DELETE_WARNING_FAQS, $cInfo->faqs_count));
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $cInfo->faq_categories_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
		
    case 'move_faq_category':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_FAQ_CATEGORY . '</b>');
    $contents = array('form' => zen_draw_form('faq_categories', FILENAME_FAQ_CATEGORIES, 'action=move_faq_category_confirm&fcPath=' . $fcPath) . zen_draw_hidden_field('faq_categories_id', $cInfo->faq_categories_id));
    $contents[] = array('text' => sprintf(TEXT_MOVE_FAQ_CATEGORIES_INTRO, $cInfo->faq_categories_name));
    $contents[] = array('text' => '<br />' . sprintf(TEXT_MOVE, $cInfo->faq_categories_name) . '<br />' . zen_draw_pull_down_menu('move_to_faq_category_id', zen_get_faq_category_tree(), $current_faq_category_id));
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&cID=' . $cInfo->faq_categories_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
		
    case 'delete_faq':
	$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ . '</b>');
    $contents = array('form' => zen_draw_form('faqs', FILENAME_FAQ_CATEGORIES, 'action=delete_faq_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $faqs->fields['faqs_id']));
    $contents[] = array('text' => TEXT_DELETE_FAQ_INTRO);
    $contents[] = array('text' => '<br /><b>' . $pInfo->faqs_name . ' ID#' . $faqs->fields['faqs_id'] . '</b>');
    $faq_faq_categories_string = '';
    $faq_faq_categories = zen_generate_faq_category_path($faqs->fields['faqs_id'], 'faq');
    for ($i = 0, $n = sizeof($faq_faq_categories); $i < $n; $i++) {
      $faq_category_path = '';
      for ($j = 0, $k = sizeof($faq_faq_categories[$i]); $j < $k; $j++) {
        $faq_category_path .= $faq_faq_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $faq_category_path = substr($faq_category_path, 0, -16);
      $faq_faq_categories_string .= zen_draw_checkbox_field('faq_faq_categories[]', $faq_faq_categories[$i][sizeof($faq_faq_categories[$i])-1]['id'], true) . '&nbsp;' . $faq_category_path . '<br />';
    }
    $faq_faq_categories_string = substr($faq_faq_categories_string, 0, -4);
    $contents[] = array('text' => '<br />' . $faq_faq_categories_string);
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $faqs->fields['faqs_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
		
    case 'move_faq':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_FAQ . '</b>');
    $contents = array('form' => zen_draw_form('faqs', FILENAME_FAQ_CATEGORIES, 'action=move_faq_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $pInfo->faqs_id));
    $contents[] = array('text' => sprintf(TEXT_MOVE_FAQS_INTRO, $pInfo->faqs_name));
    $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_FAQ_CATEGORIES . '<br /><b>' . zen_output_generated_faq_category_path($pInfo->faqs_id, 'faq') . '</b>');
    $contents[] = array('text' => '<br />' . sprintf(TEXT_MOVE, $pInfo->faqs_name) . '<br />' . zen_draw_pull_down_menu('move_to_faq_category_id', zen_get_faq_category_tree(), $current_faq_category_id));
    $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $pInfo->faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
    break;
		
    case 'copy_to':
    $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');
// WebMakers.com Added: Split Page
    if (empty($pInfo->faqs_id)) {
      $pInfo->faqs_id= $pID;
    }
    $contents = array('form' => zen_draw_form('copy_to', FILENAME_FAQ_CATEGORIES, 'action=copy_to_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $pInfo->faqs_id));
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
    echo '            <td valign="top">' . "\n";

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
            <td class="smallText" align="center"><?php echo $faqs_split->display_count($faqs_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FAQS) . '<br>' . $faqs_split->display_links($faqs_query_numrows, MAX_DISPLAY_RESULTS_CATEGORIES, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'pID')) ); ?></td>

<?php
}
// Split Page
?>
          </tr>
        </table></td>
      </tr>
    </table>
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
