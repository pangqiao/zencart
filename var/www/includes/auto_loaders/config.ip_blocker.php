<?php
// -----
// Part of the IP Blocker plugin, provided by lat9@vinosdefrutastropicales.com
//
// @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
//
/*
** point 151 is after the special functions have been initialized.
*/
$autoLoadConfig[71][] = array('autoType' => 'init_script',
                              'loadFile' => 'init_ip_blocker.php');