<?php
// -----
// Initialization script for the IP Blocker (ZC v1.5.0+)
//
// Copyright (C) 2014, Vinos de Frutas Tropicales (lat9)
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
// -----

// -----
// If the installation supports admin-page registration (i.e. v1.5.0 and later), then
// register the IP Blocker tool into the admin menu structure.
//
if (!zen_page_key_exists('toolsIPblocker')) {
  zen_register_admin_page('toolsIPblocker', 'BOX_TOOLS_IP_BLOCKER', 'FILENAME_IP_BLOCKER', '', 'tools', 'Y', 20);
}    

// -----
// If the IP blocker was not previously installed, create the ip_blocker table.
//
if (!$sniffer->table_exists (TABLE_IP_BLOCKER)) {
  $db->Execute ("CREATE TABLE " . TABLE_IP_BLOCKER . " (
    ib_id tinyint(1) unsigned NOT NULL auto_increment,
    ib_blocklist longtext NOT NULL default '',
    ib_passlist longtext NOT NULL default '',
    ib_blocklist_string longtext NOT NULL default '',
    ib_passlist_string longtext NOT NULL default '',
    ib_password varchar(50) NOT NULL default 'b58462dc43d1a1d1119a8049b426ecaa',
    ib_power tinyint(1) default 1,
    ib_date date NOT NULL default '0001-01-01',
    ib_lockout_count int(5) NOT NULL default 0,
    PRIMARY KEY (ib_id) ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET
    );
      
    $db->Execute("INSERT INTO " . TABLE_IP_BLOCKER . " (ib_date) VALUES('" . date ('Y-m-d') . "')");

} else {
  // -----
  // If the IP blocker was previously installed, but the login lockout count field doesn't exist,
  // add it.
  //
//-bof-v2.0.1a
  if (!$sniffer->field_exists(TABLE_IP_BLOCKER, 'ib_lockout_count')) {
    $db->Execute("ALTER TABLE " . TABLE_IP_BLOCKER . " ADD ib_lockout_count int(5) NOT NULL default '0'");
    
  }
//-eof-v2.0.1a
}