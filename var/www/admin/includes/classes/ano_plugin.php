<?php

@require_once DIR_FS_ADMIN . DIR_WS_CLASSES . 'plugin.php';
class ano_plugin extends plugin {
	public function getVersion() { return '1.4.1'; }
	public function getUniqueKey() { return 'ANO'; }
	public function getUniqueName() { return 'Admin New Order'; }
	public function getDescription() { return 'Settings for Admin New Order'; }
	public function getNewFiles() {
		return array(
			DIR_FS_ADMIN . 'new_order.php',
			DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/new_order.php',
			DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/new_order.php',
			DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/new_order.php',
			DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/images/buttons/button_new_order.gif'
		);
	}
	public function getObsoleteFiles() {
		return array(
			DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'init_includes/init_new_order.php'
		);
	}
	public function handleAdminMenuChanges($install = true) {
		global $db;

		$success = true;
		if($install) {
			if(!zen_page_key_exists('newOrder')) {
				// Get the sort order
				$page_sort_query = "SELECT MAX(sort_order) as max_sort FROM `". TABLE_ADMIN_PAGES ."` WHERE menu_key='modules'";
				$page_sort = $db->Execute($page_sort_query);
				$page_sort = $page_sort->fields['max_sort'] + 1;

				// Register the administrative pages
				zen_register_admin_page('newOrder', 'BOX_CONFIGURATION_NEW_ORDER',
				'FILENAME_NEW_ORDER', '', 'modules', 'N', $page_sort);
			}
			if(!zen_page_key_exists('newOrder')) $success = false;
		}
		else {
			if(zen_page_key_exists('newOrder')) zen_deregister_admin_pages('newOrder');
			if(zen_page_key_exists('newOrder')) $success = false;
		}

		return $success;
	}

	/* (non-PHPdoc)
	 * Starts by disabling the plugin if enabled
	*
	* @see plugin::install()
	*/
	public function install() {
		global $messageStack;

		// Determine if Edit Orders is installed before continuing
		$success = true;
		if(!defined('EO_VERSION') || version_compare(EO_VERSION, '4.0', '<')) {
			$messageStack->add(sprintf(ANO_PLUGIN_INSTALL_ERROR_EO_NOT_FOUND, $this->getUniqueName()), 'error');
			$success = false;
		}

		if($success) { $success = parent::install(); }
		return $success;
	}
}