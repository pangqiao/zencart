<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The zen-cart developers                           |
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
// $Id: ip_blocker_functions.php, v1.0.0.0 2009/09/09 $d <noblesenior@gmail.com> $
// ------------------------------
// Modifications for Zen Cart v1.5.0+ by lat9, Copyright 2014, Vinos de Frutas Tropicales
// ------------------------------

// -----
// Returns a boolean flag to indicate whether or not the IP blocker is enabled.
//
function ip_blocker_is_enabled() {
  global $db;
  $result = $db->Execute('SELECT ib_power FROM ' . TABLE_IP_BLOCKER . ' WHERE ib_id=1');
  
  return ($result->fields['ib_power'] == 1);
  
}

// -----
// Convert the IP Blocker's "visual" form of the IP list to the serialized version that's used to compare
// a store-side IP address for the block/pass lists.  Will not update the database if an invalid IPv4 IP
// address is discovered.
//
// Returns an empty string ('') on success or an error message identifying the invalid IP address otherwise.
//
function ip_blocker_save_iplist ($iplist, $type = 'block') {
  global $db;

  if ($iplist == '') {
    $error_ip = '';
    $empty_list = serialize (array ());
    $db->Execute('UPDATE ' . TABLE_IP_BLOCKER . " SET ib_{$type}list = '$empty_list',  ib_{$type}list_string = '', ib_date = '" . date('Y-m-d') . "' WHERE ib_id=1");
    
  } else {
    $error_ip = ip_blocker_list_to_array ($iplist, $ip_db_array, false);
    if ($error_ip == '') {
      $ip_db_list = @zen_db_input (serialize($ip_db_array));
      $db->Execute('UPDATE ' . TABLE_IP_BLOCKER . " SET ib_{$type}list = '$ip_db_list', ib_{$type}list_string = '', ib_date = '" . date('Y-m-d') . "' WHERE ib_id=1");
    
    }
    
  }
  
  return $error_ip;
  
}

// -----
// Build the IP address list from the "visual" format, checking each IP address from that data
// input and returning either an empty string ('') if all addresses passed the validation check or
// an error message to be displayed identifying the invalid address.
//
function ip_blocker_list_to_array ($iplist, & $ip_db_array, $single_address_only = false) {
  $ip_db_array = array();
  $ip_db_temp_array = array();
  $error_ip = '';
  $ip_entries = explode("\r\n", trim ($iplist));
  if (!empty ($ip_entries)) {
    foreach ($ip_entries as $next_ip){
      if (!ip_blocker_validate_address ($next_ip, $ip_info, $single_address_only)) {
        $error_ip = sprintf (ERROR_NOT_SINGLE_ADDRESS, $next_ip);
        break;
      }
     
      if (!isset ($ip_info['range'])) {
        $ip_db_temp_array[] = $next_ip;
        
      } else {
        $ip_upper = $ip_info[0] . '.' . $ip_info[1] . '.' . $ip_info[2];
        for ($range = $ip_info['range'][0]; $range <= $ip_info['range'][1]; $range++) {
          $ip_db_temp_array[] = "$ip_upper.$range";
          
        }
      }
    }
    
    if ($error_ip == '') {
      $ip_db_temp_array = array_unique ($ip_db_temp_array);     
      usort ($ip_db_temp_array, 'ip_blocker_compare_address');
      
      foreach ($ip_db_temp_array as $next_ip) {
        if (strpos ($next_ip, '*') !== false) {
          if (isset ($saved_ips)) {
            $ip_db_array = array_merge ($ip_db_array, $saved_ips);
            unset ($ip_check_upper_octets, $ip_check_lower, $saved_ips);
            
          }
          $ip_db_array[] = $next_ip;
          ip_blocker_split_address ($next_ip, $ip_all_upper_octets);
          
        } elseif (isset ($ip_all_upper_octets) && strpos ($next_ip, $ip_all_upper_octets) === 0) {
          continue;
          
        } else {
          unset ($ip_all_upper_octets);
          $ip_lower = ip_blocker_split_address ($next_ip, $ip_upper_octets);
          if (isset ($ip_check_upper_octets)) {
            if ($ip_check_upper_octets == $ip_upper_octets) {
              if ($ip_lower == 255) {
                $ip_db_array[] = $ip_upper_octets . '*';
                unset ($ip_check_upper_octets, $ip_check_lower, $saved_ips);
                
              } elseif ($ip_lower == $ip_check_lower+1) {
                $saved_ips[] = $next_ip;
                $ip_check_lower = $ip_lower;
                
              } else {
                $ip_db_array = array_merge ($ip_db_array, $saved_ips);
                $ip_db_array[] = $next_ip;
                unset ($ip_check_upper_octets, $ip_check_lower, $saved_ips);

              }
            } else {
              $ip_db_array = array_merge ($ip_db_array, $saved_ips);
              if ($ip_lower == 0) {
                $saved_ips = array ($next_ip);
                $ip_check_upper_octets = $ip_upper_octets;
                $ip_check_lower = 0;
                
              } else {
                $ip_db_array[] = $next_ip;
                unset ($ip_check_upper_octets, $ip_check_lower, $saved_ips);
              }
              
            }
          } elseif ($ip_lower == 0) {
            $saved_ips = array ($next_ip );
            $ip_check_upper_octets = $ip_upper_octets;
            $ip_check_lower = 0;
            
          } else {
            $ip_db_array[] = $next_ip;
            
          }
        }
      }
      
      if (isset ($saved_ips)) {
        $ip_db_array = array_merge ($ip_db_array, $saved_ips);
        
      }
    }
  }
  
  return $error_ip;
  
}

// -----
// Insert a single ipV4 IP address into the IP Blocker blocked-address database table.
//
function ip_blocker_insert_block_address ($newaddress) {
  global $db;
  $error_ip = '';
  if (!ip_blocker_validate_address ($newaddress, $ip_info, true)) {
    $error_ip = sprintf (ERROR_NOT_SINGLE_ADDRESS, $newaddress);
    
  } else {
    $blocklist = $db->Execute ("SELECT ib_blocklist FROM " . TABLE_IP_BLOCKER . " WHERE ib_id=1");
    $blocked_ip_array = unserialize ($blocklist->fields['ib_blocklist']);
    
    $is_new_address = false;
    if (!in_array ($newaddress, $blocked_ip_array)) {
      $newaddress_all = substr ($newaddress, 0, strrpos ($newaddress, '.')) . '*';
      if (!in_array ($newaddress_all, $blocked_ip_array)) {
        $blocked_ip_array[] = $newaddress;
        $is_new_address = true;
        
      }
    }
    
    if ($is_new_address) {
      $blocked_ip_list = serialize ($blocked_ip_array);
      $db->Execute('UPDATE ' . TABLE_IP_BLOCKER . " SET ib_blocklist = '$blocked_ip_list', ib_blocklist_string = '', ib_date = '" . date('Y-m-d') . "' WHERE ib_id=1");
      
    }
    
  }
  
  return $error_ip;
  
}

// -----
// Validate a single IP-address specification.  Valid:
//
// - 192.16.1.255
// - 192.168.1.*
// - 192.168.2.3/6
//
// Invalid:
// - 888.16.1.255
// - 192.168.*
// - 192.168.2.6/3
//
function ip_blocker_validate_address ($ip, & $ip_info, $single_address_only = false) {
  $is_valid = true;

  $ip_info = explode ('.', $ip);
  if (count ($ip_info) != 4) {
    $is_valid = false;
    
  } else {
    for ($i = 0; $i < 4 && $is_valid; $i++) {
      if (!(ctype_digit ($ip_info[$i]) && $ip_info[$i] >= 0 && $ip_info[$i] <= 255)) {
        if ($i != 3 || ($i == 3 && $single_address_only)) {
          $is_valid = false;
          
        } else {
          if ($ip_info[$i] != '*') {
            $ip_info['range'] = explode ('/', $ip_info[$i]);
            if (count ($ip_info['range']) != 2) {
              $is_valid = false;
            } else {
              for ($j = 0; $j < 2 && $is_valid; $j++) {
                if (!(ctype_digit ($ip_info['range'][$j]) && $ip_info['range'][$j] >= 0 && $ip_info['range'][$j] <= 255)) {
                  $is_valid = false;
                }
              }
              if ($ip_info['range'][0] >= $ip_info['range'][1]) {
                $is_valid = false;
              }
            }
          }
        }
      }
    }
  }
  return $is_valid;
}

// -----
// Function used to sort two IPv4 addresses, called via usort, by ip_blocker_array_to_list and ip_blocker_list_to_array.
//
function ip_blocker_compare_address ($a, $b) {
  $compare = 0;
  if ($a != $b) {
    $a_quad = explode ('.', $a);
    $b_quad = explode ('.', $b);
    for ($i = 0; $i < 4 && $compare == 0; $i++) {
      if ($a_quad[$i] != $b_quad[$i]) {
        $compare = ($a_quad[$i] < $b_quad[$i]) ? -1 : 1;
      }
    }
  }
  return $compare;
}

// -----
// Break up an IPv4 address into two strings, one containing the upper 3 octets (including the final .) and the other
// containing the lower octet value.
//
function ip_blocker_split_address ($ip_in, & $ip_upper_octets) {
  $last_dot_position = strrpos ($ip_in, '.');
  $ip_upper_octets = substr ($ip_in, 0, $last_dot_position+1);
  
  return substr ($ip_in, $last_dot_position+1);
  
}

// -----
// Function that converts an array of database-formatted IP addresses into a compacted list, with duplicate entries removed.
// The list is, after imploding, suitable for display within a textarea box.
//
function ip_blocker_array_to_list ($ip_db_array) {
  $return_array = $ip_db_array;
  if (is_array ($ip_db_array)) {
    $return_array = array_unique ($ip_db_array);
    usort ($return_array, 'ip_blocker_compare_address');
    
    $ip_list_array = array ();
    $ip_start = '';
    foreach ($return_array as $ip) {
      if (strpos ($ip, '*') !== false) {
        if ($ip_start != '') {
          if (!isset ($ip_range_end)) {
            $ip_list_array[] = $ip_start;
            
          } else {
            $ip_list_array[] = $ip_start . '/' . $ip_range_end;
            
          }
        }
        $ip_list_array[] = $ip;
        $ip_start = '';
        ip_blocker_split_address ($ip, $ip_all_upper_octets);
        
      } elseif (isset ($ip_all_upper_octets) && strpos ($ip, $ip_all_upper_octets) === 0) {
        continue;
        
      } elseif ($ip_start == '') {
        $ip_start = $ip;
        $ip_range_start = ip_blocker_split_address ($ip_start, $ip_start_upper_octets);
        unset ($ip_range_end, $ip_all_upper_octets);
        
      } else {
        $ip_range = ip_blocker_split_address ($ip, $ip_range_upper_octets);
        if (!isset ($ip_range_end)) {
          if ($ip_range_upper_octets == $ip_start_upper_octets && $ip_range == $ip_range_start + 1) {
            $ip_range_end = $ip_range;
            
          } else {
            $ip_list_array[] = $ip_start;
            $ip_start = $ip;
            $ip_range_start = ip_blocker_split_address ($ip_start, $ip_start_upper_octets);
            
          }
        } else {
          if ($ip_range == $ip_range_end + 1) {
            $ip_range_end = $ip_range;
            
          } else {
            if ($ip_range == 255 && $ip_range_start == 0) {
              $ip_list_array[] = $ip_start_upper_octets . '*';
              $ip_start = '';

            } else {
              $ip_list_array[] = $ip_start . '/' . $ip_range_end;
              $ip_start = $ip;
              $ip_range_start = ip_blocker_split_address ($ip_start, $ip_start_upper_octets);
              
            }
            unset ($ip_range_end);
            
          }
        }    
      }
    }
    
    if ($ip_start != '') {
      if (!isset ($ip_range_end)) {
        $ip_list_array[] = $ip_start;
        
      } elseif ($ip_range_start == 0 && $ip_range_end == 255) {
        $ip_list_array[] = $ip_start_upper_octets . '*';
        
      } else {
        $ip_list_array[] = $ip_start . '/' . $ip_range_end;
        
      }
    }
    
    $return_array = $ip_list_array;
    
  }

  return $return_array;
  
}