<?php
if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

// Define the version. This will be important in future releases.
$version = '2.212';

// This block installs the plugin / module
if(!ultimate_seo_install($version)) {
	$failed = false;

	// Disable this script from running again
	if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'))
	{
		if(!unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'))
		{
			$messageStack->add(sprintf(SEO_INSTALL_ERROR_AUTOLOAD, DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'), 'error');
			$failed = true;
		}
	}

	if(!$failed) {
		zen_db_perform(TABLE_CONFIGURATION, array('configuration_value' => 'true'), 'update', '`configuration_key`=\'SEO_ENABLED\'');
		$messageStack->add(SEO_INSTALL_SUCCESS, 'success');
	}
}
else {
	$messageStack->add(SEO_INSTALL_ERROR, 'error');
}

// This block uninstalls the plugin / module
/*if(!ultimate_seo_uninstall()) {
	$failed = false;

	// Disable this script from running again
	if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'))
	{
		if(!unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'))
		{
			$messageStack->add(sprintf(SEO_INSTALL_ERROR_AUTOLOAD, DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.seo.install.php'), 'error');
			$failed = true;
		}
	}

	if(!$failed) $messageStack->add(SEO_UNINSTALL_SUCCESS, 'success');
}
else {
	$messageStack->add(SEO_UNINSTALL_ERROR, 'error');
}*/

function ultimate_seo_default_settings($version) {
	// Do not change these unless you really know what you are doing.
	// The language specific items are now stored in the language file.
	return array(
		'USU_VERSION' => array('configuration_value' => '\'' . $version . '\'', 'use_function' => '', 'set_function' => 'zen_cfg_select_option(array(\\\'' . $version . '\\\'),'),
		'SEO_ENABLED' => array('configuration_value' => 'false', 'use_function' => '', 'set_function' => 'zen_cfg_select_option(array(\\\'true\\\', \\\'false\\\'),'),
		'SEO_URL_CPATH' => array('configuration_value' => 'auto', 'use_function' => 'usu_check_cpath_option', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-auto\\\', \\\'disable\\\'),'),
		'SEO_URL_END' => array('configuration_value' => '.html', 'use_function' => '', 'set_function' => ''),
		'SEO_URL_FORMAT' => array('configuration_value' => 'original', 'use_function' => 'usu_check_url_format_option', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-original\\\', \\\'enable-parent\\\'),'),
		'SEO_URL_CATEGORY_DIR' => array('configuration_value' => 'short', 'use_function' => 'usu_check_category_dir_option', 'set_function' => 'zen_cfg_select_option(array(\\\'disable\\\', \\\'enable-short\\\', \\\'enable-full\\\'),'),
		'SEO_URLS_FILTER_PCRE' => array('configuration_value' => '', 'use_function' => '', 'set_function' => ''),
		'SEO_URLS_FILTER_CHARS' => array('configuration_value' => '', 'use_function' => '', 'set_function' => ''),
		'SEO_URLS_REMOVE_CHARS' => array('configuration_value' => 'punctuation', 'use_function' => 'usu_check_remove_chars_option', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-non-alphanumerical\\\', \\\'enable-punctuation\\\'),'),
		'SEO_URLS_FILTER_SHORT_WORDS' => array('configuration_value' => '0', 'use_function' => '', 'set_function' => ''),
		'SEO_URLS_ONLY_IN' => array('configuration_value' => 'index, product_info, product_music_info, document_general_info, document_product_info, product_free_shipping_info, products_new, products_all, shopping_cart, featured_products, specials, contact_us, conditions, privacy, reviews, shippinginfo, faqs_all, site_map, gv_faq, discount_coupon, page, page_2, page_3, page_4', 'use_function' => '', 'set_function' => ''),
		'SEO_REWRITE_TYPE' => array('configuration_value' => 'rewrite', 'use_function' => '', 'set_function' => 'zen_cfg_select_option(array(\\\'rewrite\\\'),'),
		'SEO_USE_REDIRECT' => array('configuration_value' => 'false', 'use_function' => '', 'set_function' => 'zen_cfg_select_option(array(\\\'true\\\', \\\'false\\\'),'),
		'SEO_USE_CACHE_GLOBAL' => array('configuration_value' => 'true', 'use_function' => 'usu_check_cache_options', 'set_function' => 'zen_cfg_select_option(array(\\\'enable\\\', \\\'disable\\\'),'),
		'SEO_USE_CACHE_PRODUCTS' => array('configuration_value' => 'true', 'use_function' => 'usu_check_cache_options', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-products\\\', \\\'disable-products\\\'),'),
		'SEO_USE_CACHE_CATEGORIES' => array('configuration_value' => 'true', 'use_function' => 'usu_check_cache_options', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-categories\\\', \\\'disable-categories\\\'),'),
		'SEO_USE_CACHE_MANUFACTURERS' => array('configuration_value' => 'true', 'use_function' => 'usu_check_cache_options', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-manufacturers\\\', \\\'disable-manufacturers\\\'),'),
		'SEO_USE_CACHE_EZ_PAGES' => array('configuration_value' => 'true', 'use_function' => 'usu_check_cache_options', 'set_function' => 'zen_cfg_select_option(array(\\\'enable-ez_pages\\\', \\\'disable-ez_pages\\\'),'),
		'SEO_URLS_CACHE_RESET' => array('configuration_value' => 'false', 'use_function' => 'usu_reset_cache_data', 'set_function' => 'zen_cfg_select_option(array(\\\'true\\\', \\\'false\\\'),'),
	);
}

function ultimate_seo_install($version) {
	global $db, $messageStack;

	$failed = false;

	// Check to make sure all new files have been uploaded.
	// These are not intended to be perfect checks, just a quick 'hey you'.
	$files = array(
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/seo.php',
		DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'extra_functions/seo.php',
		DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/seo.php',

		DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/seo.php',
		DIR_FS_CATALOG . DIR_WS_INCLUDES . 'auto_loaders/config.seo.php',
		DIR_FS_CATALOG . DIR_WS_INCLUDES . 'init_includes/init_seo_config.php',
		DIR_FS_CATALOG . DIR_WS_CLASSES . 'seo.url.php',
	);
	foreach($files as $file) {
		if(!file_exists($file)) {
			$messageStack->add(sprintf(SEO_INSTALL_ERROR_FILE_NOT_FOUND, $file), 'error');
			$failed = true;
		}
	}

	// Attempt a database upgrade before doing anything else.
	ultimate_seo_upgrade($version);

	// Now check the required tables if not already present
	$check = $db->Execute(
		'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES ' .
		'WHERE TABLE_SCHEMA = \'' . DB_DATABASE . '\' ' .
		'AND TABLE_NAME = \'' . TABLE_SEO_CACHE . '\''
	);
	if($check->EOF) {
		$db->Execute(
			'CREATE TABLE `' . TABLE_SEO_CACHE . '` ( ' .
				'`cache_id` VARCHAR(32) NOT NULL default \'\', ' .
				'`cache_language_id` TINYINT(1) NOT NULL default \'0\', ' .
				'`cache_name` VARCHAR(255) NOT NULL default \'\', ' .
				'`cache_data` MEDIUMTEXT NOT NULL, ' .
				'`cache_global` TINYINT(1) NOT NULL default \'1\', ' .
				'`cache_gzip` TINYINT(1) NOT NULL default \'1\', ' .
				'`cache_method` VARCHAR(20) NOT NULL default \'RETURN\', ' .
				'`cache_date` DATETIME NOT NULL default \'0000-00-00 00:00:00\', ' .
				'`cache_expires` DATETIME NOT NULL default \'0000-00-00 00:00:00\', ' .
				'PRIMARY KEY (`cache_id`,`cache_language_id`), ' .
				'KEY `cache_id` (`cache_id`), ' .
				'KEY `cache_language_id` (`cache_language_id`), ' .
				'KEY `cache_global` (`cache_global`) ' .
			') ENGINE=MyISAM'
		);
	}

	// Now check the configuration group
	$group_id = 0;
	$check = $db->Execute(
		'SELECT `configuration_group_id` FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
		'WHERE `configuration_group_title` = \'' . SEO_CONFIGURATION_GROUP_TITLE . '\''
	);
	if($check->EOF) {
		$max_sort = $db->Execute(
			'SELECT MAX(sort_order) AS `max_sort` FROM `' . TABLE_CONFIGURATION_GROUP . '`'
		);
		if(!$max_sort->EOF) {
			$max_sort = $max_sort->fields['max_sort'] + 1;

			// Create configuration group
			$db->Execute(
				'INSERT INTO `' . TABLE_CONFIGURATION_GROUP . '` ' .
				'VALUES (\'\', \'' . SEO_CONFIGURATION_GROUP_TITLE . '\', ' .
				'\''. SEO_CONFIGURATION_GROUP_DESCRIPTION . '\', \'' . $max_sort . '\', \'1\')'
			);
			$group_id = (int) $db->insert_ID();
		}
		else {
			$messageStack->add(sprintf(SEO_INSTALL_ERROR_SORT_ORDER, TABLE_CONFIGURATION_GROUP), 'error');
			$failed = true;
		}
	}
	else {
		$group_id = (int) $check->fields['configuration_group_id'];
	}

	// Install (or update) any needed configuration items
	if($group_id != 0) {
		$sql_data_array = array(
			'configuration_group_id' => $group_id,
			'sort_order' => 0,
			'last_modified' => 'null',
			'date_added' => 'now()'
		);

		foreach(ultimate_seo_default_settings($version) as $key => $data) {
			$check = $db->Execute(
				'SELECT `configuration_id` FROM `' . TABLE_CONFIGURATION . '` ' .
				'WHERE `configuration_key`=\'' . $key . '\''
			);
			if($check->EOF) {
				$data['configuration_key'] = $key;
				$data['configuration_title'] = constant($key . '_TITLE');
				$data['configuration_description'] = constant($key . '_DESCRIPTION');
				zen_db_perform(TABLE_CONFIGURATION, array_merge($sql_data_array, $data));
			}
			else {
				unset($data['configuration_value']);
				ultimate_seo_upgrade_option($key, array_merge(
					$data,
					array('sort_order' => $sql_data_array['sort_order'])
				));
			}

			$sql_data_array['sort_order']++;
		}

		// Update the version stored in the database for this plugin
		$sql_data_array = array(
			'last_modified' => 'now()',
			'configuration_value' => $version,
			'set_function' => 'zen_cfg_select_option(array(\\\'' . $version . '\\\'),'
		);
		zen_db_perform(TABLE_CONFIGURATION, $sql_data_array, 'update', '`configuration_key`=\'USU_VERSION\'');
		unset($sql_data_array);

		// Reset the cache to deal with any stale entries
		usu_reset_cache_data('true');
	}
	unset($defaults);

	// Add support for admin profiles
	if(function_exists('zen_register_admin_page')) {
		if(!zen_page_key_exists('configUltimateSEO')) {
			$max_sort = $db->Execute(
				'SELECT MAX(sort_order) AS `max_sort` FROM `' . TABLE_ADMIN_PAGES . '` WHERE `menu_key`=\'configuration\''
			);
			if(!$max_sort->EOF) {
				$max_sort = $max_sort->fields['max_sort'] + 1;

				// Register the administrative page
				zen_register_admin_page(
					'configUltimateSEO', 'SEO_CONFIGURATION_GROUP_TITLE', 'FILENAME_CONFIGURATION',
					'gID=' . $group_id, 'configuration', 'Y', $max_sort
				);
			}
			else {
				$messageStack->add(sprintf(SEO_INSTALL_ERROR_SORT_ORDER, TABLE_ADMIN_PAGES), 'error');
				$failed = true;
			}
		}
	}

	return $failed;
}

function ultimate_seo_upgrade($version) {
	global $db, $messageStack;

	$failed = false;
	$old_version = '-1';

	// First see if this module has been previously installed
	$check = $db->Execute(
		'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES ' .
		'WHERE TABLE_SCHEMA = \'' . DB_DATABASE . '\' ' .
		'AND TABLE_NAME = \'' . TABLE_SEO_CACHE . '\''
	);
	if(!$check->EOF) {
		$old_version = '2.150';

		// Disable rewriter until configuration is complete
		zen_db_perform(TABLE_CONFIGURATION, array('configuration_value' => 'false'), 'update', '`configuration_key`=\'SEO_ENABLED\'');
	}

	// Grab the currently installed version
	$check = $db->Execute(
		'SELECT `configuration_value` FROM `' . TABLE_CONFIGURATION . '` WHERE `configuration_key` = \'USU_VERSION\''
	);
	if(!$check->EOF) {
		$old_version = $check->fields['configuration_value'];
	}

	// No upgrade needed as this plugin has never been installed
	if($old_version == '-1') return false;

	// Retrieve the new default settings
	$default = ultimate_seo_default_settings($version);

	// Add the new sort order and remove the default value
	$sort = array();
	$keys = array_keys($default);
	for($i=0,$n=sizeof($keys); $i<$n; $i++) {
		$default[$keys[$i]]['sort_order'] = $i;
		unset($default[$keys[$i]]['configuration_value']);
	}
	unset($keys, $i, $n);

	// Apply various database updates
	switch($old_version) {
		case '2.150':
			if(defined('SEO_ADD_PRODUCT_CAT')) {
				$data = $default['SEO_URL_FORMAT'];
				$data['configuration_key'] = 'SEO_URL_FORMAT';
				$data['configuration_value'] = (SEO_ADD_PRODUCT_CAT == 'true' ? 'parent' : 'original');
				if(defined('SEO_ADD_CAT_PARENT') && SEO_ADD_CAT_PARENT == 'true') $data['configuration_value'] = 'original';
				ultimate_seo_upgrade_option('SEO_ADD_PRODUCT_CAT', $data);
			}

			if(defined('SEO_ADD_CAT_PARENT')) {
				$data = $default['SEO_URL_CATEGORY_DIR'];
				$data['configuration_key'] = 'SEO_URL_CATEGORY_DIR';
				$data['configuration_value'] = (SEO_ADD_CAT_PARENT == 'false' ? 'off' : 'full');
				ultimate_seo_upgrade_option('SEO_ADD_CAT_PARENT', $data);
			}

			if(defined('SEO_ADD_CPATH_TO_PRODUCT_URLS')) {
				$data = $default['SEO_URL_CPATH'];
				$data['configuration_key'] = 'SEO_URL_CPATH';
				$data['configuration_value'] = (SEO_ADD_CPATH_TO_PRODUCT_URLS == 'false' ? 'off' : 'auto');
				ultimate_seo_upgrade_option('SEO_ADD_CPATH_TO_PRODUCT_URLS', $data);
			}

			$data = $default['SEO_URLS_FILTER_CHARS'];
			$data['configuration_key'] = 'SEO_URLS_FILTER_CHARS';
			ultimate_seo_upgrade_option('SEO_CHAR_CONVERT_SET', $data);

			if(defined('SEO_REMOVE_ALL_SPEC_CHARS')) {
				$data = $default['SEO_URLS_REMOVE_CHARS'];
				$data['configuration_key'] = 'SEO_URLS_REMOVE_CHARS';
				$data['configuration_value'] = (SEO_URLS_REMOVE_CHARS == 'true' ? 'alphanumerical' : 'special');
				ultimate_seo_upgrade_option('SEO_REMOVE_ALL_SPEC_CHARS', $data);
			}

			$data = $default['SEO_REWRITE_TYPE'];
			$data['configuration_value'] = 'rewrite';
			ultimate_seo_upgrade_option('SEO_REWRITE_TYPE', $data);

			$data = $default['SEO_USE_REDIRECT'];
			$data['configuration_key'] = 'SEO_USE_REDIRECT';
			ultimate_seo_upgrade_option('USE_SEO_REDIRECT', $data);

			$data = $default['SEO_USE_CACHE_GLOBAL'];
			$data['configuration_key'] = 'SEO_USE_CACHE_GLOBAL';
			ultimate_seo_upgrade_option('USE_SEO_CACHE_GLOBAL', $data);

			$data = $default['SEO_USE_CACHE_PRODUCTS'];
			$data['configuration_key'] = 'SEO_USE_CACHE_PRODUCTS';
			ultimate_seo_upgrade_option('USE_SEO_CACHE_PRODUCTS', $data);

			$data = $default['SEO_USE_CACHE_CATEGORIES'];
			$data['configuration_key'] = 'SEO_USE_CACHE_CATEGORIES';
			ultimate_seo_upgrade_option('USE_SEO_CACHE_CATEGORIES', $data);

			$data = $default['SEO_USE_CACHE_MANUFACTURERS'];
			$data['configuration_key'] = 'SEO_USE_CACHE_MANUFACTURERS';
			ultimate_seo_upgrade_option('USE_SEO_CACHE_MANUFACTURERS', $data);

			if(defined('USE_SEO_CACHE_EZ_PAGES')) {
				$data = $default['SEO_USE_CACHE_EZ_PAGES'];
				$data['configuration_key'] = 'SEO_USE_CACHE_EZ_PAGES';
				ultimate_seo_upgrade_option('USE_SEO_CACHE_EZ_PAGES', $data);
			}

			// Remove older options which are no longer used
			$old_options = array(
				'USE_SEO_CACHE_ARTICLES' , 'USE_SEO_CACHE_INFO_PAGES',
				'SEO_URLS_USE_W3C_VALID'
			);
			foreach($old_options as $option) {
				if(defined($option)) {
					$db->Execute(
						'DELETE FROM `' . TABLE_CONFIGURATION . '` ' .
						'WHERE `configuration_key`=\'' . $option . '\''
					);
				}
			}

			if(function_exists('zen_deregister_admin_pages') && zen_page_key_exists('UltimateSEO')) {
				zen_deregister_admin_pages('UltimateSEO');
				if(zen_page_key_exists('UltimateSEO')) {
					$messageStack->add(SEO_UNINSTALL_ERROR_ADMIN_PAGES);
					$failed = true;
				}
			}

			// Remove this file if it exists (should no longer be used)
			if(file_exists(DIR_FS_CATALOG . DIR_WS_CLASSES . 'seo.install.php') && !unlink(DIR_FS_CATALOG . DIR_WS_CLASSES . 'seo.install.php')) {
				$messageStack->add(sprintf(SEO_INSTALL_ERROR_FILE_FOUND, DIR_FS_CATALOG . DIR_WS_CLASSES . 'seo.install.php'), 'error');
				$failed = true;
			}
			// Remove this file if it exists (should no longer be used)
			if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'reset_seo_cache.php') && !unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'reset_seo_cache.php')) {
				$messageStack->add(sprintf(SEO_INSTALL_ERROR_FILE_FOUND, DIR_FS_ADMIN . DIR_WS_INCLUDES . 'reset_seo_cache.php'), 'error');
				$failed = true;
			}

			// Remove this file if it exists (should no longer be used)
			if(file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'auto_loaders/config.ultimate_seo.php') && !unlink(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'auto_loaders/config.ultimate_seo.php')) {
				$messageStack->add(sprintf(SEO_INSTALL_ERROR_FILE_FOUND, DIR_FS_CATALOG . DIR_WS_INCLUDES . 'auto_loaders/config.ultimate_seo.php'), 'error');
				$failed = true;
			}

			// Udate the configuration title if an old one exists
			$check = $db->Execute(
				'SELECT `configuration_group_id` FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
				'WHERE `configuration_group_title` = \'SEO URLs\''
			);
			if(!$check->EOF) {
				zen_db_perform(TABLE_CONFIGURATION_GROUP, array('configuration_group_title' => SEO_CONFIGURATION_GROUP_TITLE), 'update', '`configuration_group_id`=\'' . $check->fields['configuration_group_id'] .'\'');
			}

			// Update the admin page language key
			$check = $db->Execute(
				'SELECT `page_key` FROM `' . TABLE_ADMIN_PAGES . '` ' .
				'WHERE `page_key` = \'configUltimateSEO\''
			);
			if(!$check->EOF) {
				zen_db_perform(TABLE_ADMIN_PAGES, array('language_key' => 'SEO_CONFIGURATION_GROUP_TITLE'), 'update', '`page_key`=\'' . $check->fields['page_key'] .'\'');
			}

			// Remove the old admin page if present
			if(function_exists('zen_deregister_admin_pages')) {
				if(zen_page_key_exists('UltimateSEO')) zen_deregister_admin_pages('UltimateSEO');
			}

		case '2.210':
			// Forgot to enforce this during an upgrade from earlier versions
			if(SEO_URL_CATEGORY_DIR == 'full' && SEO_URL_FORMAT == 'parent') {
				$data = $default['SEO_URL_FORMAT'];
				$data['configuration_value'] = 'original';
				ultimate_seo_upgrade_option('SEO_URL_FORMAT', $data);
			}

		case '2.211':
			// Remove older option which is no longer used (if present)
			if(defined('SEO_REMOVE_ALL_SPEC_CHARS')) {
				$db->Execute(
					'DELETE FROM `' . TABLE_CONFIGURATION . '` ' .
					'WHERE `configuration_key`=\'SEO_REMOVE_ALL_SPEC_CHARS\''
				);
			}
			// Cleanup a bug in BETA test releases (should not affect most people)
			$db->Execute(
				'DELETE FROM `' . TABLE_CONFIGURATION . '` ' .
				'WHERE `configuration_key`=\'rewrite\' AND `configuration_value`=\'Rewrite\''
			);

			// Update some configuration options
			$data = $default['SEO_URLS_REMOVE_CHARS'];
			$data['configuration_value'] = (SEO_URLS_REMOVE_CHARS == 'alphanumerical' ? 'non-alphanumerical' : 'punctuation');
			ultimate_seo_upgrade_option('SEO_URLS_REMOVE_CHARS', $data);

		default:
	}

	return !$failed;
}

function ultimate_seo_uninstall() {
	global $db, $messageStack;

	$failed = false;
	zen_db_perform(TABLE_CONFIGURATION, array('configuration_value' => 'false'), 'update', '`configuration_key`=\'SEO_ENABLED\'');

	$db->Execute(
		'DELETE FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
		'WHERE `configuration_group_title` = \'' . SEO_CONFIGURATION_GROUP_TITLE . '\''
	);
	$check = $db->Execute(
		'SELECT `configuration_group_title` FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
		'WHERE `configuration_group_title` = \'' . SEO_CONFIGURATION_GROUP_TITLE . '\''
	);
	if(!$check->EOF) {
		$messageStack->add(sprintf(SEO_UNINSTALL_ERROR_DELETE, 'configuration group: ' . SEO_CONFIGURATION_GROUP_TITLE, TABLE_CONFIGURATION_GROUP), 'error');
		$failed = true;
	}

	$db->Execute(
		'DELETE FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
		'WHERE `configuration_group_title` = \'SEO URLs\''
	);
	$check = $db->Execute(
		'SELECT `configuration_group_title` FROM `' . TABLE_CONFIGURATION_GROUP . '` ' .
		'WHERE `configuration_group_title` = \'SEO URLs\''
	);
	if(!$check->EOF) {
		$messageStack->add(sprintf(SEO_UNINSTALL_ERROR_DELETE, 'configuration group: ' . SEO_CONFIGURATION_GROUP_TITLE, 'SEO URLs'), 'error');
		$failed = true;
	}

	foreach(ultimate_seo_default_settings() as $option => $value) {
		if(defined($option)) {
			$db->Execute(
				'DELETE FROM `' . TABLE_CONFIGURATION . '` ' .
				'WHERE `configuration_key`=\'' . $option . '\''
			);
			$check = $db->Execute(
				'SELECT `configuration_id` FROM `' . TABLE_CONFIGURATION . '` ' .
				'WHERE `configuration_key` = \'' . $option . '\''
			);
			if(!$check->EOF) {
				$messageStack->add(sprintf(SEO_UNINSTALL_ERROR_DELETE, 'configuration option: ' . $option, TABLE_CONFIGURATION), 'error');
				$failed = true;
			}
		}
	}

	$db->Execute('DROP TABLE IF EXISTS ' . TABLE_SEO_CACHE);
	$check = $db->Execute(
		'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES ' .
		'WHERE TABLE_SCHEMA = \'' . DB_DATABASE . '\' ' .
		'AND TABLE_NAME = \'' . TABLE_SEO_CACHE . '\''
	);
	if(!$check->EOF) {
		$messageStack->add(sprintf(SEO_UNINSTALL_ERROR_TABLE, TABLE_SEO_CACHE), 'error');
		$failed = true;
	}

	if(function_exists('zen_deregister_admin_pages')) {
		if(zen_page_key_exists('configUltimateSEO')) zen_deregister_admin_pages('configUltimateSEO');
		if(zen_page_key_exists('UltimateSEO')) zen_deregister_admin_pages('UltimateSEO');
		if(zen_page_key_exists('configUltimateSEO') || zen_page_key_exists('UltimateSEO')) {
			$messageStack->add(SEO_UNINSTALL_ERROR_ADMIN_PAGES);
			$failed = true;
		}
	}

	return $failed;
}

/**
 * Updates the configuration option in the database using the supplied
 * configuration data. A new key to use for the option can be specified in the
 * data array (using 'configuration_key'). If not specified in the data array
 * the title and description for the option will be updated using the defined
 * language constants for the key.
 *
 * @param string $key the configuration key to update.
 * @param array $data the array of configuration settings.
 */
function ultimate_seo_upgrade_option($key, $data = array()) {
	global $db;

	// If a new key was sent, make sure we use the new key
	$new_key = (array_key_exists('configuration_key', $data) ? $data['configuration_key'] : $key);

	// With all old versions we need to upgrade some stuff
	$check = $db->Execute(
		'SELECT `configuration_id` FROM `' . TABLE_CONFIGURATION . '` ' .
		'WHERE `configuration_key`=\'' . $key . '\''
	);
	if(!$check->EOF) {
		$sql_data_array = array(
			'configuration_key' => $new_key,
			'configuration_title' => @constant($new_key . '_TITLE'),
			'configuration_description' => @constant($new_key . '_DESCRIPTION'),
			'last_modified' => 'now()'
		);

		zen_db_perform(TABLE_CONFIGURATION, array_merge($sql_data_array, $data), 'update', '`configuration_id`=\'' . $check->fields['configuration_id'] .'\'');
	}
}