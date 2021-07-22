<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: passchange.php,v1.0 2011/06/23 lebrand2006 $
//
// Le Brand Real IT Solutions - www.lebrand.gr

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $searchemail = (isset($_POST['email']) ? $_POST['email'] : '');
  $searchemail = zen_db_prepare_input($searchemail);
  $passchangeCust = (DB_PREFIX.'customers');
  $customers_id = (isset($_GET['cID']) ? zen_db_prepare_input($_GET['cID']) : '');

  if (zen_not_null($action)) {
    switch ($action) {
      case 'save':
        $customers_password = zen_db_prepare_input($_POST['customers_password']);
        $db->Execute("UPDATE $passchangeCust  
                      SET customers_password = concat(md5(concat(substring(md5('" . $customers_password . "'),1,2) , '" . $customers_password . "')) , ':' , substring(md5('" . $customers_password . "'),1,2))
                      WHERE customers_id = '" . $customers_id . "' LIMIT 1;
                     ");
        zen_redirect(zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page']));
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
  }
  // -->
</script>
</head>
<body onload="init()">
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
          <tr>
            <td class="pageHeading"><?php echo PASSCHANGE_HEADING_TITLE; ?></td>
			<td class="pageHeading">Search email <form action='' method=post><input type=text name=email><input type=submit></form></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo PASSCHANGE_CUSTOMERS_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo PASSCHANGE_CUSTOMERS_EMAIL; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo PASSCHANGE_ACTION; ?>&nbsp;</td>
              </tr>
<?php
 if ($searchemail){
		$passchange_query_raw = "select customers_id, customers_lastname, customers_firstname, customers_email_address from $passchangeCust WHERE customers_email_address='$searchemail'";
  }elseif ($customers_id){
		$passchange_query_raw = "select customers_id, customers_lastname, customers_firstname, customers_email_address from $passchangeCust WHERE customers_id='$customers_id'";
  }else{
		$passchange_query_raw = "select customers_id, customers_lastname, customers_firstname, customers_email_address from $passchangeCust order by customers_id";
  }
  $passchange_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $passchange_query_raw, $passchange_query_numrows);
  $passchange = $db->Execute($passchange_query_raw);
  while (!$passchange->EOF) {
    if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $passchange->fields['customers_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
      $cInfo = new objectInfo($passchange->fields);
    }

    if (isset($cInfo) && is_object($cInfo) && ($passchange->fields['customers_id'] == $cInfo->customers_id)) {
      echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page'] . '&cID=' . $passchange->fields['customers_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo ''.$passchange->fields['customers_lastname'].' '.$passchange->fields['customers_firstname'].''; ?></td>
                <td class="dataTableContent"><?php echo $passchange->fields['customers_email_address']; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($passchange->fields['customers_id'] == $cInfo->customers_id) ) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page'] . '&cID=' . $passchange->fields['customers_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    $passchange->MoveNext();
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $passchange_split->display_count($passchange_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $passchange_split->display_links($passchange_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $heading[]  = array('text'  => '<b>' . $cInfo->customers_lastname . ' ' . $cInfo->customers_firstname . '</b>');
      $contents   = array('form'  => zen_draw_form('passchange', FILENAME_PASSCHANGE, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id . '&action=save'));
      $contents[] = array('text'  => '<br /><b><font color="#0000FF">&nbsp;&nbsp;'.PASSCHANGE_CUSTOMERS_NAME_BOX.'</font></b><br />&nbsp;&nbsp;' . $cInfo->customers_lastname . ' ' . $cInfo->customers_firstname . '');
      $contents[] = array('text'  => '<br /><b><font color="#0000FF">&nbsp;&nbsp;'.PASSCHANGE_CUSTOMERS_EMAIL_BOX.'</font></b><br />&nbsp;&nbsp;' . $cInfo->customers_email_address . '');
      $contents[] = array('text'  => '<br /><b><font color="#0000FF">&nbsp;&nbsp;'.PASSCHANGE_CUSTOMERS_NEW_PASSWORD_BOX.'</font></b><br />&nbsp;&nbsp;' . zen_draw_input_field('customers_password', ''));
      $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page'] ) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[]  = array('text'  => '<b>' . $cInfo->customers_lastname . ' ' . $cInfo->customers_firstname . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br /><a href="' . zen_href_link(FILENAME_PASSCHANGE, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id . '&action=edit') . '"><b><font size="3" color="#FF0000">'.PASSCHANGE_CUSTOMERS_NO_BUTTON_LINKS_BOX.'</font></b></a>');
        $contents[] = array('text'  => '<br /><b><font color="#0000FF">&nbsp;&nbsp;'.PASSCHANGE_CUSTOMERS_NAME_BOX.'</font></b><br />&nbsp;&nbsp;' . $cInfo->customers_lastname . ' ' . $cInfo->customers_firstname . '');
        $contents[] = array('text'  => '<br /><b><font color="#0000FF">&nbsp;&nbsp;'.PASSCHANGE_CUSTOMERS_EMAIL_BOX.'</font></b><br />&nbsp;&nbsp;' . $cInfo->customers_email_address . '');
      }
      break;
  }

  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td width="35%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
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
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
