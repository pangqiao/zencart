<?php
/**
 * IP Blocker functions
 *
 * @package functions
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: ip_blocker.php , v1.0.0 2009/09/09 $d <noblesenior@gmail.com> $
 */
// --------------------
// v2.0.0 for Zen Cart v1.5.0+, reworked as an init_script by lat9@vinosdefrutastropicales.com
// --------------------

// -----
// Check if the IP submitted is in the IP block-list
//
function ip_blocker_block($ip){
  global $db, $blocklist;
  
  $blocklist = $db->Execute('SELECT ib_blocklist FROM `' . TABLE_IP_BLOCKER . '` WHERE ib_id=1');
  $blocklist = $blocklist->fields['ib_blocklist'];
  $blocklist = $blocklist == '' ? @unserialize(array()) : @unserialize($blocklist);

  if (! is_array($blocklist) || empty($blocklist)) {
    return false;
  }
  
  foreach ($blocklist as $block){
    if ($ip == $block || preg_match('/^' . $block . '/', $ip)) {
      return true;
    }
  }
  
  return false;
}

// -----
// Check if the IP submitted is in the IP pass-list
//
function ip_blocker_pass($ip){
  global $db;
  
  $passlist = $db->Execute('SELECT ib_passlist FROM `' . TABLE_IP_BLOCKER . '` WHERE ib_id=1');
  $passlist = $passlist->fields['ib_passlist'];
  $passlist = ($passlist == '') ? @unserialize(array()) : @unserialize($passlist);
  
  if (! is_array($passlist) || empty($passlist)) {
    return false;
  }
  
  foreach ($passlist as $pass){
    if ($ip == $pass || preg_match('/^' . $pass . '/', $ip)) {
      return true;
    }
  }
  
  return false;
}

// -----
// If the special login script (the IP blocker) is *not* running and the IP blocker is installed ...
//
if (strpos($_SERVER['SCRIPT_NAME'], 'special_login') === false && $sniffer->table_exists(TABLE_IP_BLOCKER)) {  //-v2.0.2c
  $ib_result = $db->Execute('SELECT ib_power FROM `' . TABLE_IP_BLOCKER . '` WHERE ib_id=1');

  // -----
  // ... and enabled ...
  //
  if (!$ib_result->EOF && ((bool)$ib_result->fields['ib_power'])) {
    // -----
    // ... and the current IP has either not yet been checked or has not passed the block-check ...
    //
    if (!isset($_SESSION['ip_blocker_pass']) || $_SESSION['ip_blocker_pass'] !== true) {
      $ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) || isset($_SERVER['HTTP_VIA'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
      
      // -----
      // ... and the IP is not in the pass-list but is in the blocked list, transfer control to the IP blocker's "special" login page.
      //
      if (!ip_blocker_pass($ip) && ip_blocker_block($ip)) {
        zen_redirect (HTTP_SERVER . '/special_login.php');  //-v2.0.2c-Use built-in zen functions
        
      }
      
      $_SESSION['ip_blocker_pass'] = true;
 
    }
  }  
}
// $_SESSION['ip_blocker_pass'] = false; (removed v2.0.2)