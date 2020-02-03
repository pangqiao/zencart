<?php
if (!defined('IS_ADMIN_FLAG')) {
	    die('Illegal Access');
} 

if (function_exists('zen_register_admin_page')) {
	    if (!zen_page_key_exists('easypopulate')) {
		             zen_register_admin_page('easypopulate', 'BOX_TOOLS_EASYPOPULATE',
				                 'FILENAME_EASYPOPULATE','' , 'tools', 'Y', 20);
			         }         
}

?>
