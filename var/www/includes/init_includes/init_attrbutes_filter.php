<?php
if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

$attr_get_request = '';
$attr_id_array = array();

if (zen_not_null($_GET['options_values_id']) && preg_match('/\d+(_\d+)?/', $_GET['options_values_id'])) {
  $attr_get_request = $_GET['options_values_id'];
  $attr_id_array = explode('_', $attr_get_request);
}