<?php
/**
 *
 * @copyright Copyright 2003-2008 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id:   v 3.01 2008/05/21 paulm
*/

require('includes/application_top.php');
$secret = PAYPAL_PUSHORDER_PASS;
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
<?php

$session_post = (isset($_GET['Session_ID'])) ? $_GET['Session_ID'] : '';
$sql = "select * from " . TABLE_PAYPAL_SESSION . " ORDER BY unique_id DESC";
$sql .= ($session_post=='') ? '' : " where session_id = '" . $_GET['Session_ID'] . "'";

$stored_sessions_numrows = '';
$stored_sessions_split = new splitPageResults($_GET['page'], '20', $sql, $stored_sessions_numrows);

echo $stored_sessions_split->display_links($stored_sessions_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page')));
echo '<br />';
$stored_sessions = $db->Execute($sql);
$records = $stored_sessions->RecordCount();
if ($records > 0) {
printf(TEXT_DISPLAYING, $records);

while (!$stored_sessions->EOF) {
  $mysess = unserialize(base64_decode($stored_sessions->fields['saved_session']));
  echo '<hr style="height: 5px; background-color: green;" />';
  echo '<u style="color: red">PAYPAL SESSION INFO for record #'.$stored_sessions->fields['unique_id'].', which expires on ' . date("Y-m-d G:i", $stored_sessions->fields['expiry']) .'</u><br /><strong>Session_ID='.$stored_sessions->fields['session_id'].'</strong><br />';
  
  echo '<form action='. HTTP_CATALOG_SERVER . DIR_WS_CATALOG . FILENAME_PUSHORDER . ' method="POST" target="_blank">' . "\n";
  echo "<input type=hidden name=secret value=\"$secret\">"; 
  echo "<input type=hidden name=id value=\"".$stored_sessions->fields['session_id']."\">\n";
  echo '<input type=submit value="' . 'Move to Orders database' .'">' . "\n";
  echo "</form><br />";

    foreach($mysess as $key=>$value) { 
      if ($key != "cart" && $key != "customer_id" && $key != "customer_first_name" && $key != 'language' && $key != 'paypal_transaction_info' && $key != 'customers_ip_address'  && $key != 'customer_last_name'  && $key != 'customer_last_name') { continue; }
      echo '<strong>' . $key . ':</strong>';   
      if($key == 'customer_id'){
        echo '<br /><br />' . $value . '&nbsp;<a href="' . zen_href_link(FILENAME_CUSTOMERS, 'cID=' . $value, 'NONSSL') . '" target="_blank">' . 'lookup customer (opens new window)' . '</a><br /><br />';
      }else{
        echo '<pre>';
        print_r($value);
        echo '</pre>';
      }
      /*
      echo "<strong>$key</strong> => <em>$value</em><br />"; 
        if (is_array($value) || is_object($value)) {
          foreach($value as $key2=>$value2) { 
            echo "&nbsp;&nbsp;&nbsp;&nbsp;<strong>$key2</strong> => <em>$value2</em><br />"; 
            if (is_array($value2) || is_object($value2)) {
              foreach($value2 as $key3=>$value3) { 
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>$key3</strong> => <em>$value3</em><br />"; 
                if (is_array($value3) || is_object($value3)) {
                  foreach($value3 as $key4=>$value4) { 
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>$key4</strong> => <em>$value4</em><br />"; 
                    if (is_array($value4) || is_object($value4)) {
                      foreach($value4 as $key5=>$value5) { 
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>$key5</strong> => <em>$value5</em><br />"; 
                        if (is_array($value5) || is_object($value5)) echo '<font color=red>MORE</font>';
                      }
                    }
                  }
                }
              }
            }
          }
        }
      */
    } // END foreach($mysess as $key=>$value) 
 // echo '--------------------------------------<br /><strong>Raw version:</strong><br />';
  //print_r($mysess);
  //echo '<hr style="height: 5px;" />';
  $stored_sessions->MoveNext();
}//end while
  echo '-------------------------------------------------------<br />END OF INFO';
} else {
  echo '<br /><br />No records to display.<br />';
}//endif $records > 0
?>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
