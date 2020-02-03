<?php

if (!defined('IS_ADMIN_FLAG')) { die('Illegal Access'); }

@require_once DIR_FS_ADMIN . DIR_WS_CLASSES . 'ano_plugin.php';
$plugin = new ano_plugin();
$plugin->install();