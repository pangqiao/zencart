<?php
// ---------------------------------------------------------------------------
// Part of the IP Blocker plugin for Zen Cart v1.5.0 and later
//
// Copyright (C) 2014, Vinos de Frutas Tropicales (lat9)
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
// ---------------------------------------------------------------------------

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
} 
$autoLoadConfig[200][] = array(
  'autoType'  => 'init_script',
  'loadFile'  => 'init_ip_blocker.php');