<?php
/**
 * new_order
 *
 * Provides a mechanisim for creating new orders inside the admin interface
 *
 * @copyright Portions Copyright 2012 Andrew Ballanger
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
require('includes/application_top.php');

function no_get_ip_address() {
	if(isset($_SERVER)) {
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		}
	}

	return $ip;
}

// Init currency if not already initialized
require_once(DIR_WS_INCLUDES . 'init_includes/init_currencies.php');

$action = (isset($_GET['action']) ? $_GET['action'] : '');

// Load any information submitted
$parameters = array(
	'customers_id' => '0',
	'customers_name' => '',
	'customers_company' => 'null',
	'customers_street_address' => '',
	'customers_suburb' => 'null',
	'customers_postcode' => '',
	'customers_city' => '',
	'customers_state' => 'null',
	'customers_country' => '',
	'customers_address_format_id' => '0',
	'customers_email_address' => '',
	'customers_telephone' => '',
	'delivery_name' => '',
	'delivery_company' => 'null',
	'delivery_street_address' => '',
	'delivery_suburb' => 'null',
	'delivery_postcode' => '',
	'delivery_city' => '',
	'delivery_state' => 'null',
	'delivery_country' => '',
	'delivery_address_format_id' => '0',
	'billing_name' => '',
	'billing_company' => 'null',
	'billing_street_address' => '',
	'billing_suburb' => 'null',
	'billing_postcode' => '',
	'billing_city' => '',
	'billing_state' => 'null',
	'billing_country' => '',
	'billing_address_format_id' => '0',
	'payment_method' => '',
	'payment_module_code' => '',
	'shipping_method' => '',
	'shipping_module_code' => '',
	'coupon_code' => '',
	'date_purchased' => 'now()',
	'currency' => $_SESSION['currency'],
	'currency_value' => $currencies->currencies[$_SESSION['currency']]['value'],
	'orders_status' => DEFAULT_ORDERS_STATUS_ID,
	'order_total' => 0,
	'order_tax' => 0,
	'ip_address' => no_get_ip_address(),
);
$cInfo = new objectInfo($parameters);

$is_post = false;
if(isset($_GET['cID']) && empty($_POST)) {
	// Grab the required information for this customer
	$query = $db->Execute(
		'SELECT c.customers_id, c.customers_firstname, c.customers_lastname, a.entry_firstname, a.entry_lastname, ' .
			'a.entry_company AS customers_company, a.entry_street_address AS customers_street_address, ' .
			'a.entry_suburb AS customers_suburb, a.entry_postcode AS customers_postcode, ' .
			'a.entry_city AS customers_city, a.entry_state AS customers_state, ' .
			'a.entry_country_id, a.entry_zone_id, c.customers_telephone, c.customers_email_address ' .
		'FROM `' . TABLE_CUSTOMERS . '` AS c LEFT JOIN `' . TABLE_ADDRESS_BOOK . '` AS a ' .
			'ON c.customers_default_address_id = a.address_book_id ' .
		'WHERE a.customers_id = c.customers_id ' .
			'AND c.customers_id = \'' . (int)$_GET['cID'] . '\''
	);

	// Handle the customer name
	if(zen_not_null($query->fields['entry_firstname']) || zen_not_null($query->fields['entry_lastname'])) {
		$query->fields['customers_name'] = $query->fields['entry_firstname'] . ' ' . $query->fields['entry_lastname'];
	}
	else {
		$query->fields['customers_name'] = $query->fields['customers_firstname'] . ' ' . $query->fields['customers_lastname'];
	}
	unset($query->fields['entry_firstname']);
	unset($query->fields['entry_lastname']);
	unset($query->fields['customers_firstname']);
	unset($query->fields['customers_lastname']);

	// Handle the customer country and address
	$query->fields['customers_country'] = zen_get_country_name($query->fields['entry_country_id']);
	$query->fields['customers_address_format_id'] = zen_get_address_format_id($query->fields['entry_country_id']);
	if(zen_not_null($query->fields['entry_zone_id']) && $query->fields['entry_zone_id'] != 0) {
		$query->fields['customers_state'] = zen_get_zone_name($query->fields['entry_country_id'], $query->fields['entry_zone_id'], 0);
	}
	unset($query->fields['entry_zone_id']);
	unset($query->fields['entry_country_id']);


	$cInfo->objectInfo($query->fields);
	unset($query);
}

if(zen_not_null($action)) {
	// What was the selected action?
	switch ($action) {
		case 'new':

			// Check for customer who has not logged into zen cart
			$query = $db->Execute(
				'SELECT customers_info_date_of_last_logon ' .
				'FROM `' . TABLE_CUSTOMERS_INFO . '` ' .
				'WHERE customers_info_id = \'' . (int)$cInfo->customers_id . '\''
			);
			$has_logged_in = !$query->EOF && zen_date_short($query->fields['customers_info_date_of_last_logon']) !== false;
			unset($query);

			// TODO: Add a step to select addresses instead
			$sql_data_array = get_object_vars($cInfo);
			$sql_data_array = array_merge($sql_data_array, array(
				'delivery_name' => $cInfo->customers_name,
				'delivery_company' => $cInfo->customers_company,
				'delivery_street_address' => $cInfo->customers_street_address,
				'delivery_suburb' => $cInfo->customers_suburb,
				'delivery_postcode' => $cInfo->customers_postcode,
				'delivery_city' => $cInfo->customers_city,
				'delivery_state' => $cInfo->customers_state,
				'delivery_country' => $cInfo->customers_country,
				'delivery_address_format_id' => $cInfo->customers_address_format_id,
				'billing_name' => $cInfo->customers_name,
				'billing_company' => $cInfo->customers_company,
				'billing_street_address' => $cInfo->customers_street_address,
				'billing_suburb' => $cInfo->customers_suburb,
				'billing_postcode' => $cInfo->customers_postcode,
				'billing_city' => $cInfo->customers_city,
				'billing_state' => $cInfo->customers_state,
				'billing_country' => $cInfo->customers_country,
				'billing_address_format_id' => $cInfo->customers_address_format_id,
				'currency' => $cInfo->currency,
				'currency_value' => $cInfo->currency_value,
			));

			if(!$has_logged_in) {
				// Assume user was added by an administrative user. In this case
				// if a second address book exists, it will be the billing address.
				$query = $db->Execute(
					'SELECT c.customers_firstname, c.customers_lastname, a.entry_firstname, a.entry_lastname, ' .
						'a.entry_company AS billing_company, a.entry_street_address AS billing_street_address, ' .
						'a.entry_suburb AS billing_suburb, a.entry_postcode AS billing_postcode, ' .
						'a.entry_city AS billing_city, a.entry_state AS billing_state, ' .
						'a.entry_country_id, a.entry_zone_id ' .
					'FROM `' . TABLE_CUSTOMERS . '` AS c ' .
					'LEFT JOIN `' . TABLE_ADDRESS_BOOK . '` AS a ON c.customers_default_address_id != a.address_book_id ' .
					'WHERE a.customers_id = c.customers_id AND c.customers_id = \'' . (int)$cInfo->customers_id . '\''
				);
				if(!$query->EOF) {
					if(zen_not_null($query->fields['entry_zone_id']) && $query->fields['entry_zone_id'] != 0) {
						$query->fields['billing_state'] = zen_get_zone_name($query->fields['entry_country_id'], $query->fields['entry_zone_id'], 0);
						unset($query->fields['entry_zone_id']);
					}

					// Handle the customer's name
					if(zen_not_null($query->fields['entry_firstname']) || zen_not_null($query->fields['entry_lastname'])) {
						$query->fields['customers_name'] = $query->fields['entry_firstname'] . ' ' . $query->fields['entry_lastname'];
					}
					else {
						$query->fields['customers_name'] = $query->fields['customers_firstname'] . ' ' . $query->fields['customers_lastname'];
					}

					$sql_data_array = array_merge($sql_data_array, array(
						'billing_name' => $query->fields['customers_name'],
						'billing_company' => $query->fields['billing_company'],
						'billing_street_address' => $query->fields['billing_street_address'],
						'billing_suburb' => $query->fields['billing_suburb'],
						'billing_postcode' => $query->fields['billing_postcode'],
						'billing_city' => $query->fields['billing_city'],
						'billing_state' => $query->fields['billing_state'],
						'billing_country' => zen_get_country_name($query->fields['entry_country_id']),
						'billing_address_format_id' => zen_get_address_format_id($query->fields['entry_country_id'])
					));
				}
				unset($query);
			}

			// Create the new order
			zen_db_perform(TABLE_ORDERS, $sql_data_array);
			$cInfo->orders_id = $db->Insert_ID();

			// Use the normal order class instead of the admin one
			require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order.php');
			$GLOBALS['order'] = new order($cInfo->orders_id);
			$GLOBALS['order']->info['tax_groups'] = array();

			// Load required modules for order totals if enabled
			if(defined('MODULE_ORDER_TOTAL_INSTALLED') && zen_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
				// Edit Orders 4.0
				if(version_compare(EO_VERSION, '4.0', '>=') && version_compare(EO_VERSION, '4.1', '<')) {
					$supported_order_total_modules = 'ot_subtotal;ot_total';

					require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
					$GLOBALS['order_total_modules'] = new order_total();
					$module_list = explode(';', $supported_order_total_modules);

					foreach($module_list as $module) {
						$module_file = DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/' . $module . '.php';
						if(@file_exists($module_file)) {
							include_once($module_file);
							$GLOBALS[$module] = new $module;
							$GLOBALS['order_total_modules']->modules[] = $module . '.php';
						}
					}
				}
				// Edit Orders 4.1+
				else if(version_compare(EO_VERSION, '4.1', '>=')) {
					require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
					$GLOBALS['order_total_modules'] = new order_total();

					require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shopping_cart.php');
					$_SESSION['cart'] = new shoppingCart();
				}
				else {
					// Could not find a supported version of Edit Orders
					break;
				}

				// Create the order totals.
				$order_totals = $GLOBALS['order_total_modules']->process();
				for($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
					// Skip shipping modules. No shipping method has been added at this point.
					if($order_totals[$i]['code'] == 'ot_shipping') continue;
					$sql_data_array = array(
						'orders_id' => $cInfo->orders_id,
						'title' => $order_totals[$i]['title'],
						'text' => $order_totals[$i]['text'],
						'value' => (is_numeric($order_totals[$i]['value'])) ? $order_totals[$i]['value'] : '0',
						'class' => $order_totals[$i]['code'],
						'sort_order' => $order_totals[$i]['sort_order']
					);
					zen_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
				}
			}

			// Prepare and add the new order status history
			$admname = '{' . preg_replace('/[^\d\w]/', '*', zen_get_admin_name()) . '[' . (int)$_SESSION['admin_id'] . ']}';
			$sql_data_array = array(
				'orders_id' => (int)$cInfo->orders_id,
				'orders_status_id' => $cInfo->orders_status,
				'date_added' => 'now()',
				'customer_notified' => '-1', // HIDDEN FROM CUSTOMER
				'comments' => sprintf(STATUS_COMMENT, $cInfo->customers_name, $admname)
			);
			zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

			$messageStack->add(ENTRY_NEW_ORDER_SUCCESS, 'success');

			if(version_compare(EO_VERSION, '4.1.1', '>=')) {
				zen_redirect(zen_href_link(FILENAME_EDIT_ORDERS, 'oID=' . $cInfo->orders_id));
			}
			else {
				zen_redirect(zen_href_link(FILENAME_ORDER_EDIT, 'oID=' . $cInfo->orders_id));
			}
			default:
				break;
	}
}
// Should never get here. If we did something is broken.
$messageStack->add(ENTRY_NEW_ORDER_ERROR);
zen_redirect(zen_href_link(FILENAME_CUSTOMERS, 'cID=' . $cInfo->customers_id));
require(DIR_WS_INCLUDES . 'application_bottom.php');