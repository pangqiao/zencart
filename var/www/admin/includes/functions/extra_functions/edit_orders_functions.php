<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+

// Include various Zen Cart functions (with any necessary changes for admin)
if(!function_exists('zen_get_country_id')) {
	function zen_get_country_id($country_name) {
		global $db;
		$country_id_query = $db -> Execute("select * from " . TABLE_COUNTRIES . " where countries_name = '" . $country_name . "'");

		if (!$country_id_query->RecordCount()) {
			return 0;
		}
		else {
			return $country_id_query->fields['countries_id'];
		}
	}
}

if(!function_exists('zen_get_country_iso_code_2')) {
	function zen_get_country_iso_code_2($country_id) {
		global $db;
		$country_iso_query = $db -> Execute("select * from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");

		if (!zen_db_num_rows($country_iso_query)) {
			return 0;
		}
		else {
			$country_iso_row = zen_db_fetch_array($country_iso_query);
			return $country_iso_row['countries_iso_code_2'];
		}
	}
}

if(!function_exists('zen_get_zone_id')) {
	function zen_get_zone_id($country_id, $zone_name) {
		global $db;
		$zone_id_query = $db -> Execute("select * from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_name = '" . $zone_name . "'");

		if (!$zone_id_query->RecordCount()) {
			return 0;
		}
		else {
			return $zone_id_query->fields['zone_id'];
		}
	}
}

if(!function_exists('zen_get_country_list')) {
	function zen_get_country_list($name, $selected = '', $parameters = '') {
		$countriesAtTopOfList = array();
		$countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
		$countries = zen_get_countries();

		// Set some default entries at top of list:
		if (STORE_COUNTRY != SHOW_CREATE_ACCOUNT_DEFAULT_COUNTRY) $countriesAtTopOfList[] = SHOW_CREATE_ACCOUNT_DEFAULT_COUNTRY;
		$countriesAtTopOfList[] = STORE_COUNTRY;
		// IF YOU WANT TO ADD MORE DEFAULTS TO THE TOP OF THIS LIST, SIMPLY ENTER THEIR NUMBERS HERE.
		// Duplicate more lines as needed
		// Example: Canada is 108, so use 108 as shown:
		//$countriesAtTopOfList[] = 108;

		//process array of top-of-list entries:
		foreach ($countriesAtTopOfList as $key=>$val) {
			$countries_array[] = array('id' => $val, 'text' => zen_get_country_name($val));
		}
		// now add anything not in the defaults list:
		foreach($countries as $country) {
			$alreadyInList = FALSE;
			foreach($countriesAtTopOfList as $key=>$val) {
				if ($country['id'] == $val)
				{
					// If you don't want to exclude entries already at the top of the list, comment out this next line:
					$alreadyInList = TRUE;
					continue;
				}
			}
			if (!$alreadyInList) $countries_array[] = $country;
		}

		return zen_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
	}
}

if(!function_exists('zen_field_exists')) {
	function zen_field_exists($table,$field) {
		global $db;
		$describe_query = $db -> Execute("describe $table");
		while (!$describe_query -> EOF) {
			if ($d_row["Field"] == "$field") {
				 return true;
			}
			$describe_query -> MoveNext();
		}

		return false;
	}
}

if(!function_exists('zen_html_quotes')) {
	function zen_html_quotes($string) {
		if(function_exists('zen_db_output'))
			return zen_db_output($string);
		return htmlspecialchars($string, ENT_COMPAT, CHARSET, TRUE);
	}
}

if(!function_exists('zen_html_unquote')) {
	function zen_html_unquote($string) {
		return htmlspecialchars_decode($string, ENT_COMPAT);
	}
}

if(!function_exists('zen_get_tax_description')) {
	function zen_get_tax_description($class_id, $country_id = -1, $zone_id = -1) {
		global $db, $customer_zone_id, $customer_country_id;

		if ( ($country_id == -1) && ($zone_id == -1) ) {
			if (!$_SESSION['customer_id']) {
				$country_id = STORE_COUNTRY;
				$zone_id = STORE_ZONE;
			} else {
				$country_id = $customer_country_id;
				$zone_id = $customer_zone_id;
			}
		}

		$tax_query = "select tax_description
		from (" . TABLE_TAX_RATES . " tr
		left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id)
		left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) )
		where (za.zone_country_id is null or za.zone_country_id = 0
		or za.zone_country_id = '" . (int)$country_id . "')
		and (za.zone_id is null
		or za.zone_id = 0
		or za.zone_id = '" . (int)$zone_id . "')
		and tr.tax_class_id = '" . (int)$class_id . "'
		order by tr.tax_priority";

		$tax = $db->Execute($tax_query);

		if ($tax->RecordCount() > 0) {
			$tax_description = '';
			while (!$tax->EOF) {
				$tax_description .= $tax->fields['tax_description'] . ' + ';
				$tax->MoveNext();
			}
			$tax_description = substr($tax_description, 0, -3);

			return $tax_description;
		} else {
			return null;
		}
	}
}

if(!function_exists('zen_get_all_tax_descriptions')) {
	function zen_get_all_tax_descriptions($country_id = -1, $zone_id = -1) {
		global $db, $customer_country_id, $customer_zone_id;
		if ( ($country_id == -1) && ($zone_id == -1) ) {
			if (!$_SESSION['customer_id']) {
				$country_id = STORE_COUNTRY;
				$zone_id = STORE_ZONE;
			} else {
				$country_id = $customer_country_id;
				$zone_id = $customer_zone_id;
			}
		}

		$sql = "select tr.*
			from (" . TABLE_TAX_RATES . " tr
			left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id)
			left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) )
			where (za.zone_country_id is null
			or za.zone_country_id = 0
			or za.zone_country_id = '" . (int)$country_id . "')
			and (za.zone_id is null
			or za.zone_id = 0
			or za.zone_id = '" . (int)$zone_id . "')";
		$result = $db->Execute($sql);
		$taxDescriptions =array();
		while (!$result->EOF)
		{
			$taxDescriptions[] = $result->fields['tax_description'];
			$result->moveNext();
		}
		return $taxDescriptions;
	}
}

if(!function_exists('zen_get_tax_rate_from_desc')) {
	function zen_get_tax_rate_from_desc($tax_desc) {
		global $db;
		$tax_rate = 0.00;

		$tax_descriptions = explode(' + ', $tax_desc);
		foreach ($tax_descriptions as $tax_description) {
			$tax_query = "SELECT tax_rate
				FROM " . TABLE_TAX_RATES . "
				WHERE tax_description = :taxDescLookup";
			$tax_query = $db->bindVars($tax_query, ':taxDescLookup', $tax_description, 'string');

			$tax = $db->Execute($tax_query);

			$tax_rate += $tax->fields['tax_rate'];
		}

		return $tax_rate;
	}
}
if(!function_exists('zen_get_multiple_tax_rates')) {
	function zen_get_multiple_tax_rates($class_id, $country_id, $zone_id, $tax_description=array()) {
		global $db;

		if ( ($country_id == -1) && ($zone_id == -1) ) {
			if (isset($_SESSION['customer_id'])) {
				$country_id = $_SESSION['customer_country_id'];
				$zone_id = $_SESSION['customer_zone_id'];
			} else {
				$country_id = STORE_COUNTRY;
				$zone_id = STORE_ZONE;
			}
		}

		$tax_query = "select tax_description, tax_rate, tax_priority
			from (" . TABLE_TAX_RATES . " tr
			left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id)
			left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) )
			where (za.zone_country_id is null or za.zone_country_id = 0
			or za.zone_country_id = '" . (int)$country_id . "')
			and (za.zone_id is null
			or za.zone_id = 0
			or za.zone_id = '" . (int)$zone_id . "')
			and tr.tax_class_id = '" . (int)$class_id . "'
			order by tr.tax_priority";
		$tax = $db->Execute($tax_query);

		// calculate appropriate tax rate respecting priorities and compounding
		if ($tax->RecordCount() > 0) {
			$tax_aggregate_rate = 1;
			$tax_rate_factor = 1;
			$tax_prior_rate = 1;
			$tax_priority = 0;
			while (!$tax->EOF) {
				if ((int)$tax->fields['tax_priority'] > $tax_priority) {
					$tax_priority = $tax->fields['tax_priority'];
					$tax_prior_rate = $tax_aggregate_rate;
					$tax_rate_factor = 1 + ($tax->fields['tax_rate'] / 100);
					$tax_rate_factor *= $tax_aggregate_rate;
					$tax_aggregate_rate = 1;
				} else {
					$tax_rate_factor = $tax_prior_rate * ( 1 + ($tax->fields['tax_rate'] / 100));
				}
				$rates_array[$tax->fields['tax_description']] = 100 * ($tax_rate_factor - $tax_prior_rate);
				$tax_aggregate_rate += $tax_rate_factor - 1;
				$tax->MoveNext();
			}
		} else {
			// no tax at this level, set rate to 0 and description of unknown
			$rates_array[0] = TEXT_UNKNOWN_TAX_RATE;
		}
		return $rates_array;
	}
}

if(!function_exists('zen_get_tax_locations')) {
	function zen_get_tax_locations($store_country = -1, $store_zone = -1) {
		global $order;
		if(STORE_PRODUCT_TAX_BASIS != 'Store') {
			$_SESSION['customer_id'] = $order->customer['id'];

			if(STORE_PRODUCT_TAX_BASIS == 'Shipping') {
				$GLOBALS['customer_country_id'] = zen_get_country_id($GLOBALS['order']->delivery['country']);
				$GLOBALS['customer_zone_id'] = zen_get_zone_id($GLOBALS['customer_country_id'], $GLOBALS['order']->delivery['state']);
			}
			else if(STORE_PRODUCT_TAX_BASIS == 'Billing') {
				$GLOBALS['customer_country_id'] = zen_get_country_id($GLOBALS['order']->billing['country']);
				$GLOBALS['customer_zone_id'] = zen_get_zone_id($GLOBALS['customer_country_id'], $GLOBALS['order']->billing['state']);
			}
		}
		return array(
			'zone_id' => $GLOBALS['customer_zone_id'],
				'country_id' => $GLOBALS['customer_country_id']
		);
	}
}
if(!function_exists('is_product_valid')) {
	function is_product_valid($product_id, $coupon_id) {
		global $db;
		$coupons_query = "SELECT * FROM " . TABLE_COUPON_RESTRICT . "
	                      WHERE coupon_id = '" . (int)$coupon_id . "'
	                      ORDER BY coupon_restrict ASC";

		$coupons = $db->Execute($coupons_query);

		$product_query = "SELECT products_model FROM " . TABLE_PRODUCTS . "
	                      WHERE products_id = '" . (int)$product_id . "'";

		$product = $db->Execute($product_query);

		if (preg_match('/^GIFT/', $product->fields['products_model'])) {
			return false;
		}

		// modified to manage restrictions better - leave commented for now
		if ($coupons->RecordCount() == 0) return true;
		if ($coupons->RecordCount() == 1) {
			// If product is restricted(deny) and is same as tested prodcut deny
			if (($coupons->fields['product_id'] != 0) && $coupons->fields['product_id'] == (int)$product_id && $coupons->fields['coupon_restrict']=='Y') return false;
			// If product is not restricted(allow) and is not same as tested prodcut deny
			if (($coupons->fields['product_id'] != 0) && $coupons->fields['product_id'] != (int)$product_id && $coupons->fields['coupon_restrict']=='N') return false;
			// if category is restricted(deny) and product in category deny
			if (($coupons->fields['category_id'] !=0) && (zen_product_in_category($product_id, $coupons->fields['category_id'])) && ($coupons->fields['coupon_restrict']=='Y')) return false;
			// if category is not restricted(allow) and product not in category deny
			if (($coupons->fields['category_id'] !=0) && (!zen_product_in_category($product_id, $coupons->fields['category_id'])) && ($coupons->fields['coupon_restrict']=='N')) return false;
			return true;
		}
		$allow_for_category = validate_for_category($product_id, $coupon_id);
		$allow_for_product = validate_for_product($product_id, $coupon_id);
		//    echo '#'.$product_id . '#' . $allow_for_category;
		//    echo '#'.$product_id . '#' . $allow_for_product;
		if ($allow_for_category == 'none') {
			if ($allow_for_product === 'none') return true;
			if ($allow_for_product === true) return true;
			if ($allow_for_product === false) return false;
		}
		if ($allow_for_category === true) {
			if ($allow_for_product === 'none') return true;
			if ($allow_for_product === true) return true;
			if ($allow_for_product === false) return false;
		}
		if ($allow_for_category === false) {
			if ($allow_for_product === 'none') return false;
			if ($allow_for_product === true) return true;
			if ($allow_for_product === false) return false;
		}
		return false; //should never get here
	}
}

// Start Edit Orders configuration functions
function eo_debug_action_level_list($level) {
	global $template;

	$levels = array(
		array('id' => '0', 'text' => 'NONE'),
		array('id' => '1', 'text' => 'TRACE'),
		array('id' => '2', 'text' => 'TAXES'),
		array('id' => '3', 'text' => 'TOTALS'),
		array('id' => '4', 'text' => 'PRODUCTS')
	);

	// Generate the configuration pulldown
	return zen_draw_pull_down_menu('configuration_value', $levels, $level);
}

// Start Edit Orders functions
function eo_log($string) {
	global $eo_logfile;
	global $action;

	// Determine logfile name if not already calculated
	if(!isset($eo_logfile)) {
		$eo_logfile = (defined('DIR_FS_LOGS') ? DIR_FS_LOGS : DIR_FS_SQL_CACHE . '/logs') .
			'/edit_orders/DEBUG-' . (zen_not_null($action) ? $action . '-' : '') .
			time() . '-' . mt_rand(1000,999999) . '.log';
	}

	// Create any needed directories (disables if error occurs)
	if(!is_dir(dirname($eo_logfile)) && !mkdir(dirname($eo_logfile), 0777, true)) {
		$eo_logfile = null;
	}

	if($eo_logfile !== null) {
		error_log($string . PHP_EOL, 3, $eo_logfile);
	}
}

/**
 * Retrieves the country id, name, iso_code_2, and iso_code_3 from the database
 * for the requested country.
 *
 * @param mixed $country the id, name, or iso code for the country.
 * @return NULL|array the country if one is found, otherwise NULL
 */
function eo_get_country($country) {
	global $db;

	$query = 'SELECT `countries_id` AS `id`, `countries_name` AS `name`, ' .
			'`countries_iso_code_2` AS `iso_code_2`, `countries_iso_code_3` AS `iso_code_3` ' .
			'FROM `' . TABLE_COUNTRIES . '` ';

	// Handle null and an array being passed to this method
	if($country === null) return null;
	if(is_array($country)) {
		if(array_key_exists('id', $country)) $country = $country['id'];
		else if(array_key_exists('iso_code', $country)) $country = $country['iso_code'];
		else if(array_key_exists('iso_code_2', $country)) $country = $country['iso_code_2'];
		else if(array_key_exists('iso_code_3', $country)) $country = $country['iso_code_3'];
		else if(array_key_exists('name', $country)) $country = $country['name'];
		else if(count($country) == 1) $country = array_shift($country);
		else return null;
	}

	// Determine where clause by passed variable type
	if(is_numeric($country)) $query .= 'WHERE `countries_id`=\'' . (int)$country . '\'';
	else if(strlen($country) == 2) $query .= 'WHERE `countries_iso_code_2`= \'' . $db->prepare_input($country) . '\'';
	else if(strlen($country) == 3) $query .= 'WHERE `countries_iso_code_3`=\'' . $db->prepare_input($country) . '\'';
	else $query .= 'WHERE `countries_name`=\'' . $db->prepare_input($country) . '\'';

	$product = $db->Execute($query, false, true, 43200);

	if($product->EOF) return null;
	$retval = $product->fields;
	unset($query, $product);
	return $retval;
}
function eo_get_product_attributes_options($products_id, $readonly = false) {
	global $db;

	include_once(DIR_WS_CLASSES . 'attributes.php');
	$attributes = new attributes();
	$attributes = $attributes->get_attributes_options($products_id, $readonly);

	// Rearrange these by option id instead of attribute id
	$retval = array();
	foreach($attributes as $attr_id => $info) {
		if(!array_key_exists($info['id'], $retval)) {
			$retval[$info['id']] = array(
				'options' => array(),
				'name' => $info['name'],
				'type' => $info['type'],
				'length' => $info['length'],
				'size' => $info['size'],
				'rows' => $info['rows']
			);
		}
		$retval[$info['id']]['options'][$attr_id] = $info['value'];
	}
	unset($attributes);
	return $retval;
}

function eo_get_new_product($product_id, $product_qty = 1, $product_options = array(), $use_specials = true) {
	global $db;

	$product_id = (int)$product_id;
	$product_qty = (float)$product_qty;

	$retval = array(
		'id' => $product_id,
		'qty' => $product_qty,
	);

	$query = $db->Execute(
		'SELECT `p`.`products_id`, `p`.`master_categories_id`, `p`.`products_status`, ' .
			'`pd`.`products_name`, `p`.`products_model`, `p`.`products_image`, `p`.`products_price`, ' .
			'`p`.`products_weight`, `p`.`products_tax_class_id`, `p`.`manufacturers_id`, ' .
			'`p`.`products_quantity_order_min`, `p`.`products_quantity_order_units`, ' .
			'`p`.`products_quantity_order_max`, `p`.`product_is_free`, `p`.`products_virtual`, ' .
			'`p`.`products_discount_type`, `p`.`products_discount_type_from`, ' .
			'`p`.`products_priced_by_attribute`, `p`.`product_is_always_free_shipping` ' .
		'FROM `' . TABLE_PRODUCTS . '` AS `p`, `' . TABLE_PRODUCTS_DESCRIPTION . '` AS `pd` ' .
		'WHERE `p`.`products_id`=\'' . (int)$product_id . '\' ' .
			'AND `pd`.`products_id`=`p`.`products_id` ' .
			'AND `pd`.`language_id`=\'' . (int)$_SESSION['languages_id'] . '\''
	);

	if(!$query->EOF) {
		// Handle common fields
		$retval = array_merge($retval, array(
			'name' => $query->fields['products_name'],
			'model' => $query->fields['products_model'],
			'price' => $query->fields['products_price'],
			'products_discount_type' => $query->fields['products_discount_type'],
			'products_discount_type_from' => $query->fields['products_discount_type_from'],
			'products_priced_by_attribute' => $query->fields['products_priced_by_attribute'],
			'product_is_free' => $query->fields['product_is_free'],
			'products_virtual' => $query->fields['products_virtual'],
			'product_is_always_free_shipping' => $query->fields['product_is_always_free_shipping'],
			'tax' => number_format(zen_get_tax_rate_value($query->fields['products_tax_class_id']), 4),
			'tax_description' => zen_get_tax_description($query->fields['products_tax_class_id'])
		));

		// Handle pricing
		$special_price = zen_get_products_special_price($product_id);
		if($use_specials && $special_price && $retval['products_priced_by_attribute'] == 0) {
			$retval['price'] = $special_price;
		} else {
			$special_price = 0;
		}

		if(zen_get_products_price_is_free($product_id)) {
			// no charge
			$retval['price'] = 0;
		}
		// adjust price for discounts when priced by attribute
		if($retval['products_priced_by_attribute'] == '1' && zen_has_product_attributes($product_id, 'false')) {
			// reset for priced by attributes
			if($special_price) {
				$retval['price'] = $special_price;
			}
			else {
				$retval['price'] = $query->fields['products_price'];
				// START MARKUP
				if(isset($GLOBALS['priceMarkup'])) {
					$retval['price'] = $GLOBALS['priceMarkup']->calculatePrice(
						$product_id,
						$query->fields['manufacturers_id'],
						$query->fields['master_categories_id'],
						$retval['price']
					);
				}
				// END MARKUP
			}
		}
		else {
			// discount qty pricing
			if ($retval['products_discount_type'] != '0') {
				$retval['price'] = zen_get_products_discount_price_qty($product_id, $retval['qty']);
			}
			// START MARKUP
			if(isset($GLOBALS['priceMarkup'])) {
				$retval['price'] = $GLOBALS['priceMarkup']->calculatePrice(
					$product_id,
					$query->fields['manufacturers_id'],
					$query->fields['master_categories_id'],
					$retval['price']
				);
			}
			// END MARKUP
		}
		unset($special_price);

		$retval['onetime_charges'] = 0;
		$retval['final_price'] = $retval['price'];
	}

	// Handle attributes
	if(is_array($product_options) && count($product_options > 0))
	{
		$retval['attributes'] = array();

		include_once(DIR_WS_CLASSES . 'attributes.php');
		$attributes = new attributes();

		foreach($product_options as $option_id => $details) {
			$attr = array();
			switch($details['type']) {
				case PRODUCTS_OPTIONS_TYPE_TEXT:
				case PRODUCTS_OPTIONS_TYPE_FILE:
					$attr['option_id'] = $option_id;
					$attr['value'] = $details['value'];
					if($attr['value'] == '') continue 2;

					// There should only be one text per name.....
					$get_attr_id = $attributes->get_attributes_by_option($product_id, $option_id);
					if(count($get_attr_id) == 1) $details['value'] = $get_attr_id[0]['products_attributes_id'];
					unset($get_attr_id);
					break;
				case PRODUCTS_OPTIONS_TYPE_CHECKBOX:
					if(!array_key_exists('value', $details)) continue 2;
					$tmp_id = array_shift($details['value']);
					foreach($details['value'] as $attribute_id) {
						// We only get here if more than one checkbox per
						// option was selected.
						$tmp = $attributes->get_attribute_by_id($attribute_id, 'order');
						$retval['attributes'][] = $tmp;

						// Handle pricing
						$prices = eo_get_product_attribute_prices(
							$attribute_id, $tmp['value'], $product_qty
						);
						unset($tmp);
						if(!$query->EOF) {
							$retval['onetime_charges'] += $prices['onetime_charges'];
							$retval['final_price'] += $prices['price'];
						}
					}
					$details['value'] = $tmp_id;
					$attr = $attributes->get_attribute_by_id($details['value'], 'order');
					unset($attribute_id); unset($attribute_value); unset($tmp_id);
					break;
				default:
					$attr = $attributes->get_attribute_by_id($details['value'], 'order');
			}
			$retval['attributes'][] = $attr;

			if(!$query->EOF) {
				// Handle pricing
				$prices = eo_get_product_attribute_prices(
					$details['value'], $attr['value'], $product_qty
				);
				$retval['onetime_charges'] += $prices['onetime_charges'];
				$retval['final_price'] += $prices['price'];
			}
		}
		unset($query, $attr, $prices, $option_id, $details);
	}

	return $retval;
}

function eo_get_product_attribute_prices($attr_id, $attr_value = '', $qty = 1) {
	global $db;

	$retval = array(
		'onetime_charges' => 0,
		'price' => 0
	);

	$attribute_price = $db->Execute(
		'SELECT * ' .
		'FROM `' . TABLE_PRODUCTS_ATTRIBUTES . '` ' .
		'WHERE `products_attributes_id`=\'' . (int)$attr_id . '\''
	);

	$attr_id = (int)$attr_id;
	$qty = (int)$qty;
	$product_id = (int)$attribute_price->fields['products_id'];

	// Only check when attributes is not free or the product is not free
	if($attribute_price->fields['product_attribute_is_free'] != '1' || !zen_get_products_price_is_free($product_id)) {

		// Handle based upon discount enabled
		if($attribute_price->fields['attributes_discounted'] == '1') {
			// Calculate proper discount for attributes

			$added_charge = zen_get_discount_calc($product_id, $attr_id, $attribute_price->fields['options_values_price'], $qty);
		}
		else {
			$added_charge = $attribute_price->fields['options_values_price'];
		}

		// Handle negative price prefix
		// Other price prefixes ("+" and "") should add so no special processing
		if($attribute_price->fields['price_prefix'] == '-') {
			$added_charge = -1 * $added_charge;
		}

		$retval['price'] += $added_charge;

		//////////////////////////////////////////////////
		// calculate additional charges

		// products_options_value_text
		if (zen_get_attributes_type($attr_id) == PRODUCTS_OPTIONS_TYPE_TEXT) {
			$text_words = zen_get_word_count_price($attr_value, $attribute_price->fields['attributes_price_words_free'], $attribute_price->fields['attributes_price_words']);
			$text_letters = zen_get_letters_count_price($attr_value, $attribute_price->fields['attributes_price_letters_free'], $attribute_price->fields['attributes_price_letters']);
			$retval['price'] += $text_letters;
			$retval['price'] += $text_words;
		}

		// attributes_price_factor
		$added_charge = 0;
		if ($attribute_price->fields['attributes_price_factor'] > 0) {
			$chk_price = zen_get_products_base_price($products_id);
			$chk_special = zen_get_products_special_price($products_id, false);
			$added_charge = zen_get_attributes_price_factor($chk_price, $chk_special, $attribute_price->fields['attributes_price_factor'], $attribute_price->fields['attributes_price_factor_offset']);
			$retval['price'] += $added_charge;
		}

		// attributes_qty_prices
		$added_charge = 0;
		if ($attribute_price->fields['attributes_qty_prices'] != '') {
			$chk_price = zen_get_products_base_price($products_id);
			$chk_special = zen_get_products_special_price($products_id, false);
			$added_charge = zen_get_attributes_qty_prices_onetime($attribute_price->fields['attributes_qty_prices'], $qty);
			$retval['price'] += $added_charge;
		}

		// attributes_price_onetime
		if ($attribute_price->fields['attributes_price_onetime'] > 0) {
			$retval['onetime_charges'] = (float) $attribute_price->fields['attributes_price_onetime'];
		}
		// attributes_price_factor_onetime
		$added_charge = 0;
		if ($attribute_price->fields['attributes_price_factor_onetime'] > 0) {
			$chk_price = zen_get_products_base_price($products_id);
			$chk_special = zen_get_products_special_price($products_id, false);
			$added_charge = zen_get_attributes_price_factor($chk_price, $chk_special, $attribute_price->fields['attributes_price_factor_onetime'], $attribute_price->fields['attributes_price_factor_onetime_offset']);
			$retval['onetime_charges'] += $added_charge;
		}
		// attributes_qty_prices_onetime
		$added_charge = 0;
		if ($attribute_price->fields['attributes_qty_prices_onetime'] != '') {
			$chk_price = zen_get_products_base_price($products_id);
			$chk_special = zen_get_products_special_price($products_id, false);
			$added_charge = zen_get_attributes_qty_prices_onetime($attribute_price->fields['attributes_qty_prices_onetime'], $qty);
			$retval['onetime_charges'] += $added_charge;
		}
		////////////////////////////////////////////////
	}

	return $retval;
}

function eo_add_product_to_order($order_id, $product) {
	global $db, $order;

	// Stock Update - Joao Correia
	if (STOCK_LIMITED == 'true') {
		if (DOWNLOAD_ENABLED == 'true') {
			$stock_query_raw = "select p.products_quantity, pad.products_attributes_filename, p.product_is_always_free_shipping
			from " . TABLE_PRODUCTS . " p
			left join " . TABLE_PRODUCTS_ATTRIBUTES . " pa
			on p.products_id=pa.products_id
			left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
			on pa.products_attributes_id=pad.products_attributes_id
			WHERE p.products_id = '" . zen_get_prid($product['id']) . "'";

			// Will work with only one option for downloadable products
			// otherwise, we have to build the query dynamically with a loop
			$products_attributes = $product['attributes'];
			if (is_array($products_attributes)) {
				$stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
			}
			$stock_values = $db->Execute($stock_query_raw);
		} else {
			$stock_values = $db->Execute("select * from " . TABLE_PRODUCTS . " where products_id = '" . zen_get_prid($product['id']) . "'");
		}

		if ($stock_values->RecordCount() > 0) {
			// do not decrement quantities if products_attributes_filename exists
			if ((DOWNLOAD_ENABLED != 'true') || $stock_values->fields['product_is_always_free_shipping'] == 2 || (!$stock_values->fields['products_attributes_filename']) ) {
				$stock_left = $stock_values->fields['products_quantity'] - $product['qty'];
				$product['stock_reduce'] = $product['qty'];
			} else {
				$stock_left = $stock_values->fields['products_quantity'];
			}

			$db->Execute("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . zen_get_prid($product['id']) . "'");
			if ($stock_left <= 0) {
				// only set status to off when not displaying sold out
				if (SHOW_PRODUCTS_SOLD_OUT == '0') {
					$db->Execute("update " . TABLE_PRODUCTS . " set products_status = 0 where products_id = '" . zen_get_prid($product['id']) . "'");
				}
			}

			// for low stock email
			if ( $stock_left <= STOCK_REORDER_LEVEL ) {
				// WebMakers.com Added: add to low stock email
				$order->email_low_stock .=  'ID# ' . zen_get_prid($product['id']) . "\t\t" . $product['model'] . "\t\t" . $product['name'] . "\t\t" . ' Qty Left: ' . $stock_left . "\n";
			}
		}
	}

	// Update products_ordered (for bestsellers list)
	$db->Execute("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%f', $product['qty']) . " where products_id = '" . zen_get_prid($product['id']) . "'");

	$sql_data_array = array(
		'orders_id' => (int)$order_id,
		'products_id' => zen_get_prid($product['id']),
		'products_model' => $product['model'],
		'products_name' => $product['name'],
		'products_price' => $product['price'],
		'final_price' => $product['final_price'],
		'onetime_charges' => $product['onetime_charges'],
		'products_tax' => $product['tax'],
		'products_quantity' => $product['qty'],
		'products_priced_by_attribute' => $product['products_priced_by_attribute'],
		'product_is_free' => $product['product_is_free'],
		'products_discount_type' => $product['products_discount_type'],
		'products_discount_type_from' => $product['products_discount_type_from'],
		'products_prid' => $product['id']
	);
	zen_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
	$order_products_id = $db->Insert_ID();

	//------ bof: insert customer-chosen options to order--------
	$attributes_exist = '0';
	if (isset($product['attributes'])) {
		$attributes_exist = '1';
		for ($j=0, $n2=sizeof($product['attributes']); $j<$n2; $j++) {
			if (DOWNLOAD_ENABLED == 'true') {
				$attributes_query = "select popt.products_options_name, poval.products_options_values_name,
				pa.options_values_price, pa.price_prefix,
				pa.product_attribute_is_free, pa.products_attributes_weight, pa.products_attributes_weight_prefix,
				pa.attributes_discounted, pa.attributes_price_base_included, pa.attributes_price_onetime,
				pa.attributes_price_factor, pa.attributes_price_factor_offset,
				pa.attributes_price_factor_onetime, pa.attributes_price_factor_onetime_offset,
				pa.attributes_qty_prices, pa.attributes_qty_prices_onetime,
				pa.attributes_price_words, pa.attributes_price_words_free,
				pa.attributes_price_letters, pa.attributes_price_letters_free,
				pad.products_attributes_maxdays, pad.products_attributes_maxcount, pad.products_attributes_filename
				from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " .
				TABLE_PRODUCTS_ATTRIBUTES . " pa
				left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
				on pa.products_attributes_id=pad.products_attributes_id
				where pa.products_id = '" . zen_db_input($product['id']) . "'
				and pa.options_id = '" . $product['attributes'][$j]['option_id'] . "'
				and pa.options_id = popt.products_options_id
				and pa.options_values_id = '" . $product['attributes'][$j]['value_id'] . "'
				and pa.options_values_id = poval.products_options_values_id
				and popt.language_id = '" . $_SESSION['languages_id'] . "'
				and poval.language_id = '" . $_SESSION['languages_id'] . "'";

				$attributes_values = $db->Execute($attributes_query);
			} else {
				$attributes_values = $db->Execute("select popt.products_options_name, poval.products_options_values_name,
						pa.options_values_price, pa.price_prefix,
						pa.product_attribute_is_free, pa.products_attributes_weight, pa.products_attributes_weight_prefix,
						pa.attributes_discounted, pa.attributes_price_base_included, pa.attributes_price_onetime,
						pa.attributes_price_factor, pa.attributes_price_factor_offset,
						pa.attributes_price_factor_onetime, pa.attributes_price_factor_onetime_offset,
						pa.attributes_qty_prices, pa.attributes_qty_prices_onetime,
						pa.attributes_price_words, pa.attributes_price_words_free,
						pa.attributes_price_letters, pa.attributes_price_letters_free
						from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
						where pa.products_id = '" . $product['id'] . "' and pa.options_id = '" . (int)$product['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . (int)$product['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $_SESSION['languages_id'] . "' and poval.language_id = '" . $_SESSION['languages_id'] . "'");
			}

			//clr 030714 update insert query.  changing to use values form $order->products for products_options_values.
			$sql_data_array = array(
				'orders_id' => (int)$order_id,
				'orders_products_id' => (int)$order_products_id,
				'products_options' => $attributes_values->fields['products_options_name'],
				'products_options_values' => $product['attributes'][$j]['value'],
				'options_values_price' => $attributes_values->fields['options_values_price'],
				'price_prefix' => $attributes_values->fields['price_prefix'],
				'product_attribute_is_free' => $attributes_values->fields['product_attribute_is_free'],
				'products_attributes_weight' => $attributes_values->fields['products_attributes_weight'],
				'products_attributes_weight_prefix' => $attributes_values->fields['products_attributes_weight_prefix'],
				'attributes_discounted' => $attributes_values->fields['attributes_discounted'],
				'attributes_price_base_included' => $attributes_values->fields['attributes_price_base_included'],
				'attributes_price_onetime' => $attributes_values->fields['attributes_price_onetime'],
				'attributes_price_factor' => $attributes_values->fields['attributes_price_factor'],
				'attributes_price_factor_offset' => $attributes_values->fields['attributes_price_factor_offset'],
				'attributes_price_factor_onetime' => $attributes_values->fields['attributes_price_factor_onetime'],
				'attributes_price_factor_onetime_offset' => $attributes_values->fields['attributes_price_factor_onetime_offset'],
				'attributes_qty_prices' => $attributes_values->fields['attributes_qty_prices'],
				'attributes_qty_prices_onetime' => $attributes_values->fields['attributes_qty_prices_onetime'],
				'attributes_price_words' => $attributes_values->fields['attributes_price_words'],
				'attributes_price_words_free' => $attributes_values->fields['attributes_price_words_free'],
				'attributes_price_letters' => $attributes_values->fields['attributes_price_letters'],
				'attributes_price_letters_free' => $attributes_values->fields['attributes_price_letters_free'],
				'products_options_id' => (int)$product['attributes'][$j]['option_id'],
				'products_options_values_id' => (int)$product['attributes'][$j]['value_id'],
				'products_prid' => $product['id']
			);
			zen_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

			if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values->fields['products_attributes_filename']) && zen_not_null($attributes_values->fields['products_attributes_filename'])) {
				$sql_data_array = array(
					'orders_id' => (int)$order_id,
					'orders_products_id' => (int)$order_products_id,
					'orders_products_filename' => $attributes_values->fields['products_attributes_filename'],
					'download_maxdays' => $attributes_values->fields['products_attributes_maxdays'],
					'download_count' => $attributes_values->fields['products_attributes_maxcount'],
					'products_prid' => $product['id']
				);

				zen_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
			}
		}
	}

	$order->products[] = $product;

	return $product;
}

function eo_update_order_subtotal($order_id, $product, $add = true) {
	global $currencies, $db, $order;

	// Retrieve running subtotal
	if(!array_key_exists('subtotal', $order->info)) {
		$query = $db->Execute(
			'SELECT `value` FROM `' . TABLE_ORDERS_TOTAL . '` ' .
			'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
			'AND `class`=\'ot_subtotal\''
		);
		if(!$query->EOF) {
			$order->info['subtotal'] = $query->fields['value'];
		}
	}

	// Determine the product price
	if(DISPLAY_PRICE_WITH_TAX == 'true') {
		$shown_price = (zen_round($product['final_price'], $currencies->get_decimal_places($_SESSION['currency'])) + zen_calculate_tax($product['final_price'], $product['tax'])) * $product['qty'];
		$shown_price += zen_round($product['onetime_charges'], $currencies->get_decimal_places($_SESSION['currency'])) + zen_calculate_tax($product['onetime_charges'], $product['tax']);
	}
	else {
		$shown_price = $product['final_price'] * $product['qty'];
		$shown_price += $product['onetime_charges'];
	}
	// Not standard Zen Cart - but clears up some math issues later
	$shown_price = zen_round($shown_price, $currencies->get_decimal_places($_SESSION['currency']));

	// Update the order information
	if($add) {
		$order->info['subtotal'] += $shown_price;
		$order->info['tax'] += eo_get_product_taxes($product, $shown_price, $add);
	}
	else {
		$order->info['subtotal'] -= $shown_price;
		$order->info['tax'] -= eo_get_product_taxes($product, $shown_price, $add);
	}
	unset($shown_price);

	// Update the final total to include tax if not already tax-inc
	if (DISPLAY_PRICE_WITH_TAX == 'true') {
		$order->info['total'] = $order->info['subtotal'] + $order->info['shipping_cost'];
	} else {
		$order->info['total'] = $order->info['subtotal'] + $order->info['tax'] + $order->info['shipping_cost'];
	}

	// Update the order totals (if present)
	foreach($order->totals as $index => $total) {
		switch($total['class']) {
			case 'ot_subtotal':
				$order->totals[$index]['value'] = $order->info['subtotal'];
				$order->totals[$index]['text'] = $currencies->format($order->totals[$index]['value'], true, $order->info['currency'], $order->info['currency_value']);
				break;
			case 'ot_tax':
				$order->totals[$index]['value'] = $order->info['tax'];
				$order->totals[$index]['text'] = $currencies->format($order->totals[$index]['value'], true, $order->info['currency'], $order->info['currency_value']);
				break;
			case 'ot_total':
				$order->totals[$index]['value'] = $order->info['total'];
				$order->totals[$index]['text'] = $currencies->format($order->totals[$index]['value'], true, $order->info['currency'], $order->info['currency_value']);
				break;
			default:
		}
	}
	unset($index, $total);
}

function eo_get_product_taxes($product, $shown_price = -1, $add = true) {
	global $db, $currencies, $order;

	if(DISPLAY_PRICE_WITH_TAX == 'true') {
		$shown_price = (zen_round($product['final_price'], $currencies->get_decimal_places($_SESSION['currency'])) + zen_calculate_tax($product['final_price'], $product['tax'])) * $product['qty'];
		$shown_price += zen_round($product['onetime_charges'], $currencies->get_decimal_places($_SESSION['currency'])) + zen_calculate_tax($product['onetime_charges'], $product['tax']);
	}
	else {
		$shown_price = $product['final_price'] * $product['qty'];
		$shown_price += $product['onetime_charges'];
	}
	// Not standard Zen Cart - but clears up some math issues later
	$shown_price = zen_round($shown_price, $currencies->get_decimal_places($_SESSION['currency']));

	if(array_key_exists('tax_description', $product)) $products_tax_description = $product['tax_description'];
	else {
		$query = $db->Execute(
			'SELECT `products_tax_class_id` ' .
			'FROM `' . TABLE_PRODUCTS . '` WHERE `products_id`=\'' . (int)$product['id'] . '\' '
		);
		if(!$query->EOF) {
			$products_tax_description = zen_get_tax_description($query->fields['products_tax_class_id']);
		}
		else if(array_key_exists('tax', $product)) {
			$products_tax_description = TEXT_UNKNOWN_TAX_RATE .
			' (' . zen_display_tax_value($product['tax']) . '%)';
		}
	}

	$totalTaxAdd = 0;
	if(zen_not_null($products_tax_description)) {
		$taxAdd = 0;
		// Done this way to ensure we calculate
		if(DISPLAY_PRICE_WITH_TAX == 'true') {
			$taxAdd = $shown_price - ($shown_price / (($product['tax'] < 10) ? "1.0" . str_replace('.', '', $product['tax']) : "1." . str_replace('.', '', $product['tax'])));
		}
		else {
			$taxAdd = zen_calculate_tax($shown_price, $product['tax']);
		}
		if(isset($order->info['tax_groups'][$products_tax_description]))
		{
			if($add) {
				$order->info['tax_groups'][$products_tax_description] += $taxAdd;
			}
			else {
				$order->info['tax_groups'][$products_tax_description] -= $taxAdd;
			}
		}
		else if($add) {
			$order->info['tax_groups'][$products_tax_description] = $taxAdd;
		}
		$totalTaxAdd += $taxAdd;
		unset($taxAdd);
	}
	return $totalTaxAdd;
}

function eo_remove_product_from_order($order_id, $orders_products_id) {
	global $db, $order;

	// First grab the order <==> product mappings
	$orders_products_id_mapping = eo_get_orders_products_id_mappings((int)$order_id);

	// Handle product stock
	if (STOCK_LIMITED == 'true') {
		$query = $db->Execute(
			'SELECT `products_id`, `products_quantity` ' .
			'FROM `' . TABLE_ORDERS_PRODUCTS . '` ' .
			'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
				'AND `orders_products_id`=\'' . (int)$orders_products_id . '\''
		);

		while (!$query->EOF) {
			if (DOWNLOAD_ENABLED == 'true') {
				$check = $db->Execute(
					'SELECT `p`.`products_quantity`, `pad`.`products_attributes_filename`, `p`.`product_is_always_free_shipping` ' .
					'FROM `' . TABLE_PRODUCTS . '` AS `p` ' .
					'LEFT JOIN `' . TABLE_PRODUCTS_ATTRIBUTES . '` AS `pa` ON `p`.`products_id`=`pa`.`products_id` ' .
					'LEFT JOIN `' . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . '` AS `pad` ON `pa`.`products_attributes_id`=`pad`.`products_attributes_id` ' .
					'WHERE `p`.`products_id` = \'' . (int)$query->fields['products_id'] . '\''
				);
			}
			else {
				$check = $db->Execute(
					'SELECT `p`.`products_quantity` FROM `' . TABLE_PRODUCTS . '` ' .
				 	'WHERE `p`.`products_id` = \'' . (int)$query->fields['products_id'] . '\''
				);
			}
			if (!$check->EOF && (DOWNLOAD_ENABLED != 'true' || $check->fields['product_is_always_free_shipping'] == 2 || !$check->fields['products_attributes_filename'] )) {
				$sql_data_array = array(
					'products_quantity' => $check->fields['products_quantity'] + $query->fields['products_quantity'],
					'products_ordered' => $check->fields['products_ordered'] - $query->fields['products_quantity']
				);
				if($sql_data_array['products_ordered'] < 0) $sql_data_array['products_ordered'] = 0;
				if($sql_data_array['products_quantity'] > 0) {

					// Only set status to on when not displaying sold out
					if (SHOW_PRODUCTS_SOLD_OUT == '0') {
						$sql_data_array['products_status'] = 1;
					}
				}

				zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . (int)$query->fields['products_id'] . '\'');
			}

			$query->MoveNext();
		}
		unset($check, $query, $sql_data_array);
	}

	// Remove the product from the order in the database
	$remove_query = 'DELETE FROM `%1$s` ' .
		'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
		'AND `orders_products_id`=\'' . (int)$orders_products_id . '\'';

	$db->Execute(sprintf($remove_query, TABLE_ORDERS_PRODUCTS));
	$db->Execute(sprintf($remove_query, TABLE_ORDERS_PRODUCTS_ATTRIBUTES));
	$db->Execute(sprintf($remove_query, TABLE_ORDERS_PRODUCTS_DOWNLOAD));
	unset($remove_query);

	// Hande the internal products array
	for($i=0; $i<sizeof($order->products); $i++) {
		if($orders_products_id == $orders_products_id_mapping[$i]) {
			// Remove from the product from the array
			unset($order->products[$i]);
			// rekey the array (so for loops work)
			$order->products = array_values($order->products);
			break;
		}
	}
}

function eo_get_order_total_by_order($order_id, $class = null) {
	global $db;

	// Retreive the raw value
	$ot = $db->Execute(
		'SELECT `title`, `text`, `value`, `class`, `sort_order` ' .
		'FROM `' . TABLE_ORDERS_TOTAL . '` ' .
		'WHERE `orders_id`=\'' . (int)$order_id . '\'' .
		($class !== null ? ' AND `class`=\'' . $class . '\' ORDER BY `sort_order`' : '')
	);

	$retval = array();
	while(!$ot->EOF) {
		$retval[$ot->fields['class']] = array(
			'title' => $ot->fields['title'],
			'text' => $ot->fields['text'],
			'value' => $ot->fields['value'],
			'sort_order' => $ot->fields['sort_order'],
		);
		$ot->moveNext();
	}
	return $retval;
}

function eo_get_orders_products_id_mappings($order_id) {
	global $db;
	$orders_products_ids = $db->Execute(
		'SELECT `orders_products_id` ' .
		'FROM `' . TABLE_ORDERS_PRODUCTS . '` ' .
		'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
		'ORDER BY `orders_products_id`'
	);

	$retval = array();
	while(!$orders_products_ids->EOF) {
		$retval[] = $orders_products_ids->fields['orders_products_id'];
		$orders_products_ids->moveNext();
	}
	return $retval;
}

function eo_get_orders_products_attributes_id_mappings($order_id, $order_product_id) {
	global $db;
	$orders_products_ids = $db->Execute(
		'SELECT `orders_products_attributes_id` ' .
		'FROM `' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . '` ' .
		'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
		'AND `orders_products_id`=\'' . (int)$order_product_id . '\''
	);

	$retval = array();
	while(!$orders_products_ids->EOF) {
		$retval[] = $orders_products_ids->fields['orders_products_attributes_id'];
		$orders_products_ids->moveNext();
	}
	return $retval;
}

function eo_get_orders_products_options_id_mappings($order_id, $order_product_id) {
	global $db;
	$orders_products_ids = $db->Execute(
		'SELECT `products_options_id`, `orders_products_attributes_id` ' .
		'FROM `' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . '` ' .
		'WHERE `orders_id`=\'' . (int)$order_id . '\' ' .
		'AND `orders_products_id`=\'' . (int)$order_product_id . '\''
	);

	$retval = array();
	while(!$orders_products_ids->EOF) {
		if(!array_key_exists($orders_products_ids->fields['products_options_id'], $retval)) {
			$retval[$orders_products_ids->fields['products_options_id']] = array();
		}
		$retval[$orders_products_ids->fields['products_options_id']][] = $orders_products_ids->fields['orders_products_attributes_id'];
		$orders_products_ids->moveNext();
	}
	return $retval;
}

function eo_is_selected_product_attribute_id($orders_products_attributes_id, $attribute_id) {
	global $db;

	$attributes = new attributes();
	$attributes = $attributes->get_attribute_by_id($attribute_id);

	$query = $db->Execute(
		'SELECT COUNT(`orders_products_attributes_id`) AS `count` ' .
		'FROM `' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . '` AS `opa` ' .
		'LEFT JOIN `' . TABLE_ORDERS_PRODUCTS . '` AS `op` ON `op`.`orders_products_id`=`opa`.`orders_products_id` ' .
		'WHERE `orders_products_attributes_id`=\'' . (int)$orders_products_attributes_id . '\' ' .
		'AND `op`.`products_id`=\'' . $attributes['products_id'] . '\' ' .
		'AND `opa`.`products_options_id`=\'' . $attributes['options_id'] . '\' ' .
		'AND `opa`.`products_options_values_id`=\'' . $attributes['options_values_id'] . '\''
	);
	unset($attributes);

	return (!$query->EOF && $query->fields['count'] == 1);
}

function eo_get_selected_product_attribute_value_by_id($orders_products_attributes_id, $attribute_id) {
	global $db;

	$attributes = new attributes();
	$attributes = $attributes->get_attribute_by_id($attribute_id);

	$query = $db->Execute(
		'SELECT `products_options_values` ' .
		'FROM `' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . '` AS `opa` ' .
		'LEFT JOIN `' . TABLE_ORDERS_PRODUCTS . '` AS `op` ON `op`.`orders_products_id`=`opa`.`orders_products_id` ' .
		'WHERE `orders_products_attributes_id`=\'' . (int)$orders_products_attributes_id . '\' ' .
		'AND `op`.`products_id`=\'' . $attributes['products_id'] . '\' ' .
		'AND `opa`.`products_options_id`=\'' . $attributes['options_id'] . '\' ' .
		'AND `opa`.`products_options_values_id`=\'' . $attributes['options_values_id'] . '\''
	);
	unset($attributes);

	if($query->EOF) return null;
	return $query->fields['products_options_values'];
}

function eo_update_database_order_totals($oID) {
	global $db, $order;

	// Load required modules for order totals if enabled
	if(defined('MODULE_ORDER_TOTAL_INSTALLED') && zen_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {

		// Load order totals.
		require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
		$GLOBALS['order_total_modules'] = new order_total();

		// Load the shopping cart class into the session
		eo_shopping_cart();

		// This fixes an issue caused by ot_shipping assuming shipping tax amount
		// has not already been added to the shipping cost and order tax.
		if(array_key_exists('shipping_tax', $order->info)) {
			$order->info['tax'] -= $order->info['shipping_tax'];
			if (DISPLAY_PRICE_WITH_TAX == 'true') $order->info['shipping_cost'] -= $order->info['shipping_tax'];
		}

		// Reset the final total (include tax if not already tax-inc)
		// This code causes the order totals to be correctly calculated.
		if (DISPLAY_PRICE_WITH_TAX == 'true') {
			$order->info['total'] = $order->info['subtotal'] + $order->info['shipping_cost'];
		} else {
			$order->info['total'] = $order->info['subtotal'] + $order->info['tax'] + $order->info['shipping_cost'];
		}

		// Process the order totals
		$order_totals = $GLOBALS['order_total_modules']->process();

		// Update the order totals in the database
		for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
			eo_update_database_order_total($oID, $order_totals[$i]);
		}
		unset($order_totals);
	}
}

function eo_update_database_order_total($oID, $order_total) {
	global $db;
	$updated = false;

	$sql_data_array = array(
		'title' => $order_total['title'],
		'text' => $order_total['text'],
		'value' => (is_numeric($order_total['value'])) ? $order_total['value'] : '0',
		'sort_order' => $order_total['sort_order']
	);
	// Update the Order Totals in the Database
	$found = $db->Execute(
		'SELECT `orders_id` FROM `' . TABLE_ORDERS_TOTAL . '` ' .
		'WHERE `class`=\'' . $order_total['code'] .
		'\' AND `orders_id`=\'' . (int)$oID .'\''
	);
	if(!$found->EOF) {
		if(zen_not_null($order_total['title']) && $order_total['title'] != ':') {
			zen_db_perform(
				TABLE_ORDERS_TOTAL, $sql_data_array, 'update',
				'class=\'' . $order_total['code'] . '\' AND orders_id=\'' . (int)$oID .'\''
			);
		}
		else {
			$db->Execute(
				'DELETE FROM `' . TABLE_ORDERS_TOTAL . '` ' .
				'WHERE `class`=\'' . $order_total['code'] . '\' AND `orders_id`=\'' . (int)$oID . '\''
			);
		}
		$updated = true;
	}
	else if(zen_not_null($order_total['title']) && $order_total['title'] != ':') {
		$sql_data_array['orders_id'] = (int)$oID;
		$sql_data_array['class'] = $order_total['code'];

		zen_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		$updated = true;
	}
	else return $updated;
//DEBUG echo '<div>Updated Order Total: ' . $order_total['code'] . '</div><pre>'; var_dump($sql_data_array); echo '</pre>';

	// Update order if relevant
	switch($order_total['code']) {
		case 'ot_tax':
			zen_db_perform(
				TABLE_ORDERS, array('order_tax' => $sql_data_array['value']), 'update',
				'orders_id = \'' . (int)$oID . '\''
			);
			break;
		case 'ot_total':
			zen_db_perform(
				TABLE_ORDERS, array('order_total' => $sql_data_array['value']), 'update',
				'orders_id = \'' . (int)$oID . '\''
			);
			break;
		case 'ot_shipping':
			if(substr($order_total['title'], -1) == ':') $order_total['title'] = substr($order_total['title'], 0, -1);
			$sql_data_array = array(
				'shipping_method' => $order_total['title']
			);
			if(array_key_exists('shipping_module', $order_total))
				$sql_data_array['shipping_module_code'] = $order_total['shipping_module'];

			zen_db_perform(
				TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \'' . (int)$oID . '\''
			);
			break;
		default:
	}
	unset($sql_data_array);

	return $updated;
}

function eo_get_available_order_totals_class_values($oID) {
	global $db;

	$retval = array();

	// Remove order totals already present in the order
	$module_list = explode(';', (str_replace('.php', '', MODULE_ORDER_TOTAL_INSTALLED)));
	$order_totals = eo_get_order_total_by_order($oID);
	if($order_totals !== null) {
		foreach($order_totals as $class => $total) {
			if($class == 'ot_local_sales_taxes') continue;
			$keys = array_keys($module_list, $class);
			foreach($keys as $key) {
				unset($module_list[$key]);
			}
		}
	}

	// Load the order total classes
	require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
	$order_totals = new order_total();

	foreach($module_list as $class) {
		if($class == 'ot_group_pricing' || $class == 'ot_cod_fee' || $class == 'ot_tax' || $class == 'ot_loworderfee') continue;
		$retval[] = array(
			'id' => $class,
			'text' => $GLOBALS[$class]->title,
			'sort_order' => $GLOBALS[$class]->sort_order
		);
	}
	unset($module_list, $order_totals, $class, $total, $keys, $key);

	return $retval;
}

function eo_get_available_shipping_modules() {
	$retval = array();
	if(defined('MODULE_SHIPPING_INSTALLED') && zen_not_null(MODULE_SHIPPING_INSTALLED)) {

		// Load the shopping cart class into the session
		eo_shopping_cart();

		// Load the shipping class into the globals
		require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shipping.php');
		$shipping_modules = new shipping();

		for($i=0, $n=count($shipping_modules->modules); $i<$n; $i++) {
			$class = substr($shipping_modules->modules[$i], 0, strrpos($shipping_modules->modules[$i], '.'));
			if(isset($GLOBALS[$class])) {
				$retval[] = array(
					'id' => $GLOBALS[$class]->code,
					'text' => $GLOBALS[$class]->title
				);
				if(defined('EO_SHIPPING_DROPDOWN_STRIP_TAGS') && EO_SHIPPING_DROPDOWN_STRIP_TAGS === 'true') {
					$retval['text'] = @strip_tags($retval['text']);
				}
			}
		}
		unset($shipping_modules, $class, $i, $n);
	}

	return $retval;
}

function eo_get_order_by_id($oID) {
	global $db, $order;

	// Retrieve the order
	$order = new order($oID);

	// Add some required customer information for tax calculation
	// The next method has been modified to add required info to the
	// session and global variables.
	zen_get_tax_locations();

	// Cleanup tax_groups in the order (broken code in order.php)
	// Shipping module will automatically add tax if needed.
	$order->info['tax_groups'] = array();
	foreach($order->products as $product) {
		eo_get_product_taxes($product);
	}

	// Correctly add the running subtotal (broken code in order.php)
	if(!array_key_exists('subtotal', $order->info)) {
		$query = $db->Execute(
			'SELECT `value` FROM `' . TABLE_ORDERS_TOTAL . '` ' .
			'WHERE `orders_id`=\'' . (int)$oID . '\' ' .
			'AND `class`=\'ot_subtotal\''
		);
		if(!$query->EOF) {
			$order->info['subtotal'] = $query->fields['value'];
		}
	}

	// Convert country portion of addresses to same format used in catalog side
	$country = null;
	if(array_key_exists('country', $order->customer)) {
		$country = eo_get_country($order->customer['country']);
		if($country !== null) {
			$order->customer['country'] = $country;
			$order->customer['zone_id'] = zen_get_zone_id($order->customer['country']['id'], $order->customer['state']);
		}
	}
	if(array_key_exists('country', $order->delivery)) {
		$country = eo_get_country($order->delivery['country']);
		if($country !== null) {
			$order->delivery['country'] = $country;
			$order->delivery['zone_id'] = zen_get_zone_id($order->delivery['country']['id'], $order->delivery['state']);
		}
	}
	if(array_key_exists('country', $order->billing)) {
		$country = eo_get_country($order->billing['country']);
		if($country !== null) {
			$order->billing['country'] = $country;
			$order->billing['zone_id'] = zen_get_zone_id($order->billing['country']['id'], $order->billing['state']);
		}
	}
	unset($country);

	// Handle shipping costs (module will automatically handle tax)
	if(!array_key_exists('shipping_cost', $order->info)) {
		$query = $db->Execute(
			'SELECT `value` FROM `' . TABLE_ORDERS_TOTAL . '` ' .
			'WHERE `orders_id` = \'' . (int)$oID . '\' ' .
			'AND `class` = \'ot_shipping\''
		);
		if(!$query->EOF) {
			$order->info['shipping_cost'] = $query->fields['value'];

			$_SESSION['shipping'] = array(
				'title' => $order->info['shipping_method'],
				'id' => $order->info['shipping_module_code'] . '_',
				'cost' => $order->info['shipping_cost']
			);

			// Load the shopping cart class into the session
			eo_shopping_cart();

			// Load the shipping class into the globals
			require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shipping.php');
			$shipping_modules = new shipping($_SESSION['shipping']);
		}
	}

	return $order;
}

function eo_shopping_cart() {
	if(!isset($_SESSION['cart'])) {
		if(defined('EO_MOCK_SHOPPING_CART') && EO_MOCK_SHOPPING_CART === 'true') {
			$_SESSION['cart'] = new mockCart();
		}
		else {
			require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'shopping_cart.php');
			$_SESSION['cart'] = new shoppingCart();
		}
	}
}

function eo_checks_and_warnings() {
	global $db, $messageStack;

	$result = $db->Execute('SELECT `project_version_major`, `project_version_minor` FROM `' . TABLE_PROJECT_VERSION . '` WHERE `project_version_key`=\'Zen-Cart Database\'');
	$version = $result->fields['project_version_major'] . '.' . $result->fields['project_version_minor'];
	unset($result);

	// Core checks first. If reload needed after set reload to true
	$reload = false;
	if(!defined('PRODUCTS_OPTIONS_TYPE_SELECT')) {
		if(version_compare($version, '1.5', '>=')) {
			$sql_data_array = array(
				'configuration_title' => 'Product option type Select',
				'configuration_key' => 'PRODUCTS_OPTIONS_TYPE_SELECT',
				'configuration_value' => '0',
				'configuration_description' => 'The number representing the Select type of product option.',
				'configuration_group_id' => '0',
				'sort_order' => null,
				'last_modified' => 'now()',
				'date_added' => 'now()',
				'use_function' => null,
				'set_function' => null
			);
			zen_db_perform(TABLE_CONFIGURATION, $sql_data_array);
			$reload = true;
		}
	}

	if(!defined('UPLOAD_PREFIX')) {
		if(version_compare($version, '1.5', '>=')) {
			$sql_data_array = array(
				'configuration_title' => 'Upload prefix',
				'configuration_key' => 'UPLOAD_PREFIX',
				'configuration_value' => 'upload_',
				'configuration_description' => 'Prefix used to differentiate between upload options and other options',
				'configuration_group_id' => '0',
				'sort_order' => null,
				'last_modified' => 'now()',
				'date_added' => 'now()',
				'use_function' => null,
				'set_function' => null
			);
			zen_db_perform(TABLE_CONFIGURATION, $sql_data_array);
			$reload = true;
		}
	}

	if(!defined('TEXT_PREFIX')) {
		if(version_compare($version, '1.5', '>=')) {
			$sql_data_array = array(
				'configuration_title' => 'Text prefix',
				'configuration_key' => 'TEXT_PREFIX',
				'configuration_value' => 'txt_',
				'configuration_description' => 'Prefix used to differentiate between text option values and other options',
				'configuration_group_id' => '0',
				'sort_order' => null,
				'last_modified' => 'now()',
				'date_added' => 'now()',
				'use_function' => null,
				'set_function' => null
			);
			zen_db_perform(TABLE_CONFIGURATION, $sql_data_array);
			$reload = true;
		}
	}
	if($reload) zen_redirect(zen_href_link(FILENAME_EDIT_ORDERS, zen_get_all_get_params(array('action')) . 'action=edit'));
	unset($reload);

	// Warn user about subtotal calculations
	if(DISPLAY_PRICE_WITH_TAX_ADMIN !== DISPLAY_PRICE_WITH_TAX) {
		$messageStack->add(WARNING_DISPLAY_PRICE_WITH_TAX, 'warning');
	}

	// Warn user about potential issues with subtotal / total calculations
	$module_list = explode(';', (str_replace('.php', '', MODULE_ORDER_TOTAL_INSTALLED)));
	if(!in_array('ot_subtotal', $module_list)) {
		$messageStack->add(WARNING_ORDER_TOTAL_SUBTOTAL, 'warning');
	}
	if(!in_array('ot_total', $module_list)) {
		$messageStack->add(WARNING_ORDER_TOTAL_TOTAL, 'warning');
	}
	unset($module_list);

	// Check for the installation of "Absolute's Product Attribute Grid"
	if(!defined('PRODUCTS_OPTIONS_TYPE_ATTRIBUTE_GRID')) {
		if(defined('CONFIG_ATTRIBUTE_OPTION_GRID_INSTALLED')) {
			define('PRODUCTS_OPTIONS_TYPE_ATTRIBUTE_GRID', '23997');
			$messageStack->add(WARNING_ATTRIBUTE_OPTION_GRID, 'warning');
		}
		else {
			define('PRODUCTS_OPTIONS_TYPE_ATTRIBUTE_GRID', '-1');
		}
	}
}