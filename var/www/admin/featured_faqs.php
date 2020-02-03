<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on $Id: featured.php 19294 2011-07-28 18:15:46Z drbyte $
 * @featured_faqs.php created 2012-09-18 kamelion0927
 */

  require('includes/application_top.php');
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  
  $faq_manager_link_id = $db->Execute("select configuration_group_id from " . TABLE_CONFIGURATION_GROUP . "
            where configuration_group_title = 'FAQ Manager'");
  $faq_manager_link = $faq_manager_link_id->fields['configuration_group_id'];

  if (zen_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if (isset($_POST['flag']) && ($_POST['flag'] == 1 || $_POST['flag'] == 0))
        {
        zen_set_featured_faq_status($_GET['id'], $_POST['flag']);
        zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'fID=' . $_GET['id'] . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''), 'NONSSL'));
        }
        break;
      case 'insert':
        if ($_POST['faqs_id'] < 1) {
          $messageStack->add_session(ERROR_NOTHING_SELECTED, 'caution');
        } else {
        $faqs_id = zen_db_prepare_input($_POST['faqs_id']);

        $featured_date_available = ((zen_db_prepare_input($_POST['start']) == '') ? '0001-01-01' : zen_date_raw($_POST['start']));
        $expires_date = ((zen_db_prepare_input($_POST['end']) == '') ? '0001-01-01' : zen_date_raw($_POST['end']));

        $db->Execute("insert into " . TABLE_FEATURED_FAQS . "
                    (faqs_id, featured_date_added, expires_date, status, featured_date_available)
                    values ('" . (int)$faqs_id . "',
                            now(),
                            '" . zen_db_input($expires_date) . "', '1', '" . zen_db_input($featured_date_available) . "')");

        $new_featured = $db->Execute("select featured_faqs_id from " . TABLE_FEATURED_FAQS . " where faqs_id='" . (int)$faqs_id . "'");
        } // nothing selected to add
          zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, (isset($_GET['page']) && $_GET['page'] > 0 ? 'page=' . $_GET['page'] . '&' : '') . 'fID=' . $new_featured->fields['featured_faqs_id'] . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')));
        break;
      case 'update':
        $featured_faqs_id = zen_db_prepare_input($_POST['featured_faqs_id']);

        $featured_date_available = ((zen_db_prepare_input($_POST['start']) == '') ? '0001-01-01' : zen_date_raw($_POST['start']));
        $expires_date = ((zen_db_prepare_input($_POST['end']) == '') ? '0001-01-01' : zen_date_raw($_POST['end']));

        $db->Execute("update " . TABLE_FEATURED_FAQS . "
                      set featured_last_modified = now(),
                          expires_date = '" . zen_db_input($expires_date) . "',
                          featured_date_available = '" . zen_db_input($featured_date_available) . "'
                      where featured_faqs_id = '" . (int)$featured_faqs_id . "'");

        zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, (isset($_GET['page']) && $_GET['page'] > 0 ? 'page=' . $_GET['page'] . '&' : '') . 'fID=' . (int)$featured_faqs_id . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')));
        break;
      case 'deleteconfirm':
        // demo active test
        if (zen_admin_demo()) {
          $_GET['action']= '';
          $messageStack->add_session(ERROR_ADMIN_DEMO, 'caution');
          zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')));
        }
        $featured_faqs_id = zen_db_prepare_input($_POST['fID']);

        $db->Execute("delete from " . TABLE_FEATURED_FAQS . "
                      where featured_faqs_id = '" . (int)$featured_faqs_id . "'");

        zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')));
        break;
      case 'pre_add_confirmation':
      // check for blank or existing featured
        $skip_featured = false;
        if (empty($_POST['pre_add_faqs_id'])) {
          $skip_featured = true;
          $messageStack->add_session(WARNING_FEATURED_PRE_ADD_EMPTY, 'caution');
        }

        if ($skip_featured == false) {
          $sql = "select faqs_id from " . TABLE_FAQS . " where faqs_id='" . (int)$_POST['pre_add_faqs_id'] . "'";
          $check_featured = $db->Execute($sql);
          if ($check_featured->RecordCount() < 1) {
            $skip_featured = true;
            $messageStack->add_session(WARNING_FEATURED_PRE_ADD_BAD_FAQS_ID, 'caution');
          }
        }

        if ($skip_featured == false) {
          $sql = "select featured_faqs_id from " . TABLE_FEATURED_FAQS . " where faqs_id='" . (int)$_POST['pre_add_faqs_id'] . "'";
          $check_featured = $db->Execute($sql);
          if ($check_featured->RecordCount() > 0) {
            $skip_featured = true;
            $messageStack->add_session(WARNING_FEATURED_PRE_ADD_DUPLICATE, 'caution');
          }
        }

        if ($skip_featured == true) {
          zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, (isset($_GET['page']) && $_GET['page'] > 0 ? 'page=' . $_GET['page'] . '&' : '') . ((int)$check_featured->fields['featured_faqs_id'] > 0 ? 'fID=' . (int)$check_featured->fields['featured_faqs_id'] : '' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''))));
        }
      // add empty featured

        $featured_date_available = ((zen_db_prepare_input($_POST['start']) == '') ? '0001-01-01' : zen_date_raw($_POST['start']));
        $expires_date = ((zen_db_prepare_input($_POST['end']) == '') ? '0001-01-01' : zen_date_raw($_POST['end']));

        $faqs_id = zen_db_prepare_input($_POST['pre_add_faqs_id']);
        $db->Execute("insert into " . TABLE_FEATURED_FAQS . "
                    (faqs_id, featured_faq_date_added, expires_date, status, featured_faq_date_available)
                    values ('" . (int)$faqs_id . "',
                            now(),
                            '" . zen_db_input($expires_date) . "', '1', '" . zen_db_input($featured_date_available) . "')");

        $new_featured = $db->Execute("select featured_faqs_id from " . TABLE_FEATURED_FAQS . " where faqs_id='" . (int)$faqs_id . "'");

        $messageStack->add_session(SUCCESS_FEATURED_PRE_ADD, 'success');
        zen_redirect(zen_href_link(FILENAME_FEATURED_FAQS, 'action=edit' . '&fID=' . $new_featured->fields['featured_faqs_id'] . '&manual=1'));
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
<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<?php
  }
?>
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
  }
  // -->
</script>
</head>
<body onLoad="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">

      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
         <tr><?php echo zen_draw_form('search', FILENAME_FEATURED_FAQS, '', 'get'); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right">
<?php
// show reset search
  if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
    echo '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS) . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>&nbsp;&nbsp;';
  }
  echo HEADING_TITLE_SEARCH_DETAIL . ' ' . zen_draw_input_field('search') . zen_hide_session_id();
  if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
    $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
    echo '<br/ >' . TEXT_INFO_SEARCH_DETAIL_FILTER . $keywords;
  }
?>
            </td>
          </form></tr>
          <tr>
            <td colspan="3" class="main"><?php echo TEXT_STATUS_WARNING; ?></td>
          </tr>
        </table></td>
      </tr>
    <tr>
	<td class="pageHeading"><?php echo zen_draw_separator('pixel_trans.gif', 1, 3); ?></td>
	</tr>

<?php
  if (empty($action)) {
?>

    <td align="left" valign="middle"><?php echo '<a href="' . zen_href_link(FILENAME_FAQ_MANAGER, 'gID=' . $faq_manager_link) . '">' . zen_image_button('faq_manager_links_categories.gif', TEXT_FAQ_CONFIGURATION) . '</a>' . '&nbsp;&nbsp;&nbsp;' . '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES) . '">' . zen_image_button('faq_manager_links_edit.gif', TEXT_FAQ_FAQ_CATEGORIES) . '</a>' . '&nbsp;&nbsp;&nbsp;' . '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, ((isset($_GET['page']) && $_GET['page'] > 0) ? 'page=' . $_GET['page'] . '&' : '') . 'action=new') . '">' . zen_image_button('button_new_faqlg.gif', IMAGE_NEW_FAQ) . '</a>'; ?></td>
    <tr>
	<td class="pageHeading"><?php echo zen_draw_separator('pixel_trans.gif', 1, 5); ?></td>
	</tr>
<?php
  }
?>
<?php
  if ( ($action == 'new') || ($action == 'edit') ) {
    $form_action = 'insert';
    if ( ($action == 'edit') && isset($_GET['fID']) ) {
      $form_action = 'update';

      $faq = $db->Execute("select p.faqs_id, pd.faqs_name,
                                      f.expires_date, f.featured_date_available
                               from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd, " .
                                        TABLE_FEATURED_FAQS . " f
                               where p.faqs_id = pd.faqs_id
                               and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                               and p.faqs_id = f.faqs_id
                               and f.featured_faqs_id = '" . (int)$_GET['fID'] . "'");

      $fInfo = new objectInfo($faq->fields);
    } else {
      $fInfo = new objectInfo(array());

// create an array of featured faqs, which will be excluded from the pull down menu of faqs
// (when creating a new featured faq)
      $featured_array = array();
      $featured = $db->Execute("select p.faqs_id
                                from " . TABLE_FAQS . " p, " . TABLE_FEATURED_FAQS . " f
                                where f.faqs_id = p.faqs_id");

      while (!$featured->EOF) {
        $featured_array[] = $featured->fields['faqs_id'];
        $featured->MoveNext();
      }
    }
?>
<script language="javascript">
var StartDate = new ctlSpiffyCalendarBox("StartDate", "new_featured", "start", "btnDate1","<?php echo (($fInfo->featured_date_available == '0001-01-01') ? '' : zen_date_short($fInfo->featured_date_available)); ?>",scBTNMODE_CUSTOMBLUE);
var EndDate = new ctlSpiffyCalendarBox("EndDate", "new_featured", "end", "btnDate2","<?php echo (($fInfo->expires_date == '0001-01-01') ? '' : zen_date_short($fInfo->expires_date)); ?>",scBTNMODE_CUSTOMBLUE);
</script>

      <tr>
      <?php echo zen_draw_form('new_featured', FILENAME_FEATURED_FAQS, zen_get_all_get_params(array('action', 'info', 'fID')) . 'action=' . $form_action . '&go_back=' . $_GET['go_back']); ?><?php if ($form_action == 'update') echo zen_draw_hidden_field('featured_faqs_id', $_GET['fID']); ?>
        <td><br><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo TEXT_FEATURED_FAQ; ?>&nbsp;</td>
            <td class="main"><?php echo (isset($fInfo->faqs_name)) ? $fInfo->faqs_name  : zen_draw_faqs_pull_down('faqs_id', 'size="15" style="font-size:12px"', $featured_array, true, $_GET['add_faqs_id'], true); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FEATURED_AVAILABLE_DATE; ?>&nbsp;</td>
            <td class="main"><script language="javascript">StartDate.writeControl(); StartDate.dateFormat="<?php echo DATE_FORMAT_SPIFFYCAL; ?>";</script></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_FEATURED_EXPIRES_DATE; ?>&nbsp;</td>
            <td class="main"><script language="javascript">EndDate.writeControl(); EndDate.dateFormat="<?php echo DATE_FORMAT_SPIFFYCAL; ?>";</script></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="2" class="main" align="right" valign="top"><br><?php echo (($form_action == 'insert') ? zen_image_submit('button_insert.gif', IMAGE_INSERT) : zen_image_submit('button_update.gif', IMAGE_UPDATE)). ((int)$_GET['manual'] == 0 ? '&nbsp;&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_FEATURED_FAQS) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>' : ''); ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="right"><?php echo 'ID#'; ?>&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FAQS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_AVAILABLE_DATE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_EXPIRES_DATE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
// create search filter
  $search = '';
  if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
    $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
    $search = " and (pd.faqs_name like '%" . $keywords . "%' or pd.faqs_answer like '%" . $keywords . "%')";
  }

// order of display
  $order_by = " order by pd.faqs_name ";
  $featured_query_raw = "select p.faqs_id, pd.faqs_name, f.featured_faqs_id, f.featured_date_added, f.featured_last_modified, f.expires_date, f.date_status_change, f.status, f.featured_date_available from " . TABLE_FAQS . " p, " . TABLE_FEATURED_FAQS . " f, " . TABLE_FAQS_DESCRIPTION . " pd where p.faqs_id = pd.faqs_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p.faqs_id = f.faqs_id"  . $search . $order_by;
// Split Page
// reset page when page is unknown
if (($_GET['page'] == '1' or $_GET['page'] == '') and $_GET['fID'] != '') {
  $old_page = $_GET['page'];
  $check_page = $db->Execute($featured_query_raw);
  if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN) {
    $check_count=1;
    while (!$check_page->EOF) {
      if ($check_page->fields['featured_faqs_id'] == $_GET['fID']) {
        break;
      }
      $check_count++;
      $check_page->MoveNext();
    }
    $_GET['page'] = round((($check_count/MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN)+(fmod_round($check_count,MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN) !=0 ? .5 : 0)),0);
    $page = $_GET['page'];
    if ($old_page != $_GET['page']) {
// do nothing
    }
  } else {
    $_GET['page'] = 1;
  }
}
// create split page control
    $featured_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN, $featured_query_raw, $featured_query_numrows);
    $featured = $db->Execute($featured_query_raw);
    while (!$featured->EOF) {
      if ((!isset($_GET['fID']) || (isset($_GET['fID']) && ($_GET['fID'] == $featured->fields['featured_faqs_id']))) && !isset($fInfo)) {
        $faqs = $db->Execute("select faqs_id
                                  from " . TABLE_FAQS . "
                                  where faqs_id = '" . (int)$featured->fields['faqs_id'] . "'");

        $fInfo_array = array_merge($featured->fields, $faqs->fields);
        $fInfo = new objectInfo($fInfo_array);
      }

      if (isset($fInfo) && is_object($fInfo) && ($featured->fields['featured_faqs_id'] == $fInfo->featured_faqs_id)) {
        echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_faqs_id . '&action=edit' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '\'">' . "\n";
      } else {
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $featured->fields['featured_faqs_id'] . '&action=edit' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '\'">' . "\n";
      }

?>
                <td  class="dataTableContent" align="right"><?php echo $featured->fields['faqs_id']; ?>&nbsp;</td>
                <td  class="dataTableContent"><?php echo $featured->fields['faqs_name']; ?></td>
                <td  class="dataTableContent" align="center"><?php echo (($featured->fields['featured_date_available'] != '0001-01-01' and $featured->fields['featured_date_available'] !='') ? zen_date_short($featured->fields['featured_date_available']) : TEXT_NONE); ?></td>
                <td  class="dataTableContent" align="center"><?php echo (($featured->fields['expires_date'] != '0001-01-01' and $featured->fields['expires_date'] !='') ? zen_date_short($featured->fields['expires_date']) : TEXT_NONE); ?></td>
                <td  class="dataTableContent" align="center">
<?php
      if ($featured->fields['status'] == '1') {
        echo zen_draw_form('setflag_faqs', FILENAME_FEATURED_FAQS, 'action=setflag&id=' . $featured->fields['featured_faqs_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''));?>
        <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_green_on.gif" title="<?php echo IMAGE_ICON_STATUS_ON; ?>" />
        <input type="hidden" name="flag" value="0" />
        </form>
<?php
      } else {
        echo zen_draw_form('setflag_faqs', FILENAME_FEATURED_FAQS, 'action=setflag&id=' . $featured->fields['featured_faqs_id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : ''));?>
        <input type="image" src="<?php echo DIR_WS_IMAGES ?>icon_red_on.gif" title="<?php echo IMAGE_ICON_STATUS_OFF; ?>" />
        <input type="hidden" name="flag" value="1" />
        </form>
<?php
      }
?>
                </td>
                <td class="dataTableContent" align="right">
                  <?php echo '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $featured->fields['featured_faqs_id'] . '&action=edit' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>'; ?>
				  <?php echo '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $featured->fields['featured_faqs_id'] . '&action=delete' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>'; ?>
                  <?php if (isset($fInfo) && is_object($fInfo) && ($featured->fields['featured_faqs_id'] == $fInfo->featured_faqs_id)) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, zen_get_all_get_params(array('fID')) . 'fID=' . $featured->fields['featured_faqs_id'] . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>
				        </td>
      </tr>
<?php
      $featured->MoveNext();
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellpadding="0"cellspacing="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $featured_split->display_count($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_FEATURED); ?></td>
                    <td class="smallText" align="right"><?php echo $featured_split->display_links($featured_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_FEATURED_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params( array( 'page', 'fID' ))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FEATURED . '</b>');

      $contents = array('form' => zen_draw_form('featured', FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&action=deleteconfirm' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . zen_draw_hidden_field('fID', $fInfo->featured_faqs_id));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . $fInfo->faqs_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_faqs_id . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'pre_add':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_PRE_ADD_FEATURED . '</b>');
      $contents = array('form' => zen_draw_form('featured', FILENAME_FEATURED_FAQS, 'action=pre_add_confirmation' . ((isset($_GET['page']) && $_GET['page'] > 0) ? '&page=' . $_GET['page'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')));
      $contents[] = array('text' => TEXT_INFO_PRE_ADD_INTRO);
      $contents[] = array('text' => '<br />' . TEXT_PRE_ADD_FAQS_ID . '<br>' . zen_draw_input_field('pre_add_faqs_id', '', zen_set_field_length(TABLE_FEATURED_FAQS, 'faqs_id')));
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_confirm.gif', IMAGE_CONFIRM) . '&nbsp;<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . ($fInfo->featured_faqs_id > 0 ? '&fID=' . $fInfo->featured_faqs_id : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($fInfo)) {
        $heading[] = array('text' => '<b>' . $fInfo->faqs_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_faqs_id . '&action=edit' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'page=' . $_GET['page'] . '&fID=' . $fInfo->featured_faqs_id . '&action=delete' . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . zen_date_short($fInfo->featured_date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . zen_date_short($fInfo->featured_last_modified));

        $contents[] = array('text' => '<br />' . TEXT_INFO_AVAILABLE_DATE . ' <b>' . (($fInfo->featured_date_available != '0001-01-01' and $fInfo->featured_date_available !='') ? zen_date_short($fInfo->featured_date_available) : TEXT_NONE) . '</b>');
        $contents[] = array('text' => TEXT_INFO_EXPIRES_DATE . ' <b>' . (($fInfo->expires_date != '0001-01-01' and $fInfo->expires_date !='') ? zen_date_short($fInfo->expires_date) : TEXT_NONE) . '</b>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_STATUS_CHANGE . ' ' . zen_date_short($fInfo->date_status_change));
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, '&action=new_faq' . '&cPath=' . zen_get_faq_path($fInfo->faqs_id, 'override') . '&pID=' . $fInfo->faqs_id . 'faq_type=1') . '">' . zen_image_button('button_edit_faq.gif', IMAGE_EDIT_FAQ) . '<br />' . '</a>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'action=pre_add' . ((isset($_GET['page']) && $_GET['page'] > 0) ? '&page=' . $_GET['page'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_select.gif', IMAGE_SELECT) . '<br />' . TEXT_INFO_MANUAL . '</a><br /><br />');
      } else {
        $heading[] = array('text' => '<b>' . TEXT_NONE . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_FEATURED_FAQS, 'action=pre_add' . ((isset($_GET['page']) && $_GET['page'] > 0) ? '&page=' . $_GET['page'] : '') . (isset($_GET['search']) ? '&search=' . $_GET['search'] : '')) . '">' . zen_image_button('button_select.gif', IMAGE_SELECT) . '<br />' . TEXT_INFO_MANUAL . '</a><br /><br />');
      }
      break;
  }
  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
