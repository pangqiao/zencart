<?php
class fedexwebservices {
 var $code, $title, $description, $icon, $sort_order, $enabled, $tax_class, $fedex_key, $fedex_pwd, $fedex_act_num, $fedex_meter_num, $country, $total_weight;

//Class Constructor
  function fedexwebservices() {
    global $order, $customer_id, $db;

    @define('MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY', '6W02EnQC0n9nO5NH');
    @define('MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD', 'tmZlwVIuLUHGNtasKOHQYkKKd');
    @define('MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE', 0); 
    $this->code             = "fedexwebservices";
    $this->title            = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TEXT_TITLE;
    if (extension_loaded('soap')) {
    $this->description      = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TEXT_DESCRIPTION;
    } else {
     $this->description      = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TEXT_DESCRIPTION_SOAP;
     }
    $this->sort_order       = MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER;
    //$this->icon = DIR_WS_IMAGES . 'fedex-images/fedex.gif';
    $this->icon = ''; 
    
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING == 'true' || zen_get_shipping_enabled($this->code)) {
    	if (extension_loaded('soap')) {
      $this->enabled = ((MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS == 'true') ? true : false);
      }
    }
    
    
    
    $this->tax_class        = MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS;
    $this->fedex_key        = MODULE_SHIPPING_FEDEX_WEB_SERVICES_KEY;
    $this->fedex_pwd        = MODULE_SHIPPING_FEDEX_WEB_SERVICES_PWD;
    $this->fedex_act_num    = MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM;
    $this->fedex_meter_num  = MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM;
    $this->total_weight = 0;
    if (defined("SHIPPING_ORIGIN_COUNTRY")) {
      if ((int)SHIPPING_ORIGIN_COUNTRY > 0) {
        $countries_array = zen_get_countries(SHIPPING_ORIGIN_COUNTRY, true);
        $this->country = $countries_array['countries_iso_code_2'];
      } else {
        $this->country = SHIPPING_ORIGIN_COUNTRY;
      }
    } else {
      $this->country = STORE_ORIGIN_COUNTRY;
    }
    if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
      while (!$check->EOF) {
        if ($check->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check->fields['zone_id'] == $order->delivery['zone_id']) {
          $check_flag = true;
          break;
        }
        $check->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
    // BEGIN SHIPPING BOXES MANAGER EDIT
    $this->box_destination = '';
    if ($order->delivery['country']['id'] == STORE_COUNTRY) {
      $this->box_destination == 'domestic';
    } else {
      $this->box_destination == 'international';
    }
    // END        
  }

  //Class Methods

  function build_request($client, $allow_0_weight_shipping = true) {
    /* FedEx integration starts */
    global $db, $shipping_weight, $shipping_num_boxes, $cart, $order, $all_products_ship_free;

    // shipping boxes manager
    if (MODULE_SHIPPING_BOXES_MANAGER_STATUS == 'true') {
      global $packed_boxes;    
    }
    
    $this->types = array();
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY == 'true') {
      $this->types['INTERNATIONAL_PRIORITY'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE);
      $this->types['EUROPE_FIRST_INTERNATIONAL_PRIORITY'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY == 'true') {
      $this->types['INTERNATIONAL_ECONOMY'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE);
    }  
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT == 'true') {
      $this->types['STANDARD_OVERNIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT == 'true') {
      $this->types['FIRST_OVERNIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT == 'true') {
      $this->types['PRIORITY_OVERNIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY == 'true') {
      $this->types['FEDEX_2_DAY'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
    }
    // because FEDEX_GROUND also is returned for Canadian Addresses, we need to check if the country matches the store country and whether international ground is enabled
    if ((MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND == 'true' && $order->delivery['country']['id'] == STORE_COUNTRY) || (MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND == 'true' && ($order->delivery['country']['id'] != STORE_COUNTRY) && MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND == 'true')) {
      $this->types['FEDEX_GROUND'] = array('icon' => '', 'handling_fee' => ($order->delivery['country']['id'] == STORE_COUNTRY ? MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE : MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_HANDLING_FEE));
      $this->types['GROUND_HOME_DELIVERY'] = array('icon' => '', 'handling_fee' => ($order->delivery['country']['id'] == STORE_COUNTRY ? MODULE_SHIPPING_FEDEX_WEB_SERVICES_HOME_DELIVERY_HANDLING_FEE : MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_HANDLING_FEE));
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND == 'true') {
      $this->types['INTERNATIONAL_GROUND'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER == 'true') {
      $this->types['FEDEX_EXPRESS_SAVER'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
    }
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT == 'true') {
      $this->types['FEDEX_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
      $this->types['FEDEX_NATIONAL_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
      $this->types['FEDEX_1_DAY_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
      $this->types['FEDEX_2_DAY_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
      $this->types['FEDEX_3_DAY_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE);
      $this->types['INTERNATIONAL_ECONOMY_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE);
      $this->types['INTERNATIONAL_PRIORITY_FREIGHT'] = array('icon' => '', 'handling_fee' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE);
    }                      
                         
    // customer details      
    $street_address = $order->delivery['street_address'];
    $street_address2 = $order->delivery['suburb'];
    $city = $order->delivery['city'];
    $state = zen_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], '');
    if ($state == "QC") $state = "PQ";
    $postcode = str_replace(array(' ', '-'), '', $order->delivery['postcode']);
    $country_id = $order->delivery['country']['iso_code_2'];
      
    $totals = $_SESSION['cart']->show_total();
    $this->_setInsuranceValue($totals);
    
    $request['WebAuthenticationDetail'] = array('UserCredential' =>
                                          array('Key' => $this->fedex_key, 'Password' => $this->fedex_pwd)); // Replace 'XXX' and 'YYY' with FedEx provided credentials 
    $request['ClientDetail'] = array('AccountNumber' => $this->fedex_act_num, 'MeterNumber' => $this->fedex_meter_num);// Replace 'XXX' with your account and meter number
    $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v10 using PHP ***');
    $request['Version'] = array('ServiceId' => 'crs', 'Major' => '10', 'Intermediate' => '0', 'Minor' => '0');
    $request['ReturnTransitAndCommit'] = true;
    $request['RequestedShipment']['DropoffType'] = $this->_setDropOff(); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
    $request['RequestedShipment']['ShipTimestamp'] = date('c');
    //if (zen_not_null($method) && in_array($method, $this->types)) {
      //$request['RequestedShipment']['ServiceType'] = $method; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
    //}
    $request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
    $request['RequestedShipment']['TotalInsuredValue']=array('Amount'=> $this->insurance, 'Currency' => $_SESSION['currency']);
    $request['WebAuthenticationDetail'] = array('UserCredential' => array('Key' => $this->fedex_key, 'Password' => $this->fedex_pwd));                     
    $request['ClientDetail'] = array('AccountNumber' => $this->fedex_act_num, 'MeterNumber' => $this->fedex_meter_num);
    //print_r($request['WebAuthenticationDetail']);
    //print_r($request['ClientDetail']);
    //exit; 
    // Address Validation
    $residential_address = true;
    $address_validation = false;
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_VALIDATION == 'true') {
      $path_to_address_validation_wsdl = DIR_WS_MODULES . 'shipping/fedexwebservices/wsdl/AddressValidationService_v2.wsdl';
      $av_client = new SoapClient($path_to_address_validation_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
      $av_request['WebAuthenticationDetail'] = array('UserCredential' =>
                                            array('Key' => $this->fedex_key, 'Password' => $this->fedex_pwd));
      $av_request['ClientDetail'] = array('AccountNumber' => $this->fedex_act_num, 'MeterNumber' => $this->fedex_meter_num);
      $av_request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Address Validation Request v2 using PHP ***');
      $av_request['Version'] = array('ServiceId' => 'aval', 'Major' => '2', 'Intermediate' => '0', 'Minor' => '0');
      $av_request['RequestTimestamp'] = date('c');
      $av_request['Options'] = array('CheckResidentialStatus' => 1,
      															 'VerifyAddress' => 1,	
                                     'MaximumNumberOfMatches' => 10,
                                     'StreetAccuracy' => 'MEDIUM',
                                     'DirectionalAccuracy' => 'MEDIUM',
                                     'CompanyNameAccuracy' => 'MEDIUM',
                                     'ConvertToUpperCase' => 1,
                                     'RecognizeAlternateCityNames' => 1,
                                     'ReturnParsedElements' => 1);
      $av_request['AddressesToValidate'] = array(
        0 => array(
          'AddressId' => 'Customer Address',                                                                                                      
          'Address' => array(
            'StreetLines' => array(utf8_encode($street_address), utf8_encode($street_address2)),
            'PostalCode' => $postcode,
            'City' => $city,
            'StateOrProvinceCode' => $state,
            'CompanyName' => $order->delivery['company'],
            'CountryCode' => $country_id
          )
        )
      );
      try {
        $av_response = $av_client->addressValidation($av_request);
        /*
        //echo '<!--';
        echo '<pre>';
        print_r($av_response); 
        echo '</pre>';
        //echo '-->';
        die();
        */
        if ($av_response->HighestSeverity == 'SUCCESS') {
          $address_validation = true;
          if ($av_response->AddressResults->ProposedAddressDetails->ResidentialStatus == 'BUSINESS') {
            $residential_address = false;
          } // already set to true so no need for else statement
        }
      } catch (Exception $e) {
      }
    }
    if ($address_validation == false) {
      if ($order->delivery['company'] != '') {
        $residential_address = false;
      } else {
        $residential_address = true;
      }
    }                   
    $request['RequestedShipment']['Shipper'] = array('Address' => array(
                                                     'StreetLines' => array(MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1, MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2), // Origin details
                                                     'City' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY,
                                                     'StateOrProvinceCode' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE,
                                                     'PostalCode' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL,
                                                     'CountryCode' => $this->country));          
    $request['RequestedShipment']['Recipient'] = array('Address' => array (
                                                       'StreetLines' => array(utf8_encode($street_address), utf8_encode($street_address2)), // customer street address
                                                       'City' => utf8_encode($city), //customer city
                                                       //'StateOrProvinceCode' => $state, //customer state
                                                       'PostalCode' => $postcode, //customer postcode
                                                       'CountryCode' => $country_id,
                                                       'Residential' => $residential_address)); //customer county code
    if (in_array($country_id, array('US', 'CA'))) {
      $request['RequestedShipment']['Recipient']['StateOrProvinceCode'] = $state;
    }
    //print_r($request['RequestedShipment']['Recipient'])  ;
    //exit;                     
    $request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                                    'Payor' => array('AccountNumber' => $this->fedex_act_num, // Replace 'XXX' with payor's account number
                                                                    'CountryCode' => $this->country));
    $request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
    $request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
    $request['RequestedShipment']['RequestedPackageLineItems'] = array();
    
    $dimensions_failed = false;
    
    //Update weight to 0 for products with free shipping, if this feature is enabled.
    $free_weight = 0; //for use later to alter packed boxes
    $all_products_ship_free = false; //for use later to set shipping cost to 0, when this is enabled
    if($allow_0_weight_shipping && (MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'all methods' || MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'Ground/Home only')) {
      $products = $_SESSION['cart']->get_products();
      $all_products_ship_free = true; //default
      foreach ($products as $product) {
        $dimensions_query = "SELECT product_is_always_free_shipping FROM " . TABLE_PRODUCTS . " 
                               WHERE products_id = " . (int)$product['id'] . " 
                               LIMIT 1;";
//print " + " . $product['weight'] . " id " . $product['id'];
         $dimensions = $db->Execute($dimensions_query);
         if($dimensions->fields['product_is_always_free_shipping'] == 1) {
             $free_weight += $product['weight'] * $product['quantity'];//give this product 0 weight so it ships free.
         }
         else {
           $all_products_ship_free = false;
         }
      }
    }

    $this->fedex_shipping_num_boxes = $shipping_num_boxes;
    $this->fedex_shipping_weight = $shipping_weight - $free_weight;

    // shipping boxes manager
    if (MODULE_SHIPPING_BOXES_MANAGER_STATUS == 'true' && is_array($packed_boxes) && sizeof($packed_boxes) > 0) {
      //$shipping_num_boxes = sizeof($packed_boxes);
      //$shipping_weight = round(($this->total_weight / $shipping_num_boxes), 2); // use our number of packages rather than Zen Cart's calculation, package weight will still have to be an average since we don't know which products are in the box.
      $boxed_value = sprintf("%01.2f", $this->insurance / $this->fedex_shipping_num_boxes);
      $packages = array();
      foreach ($packed_boxes as $packed_box) {
        $packed_box['weight'] = $packed_box['weight'] - ($free_weight / count($packed_boxes));
        if ($packed_box['weight'] <= 0) $packed_box['weight'] = 0.1;

        $package = array(
          'Weight' => array(
            'Value' => $packed_box['weight'], // this is an averaged value
            'Units' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT
          ),
          'InsuredValue' => array(
            'Currency' => $_SESSION['currency'],
            'Amount' => $boxed_value          
          ),
          'GroupPackageCount' => 1
        );
        if (isset($packed_box['length']) && isset($packed_box['width']) && isset($packed_box['height'])) {
          $package['Dimensions'] = array(
            'Length' => ($packed_box['length'] >= 1 ? $packed_box['length'] : 1),
            'Width' => ($packed_box['width'] >= 1 ? $packed_box['width'] : 1),
            'Height' => ($packed_box['height'] >= 1 ? $packed_box['height'] : 1),
            'Units' => (MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT == 'LB' ? 'IN' : 'CM') 
          );
        }
        $packages[] = $package;
      }

      $request['RequestedShipment']['RequestedPackageLineItems'] = $packages;
    } else {
      // check for ready to ship field
      if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_READY_TO_SHIP == 'true') {      
        $products = $_SESSION['cart']->get_products();
        $packages = array('default' => 0);
        $new_shipping_num_boxes = 0;
        foreach ($products as $product) {
          $dimensions_query = "SELECT products_length, products_width, products_height, products_ready_to_ship, products_dim_type, product_is_always_free_shipping FROM " . TABLE_PRODUCTS . " 
                               WHERE products_id = " . (int)$product['id'] . " 
                               AND products_length > 0 
                               AND products_width > 0
                               AND products_height > 0 
                               LIMIT 1;";

          $dimensions = $db->Execute($dimensions_query);

         if(MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'all methods' && $dimensions->fields['product_is_always_free_shipping'] == 1) {
           $products_weight = 0;
         }
         else{
           $products_weight = $product['weight'];
         }

          if ($dimensions->RecordCount() > 0 && $dimensions->fields['products_ready_to_ship'] == 1) {
            for ($i = 1; $i <= $product['quantity']; $i++) {
              $packages[] = array('weight' => $products_weight, 'length' => $dimensions->fields['products_length'], 'width' => $dimensions->fields['products_width'], 'height' => $dimensions->fields['products_height'], 'units' => strtoupper($dimensions->fields['products_dim_type']));
            }    
          } else {
            $packages['default'] += $products_weight * $product['quantity']; 
          }
        }
        if (count($packages) > 1) {
          $za_tare_array = preg_split("/[:,]/" , SHIPPING_BOX_WEIGHT);
          $zc_tare_percent= $za_tare_array[0];
          $zc_tare_weight= $za_tare_array[1];

          $za_large_array = preg_split("/[:,]/" , SHIPPING_BOX_PADDING);
          $zc_large_percent= $za_large_array[0];
          $zc_large_weight= $za_large_array[1];
        }
        foreach ($packages as $id => $values) {
          if ($id === 'default') {
            // divide the weight by the max amount to be shipped (can be done inside loop as this occurance should only ever happen once
            // note $values is not an array
            if ($values == 0) continue;
            $this->fedex_shipping_num_boxes = ceil((float)$values / (float)SHIPPING_MAX_WEIGHT);
            if ($this->fedex_shipping_num_boxes < 1) $this->fedex_shipping_num_boxes = 1;
            $this->fedex_shipping_weight = round((float)$values / $this->fedex_shipping_num_boxes, 2); // 2 decimal places max
            $boxed_value = sprintf("%01.2f", $this->insurance / $this->fedex_shipping_num_boxes);
            for ($i=0; $i<$this->fedex_shipping_num_boxes; $i++) {
              $new_shipping_num_boxes++;
              if (SHIPPING_MAX_WEIGHT <= $this->fedex_shipping_weight) {
                $this->fedex_shipping_weight = $this->fedex_shipping_weight + ($this->fedex_shipping_weight*($zc_large_percent/100)) + $zc_large_weight;
              } else {
                $this->fedex_shipping_weight = $this->fedex_shipping_weight + ($this->fedex_shipping_weight*($zc_tare_percent/100)) + $zc_tare_weight;
              }
              if ($this->fedex_shipping_weight <= 0) $this->fedex_shipping_weight = 0.1; 
              $new_shipping_weight += $this->fedex_shipping_weight;           
              $request['RequestedShipment']['RequestedPackageLineItems'][] = array('Weight' => array('Value' => $this->fedex_shipping_weight,
                                                                                                     'Units' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT
                                                                                                     ),
                                                                                   'GroupPackageCount' => 1,
                                                                                   'InsuredValue' => array(
                                                                                     'Currency' => $_SESSION['currency'],
                                                                                     'Amount' => $boxed_value          
                                                                                   ),                                                                                   
                                                                                  );
            }
          } else {
            $boxed_value = sprintf("%01.2f", $this->insurance / count($packages));
            // note $values is an array
            $new_shipping_num_boxes++;
            if ($values['weight'] <= 0) $values['weight'] = 0.1;
            $new_shipping_weight += $values['weight'];
            $request['RequestedShipment']['RequestedPackageLineItems'][] = array('Weight' => array('Value' => $values['weight'],
                                                                                                   'Units' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT),
                                                                                 'Dimensions' => array('Length' => ($values['length'] >= 1 ? $values['length'] : 1),
                                                                                                       'Width' => ($values['width'] >= 1 ? $values['width'] : 1),
                                                                                                       'Height' => ($values['height'] >= 1 ? $values['height'] : 1),
                                                                                                       'Units' => $values['units'] 
                                                                                                      ),
                                                                                 'InsuredValue' => array(
                                                                                   'Currency' => $_SESSION['currency'],
                                                                                   'Amount' => $boxed_value          
                                                                                 ),
                                                                                 'GroupPackageCount' => 1
                                                                                 );
          }
        }
        $this->fedex_shipping_num_boxes = $new_shipping_num_boxes;
        $this->fedex_shipping_weight = round($new_shipping_weight / $this->fedex_shipping_num_boxes, 2);
      } else {
        // Zen Cart default method for calculating number of packages
        
        // check if cart contains free shipping items (module would be disabled unless strictly enabled to still quote for always free shipping products)
        /*
        if ($_SESSION['cart']->in_cart_check('product_is_always_free_shipping','1')) {
          // cart contains free shipping, get products weights
          $shipping_weight = 0;
          $products = $_SESSION['cart']->get_products();
          foreach ($products as $product) {
            $shipping_weight += $product['weight'] * $product['quantity'];
          }
          $shipping_weight = $shipping_weight / $shipping_num_boxes;
        }
        */
        $boxed_value = sprintf("%01.2f", $this->insurance / $this->fedex_shipping_num_boxes);
        if (!($this->fedex_shipping_weight > 0)) $this->fedex_shipping_weight = 0.1;
        
        for ($i=0; $i<$this->fedex_shipping_num_boxes; $i++) {
          $request['RequestedShipment']['RequestedPackageLineItems'][] = array('Weight' => array('Value' => $this->fedex_shipping_weight,
                                                                                                 'Units' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT),
                                                                               'GroupPackageCount' => 1,
                                                                               'InsuredValue' => array(
                                                                                 'Currency' => $_SESSION['currency'],
                                                                                 'Amount' => $boxed_value          
                                                                               ),                  
                                                                              );
        }
      }
    }
    $request['RequestedShipment']['PackageCount'] = $this->fedex_shipping_num_boxes;
    
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_SATURDAY == 'true') {
      $request['RequestedShipment']['ServiceOptionType'] = 'SATURDAY_DELIVERY';
    }
    
    if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_SIGNATURE_OPTION >= 0 && $totals >= MODULE_SHIPPING_FEDEX_WEB_SERVICES_SIGNATURE_OPTION) { 
      $request['RequestedShipment']['SpecialServicesRequested'] = 'SIGNATURE_OPTION'; 
    }
    //echo '<!-- shippingWeight: ' . $shipping_weight . ' ' . $shipping_num_boxes . ' -->';                                                                                                                                             
    
    /*
    echo '<!-- ';
    echo '<pre>';
    print_r($request);                                                                                                                                                                                                                   
    echo '</pre>';
    echo ' -->';
    */

    return $request;
  }

  function quote($method = '') {
    /* FedEx integration starts */
    global $db, $shipping_weight, $shipping_num_boxes, $cart, $order, $all_products_ship_free;
    require_once(DIR_WS_INCLUDES . 'library/fedex-common.php5');


    //if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_SERVER == 'test') {
      //$request['Version'] = array('ServiceId' => 'crs', 'Major' => '7', 'Intermediate' => '0', 'Minor' => '0');
      //$path_to_wsdl = DIR_WS_INCLUDES . "wsdl/RateService_v7_test.wsdl";
    //} else {
    $path_to_wsdl = DIR_WS_MODULES . 'shipping/fedexwebservices/wsdl/RateService_v10.wsdl';
    //}
    ini_set("soap.wsdl_cache_enabled", "0");
    //IF SOAP COMPILED WITH PEAR UNCOMMENT BELOW
    //require_once('SOAP/Client.php');
    $client = new SoapClient($path_to_wsdl, array('trace' => 1, 'connection_timeout' => MODULE_SHIPPING_FEDEX_WEB_SERVICES_CONNECTION_TIMEOUT));


    $request = $this->build_request($client, true);
    $this->quotes = $this->do_request($method, $request, $client);

    if(MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'Ground/Home only' && !$all_products_ship_free) {
      //in this case, we need to do a second request and combine the results
      $request = $this->build_request($client, false); //third parameter false to disallow free (0 weight) shipping

      $full_price_quotes = $this->do_request($method, $request, $client);
      $zero_weight_quotes = $this->quotes;
      $this->quotes['methods'] = $full_price_quotes['methods']; //default to full price quotes
      //replace with zero weight quote for ground methods
      foreach($this->quotes['methods'] as $method_id => $method) {
        if(in_array($method['id'], array('GROUNDHOMEDELIVERY', 'GROUND_HOME_DELIVERY', 'FEDEX_GROUND', 'INTERNATIONAL_GROUND'))){
          foreach($zero_weight_quotes['methods'] as $zero_weight_id => $zero_weight_method){
            if($method['id'] == $zero_weight_method['id']) {
              $this->quotes['methods'][$method_id] = $zero_weight_quotes['methods'][$zero_weight_id];
            }
          }
        }
      }
    }
    return $this->quotes;

   }

  function do_request($method = '', $request, $client) { 
  global $db, $shipping_weight, $shipping_num_boxes, $cart, $order, $all_products_ship_free;
    try {
      $response = $client->getRates($request);
      
     /* 
      echo '<!-- '; 
      echo '<pre>';
      print_r($response);
      echo '</pre>';
      echo ' -->';
      */
      
      if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR' && is_array($response->RateReplyDetails) || is_object($response->RateReplyDetails)) {
        if (is_object($response->RateReplyDetails)) {
          $response->RateReplyDetails = get_object_vars($response->RateReplyDetails);
        }
        //echo '<pre>';
       // print_r($response->RateReplyDetails);
        //echo '</pre>';
        switch (SHIPPING_BOX_WEIGHT_DISPLAY) {
          case (0):
          $show_box_weight = '';
          break;
          case (1):
          $show_box_weight = ' (' . $shipping_num_boxes . ' ' . TEXT_SHIPPING_BOXES . ')';
          break;
          case (2):
          echo '<!-- ' . $this->fedex_shipping_weight . ' ' . $this->fedex_shipping_num_boxes . ' -->';
          $show_box_weight = ' (' . number_format($this->fedex_shipping_weight * $this->fedex_shipping_num_boxes,2) . TEXT_SHIPPING_WEIGHT . ')';
          break;
          default:
          $show_box_weight = ' (' . $this->fedex_shipping_num_boxes . ' x ' . number_format($this->fedex_shipping_weight,2) . TEXT_SHIPPING_WEIGHT . ')';
          break;
        }      
        $quotes = array('id' => $this->code,
                              'module' => $this->title . $show_box_weight,
                              'info' => $this->info());
        $methods = array();
        foreach ($response->RateReplyDetails as $rateReply) {
          if (array_key_exists($rateReply->ServiceType, $this->types) && ($method == '' || str_replace('_', '', $rateReply->ServiceType) == $method)) {
            $showAccountRates = true;
            if(MODULE_SHIPPING_FEDEX_WEB_SERVICES_RATES=='LIST') {
              foreach($rateReply->RatedShipmentDetails as $ShipmentRateDetail) {
                if($ShipmentRateDetail->ShipmentRateDetail->RateType=='PAYOR_LIST_PACKAGE') {
                  $cost = $ShipmentRateDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                  $cost = (float)round(preg_replace('/[^0-9.]/', '',  $cost), 2);
                  if ($cost > 0) $showAccountRates = false;
                }                                              
              }
            }
            if ($showAccountRates) {
              $cost = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
              $cost = (float)round(preg_replace('/[^0-9.]/', '',  $cost), 2);
            }
            $transitTime = '';
            if (MODULE_SHIPPING_FEDEX_WEB_SERVICES_TRANSIT_TIME == 'true' && in_array($rateReply->ServiceType, array('GROUND_HOME_DELIVERY', 'FEDEX_GROUND', 'INTERNATIONAL_GROUND'))) {
              $transitTime = ' (' . str_replace(array('_', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen'), array(' business ', 1,2,3,4,5,6,7,8,9,10,11,12,13,14), strtolower($rateReply->TransitTime)) . ')';
            }
            
            // added condition that cost must be greater than 0.  Rate can still be made free using handling fees.
            if ($cost > 0) {
              $new_cost = $cost + (strpos($this->types[$rateReply->ServiceType]['handling_fee'], '%') ? ($cost * (float)$this->types[$rateReply->ServiceType]['handling_fee'] / 100) : (float)$this->types[$rateReply->ServiceType]['handling_fee']);
              //if all items in the order are "Always Free Shipping" items, show free shipping as specified in config
              if($all_products_ship_free) {
                if(MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'all methods') {
                  $new_cost = 0;
                }
                else if(MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD == 'Ground/Home only') {
                  if(in_array($rateReply->ServiceType, array('GROUND_HOME_DELIVERY', 'FEDEX_GROUND', 'INTERNATIONAL_GROUND'))) {
                    $new_cost = 0;
                  }
                }
              }
              if ($new_cost < 0) $new_cost = 0;
              $methods[] = array('id' => str_replace('_', '', $rateReply->ServiceType),
                                 'title' => ucwords(strtolower(str_replace('_', ' ', $rateReply->ServiceType))) . $transitTime,
                                 'cost' => $new_cost);
            }
          }
        }
        if (sizeof($methods) == 0) return false;
        $quotes['methods'] = $methods;
        if ($this->tax_class > 0) {
          $quotes['tax'] = zen_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
        } 
      } else {
        $message = 'Error in processing transaction.<br /><br />'; 
        $message .= $response->Notifications->Severity;
        $message .= ': ';           
        $message .= $response->Notifications->Message . '<br />';
        $quotes = array('module' => $this->title,
                              'error'  => $message);
      }
      if (zen_not_null($this->icon)) $this->quotes['icon'] = zen_image($this->icon, $this->title);
    } catch (Exception $e) {
      $quotes = array('module' => $this->title,
                            'error'  => 'Sorry, the FedEx.com server is currently not responding, please try again later.');
    }
    //echo '<!-- Quotes: ';
    //print_r($this->quotes);
    //print_r($_SESSION['shipping']);
    //echo ' -->';
    return $quotes;
  }

  // method added for expanded info in FEAC
  function info() {
    return MODULE_SHIPPING_FEDEX_WEB_SERVICES_INFO; // add a description here or leave blank to disable
  }
    
  function _setInsuranceValue($order_amount){
    if ($order_amount > (float)MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE) {
      $this->insurance = sprintf("%01.2f", $order_amount);
    } else {
      $this->insurance = 0;
    }
  }
  
  function objectToArray($object) {
    if( !is_object( $object ) && !is_array( $object ) ) {
      return $object;
    }
    if( is_object( $object ) ) {
      $object = get_object_vars( $object );
    }
    return array_map( 'objectToArray', $object );
  }
  
  function _setDropOff() {
    switch(MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF) {
      case '1':
        return 'REGULAR_PICKUP';
        break;
      case '2':
        return 'REQUEST_COURIER';
        break;
      case '3':
        return 'DROP_BOX';
        break;
      case '4':
        return 'BUSINESS_SERVICE_CENTER';
        break;
      case '5':
        return 'STATION';
        break;
    }
  }

  function check(){
    global $db;
    if(!isset($this->_check)){
      $check_query  = $db->Execute("SELECT configuration_value FROM ". TABLE_CONFIGURATION ." WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS'");
      $this->_check = $check_query->RecordCount();      
      if ($this->_check && defined('MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION')) { 
        switch(MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION) {
          case '1.4.0':
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.1' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Insurance', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE', '0', 'Insure packages when order total is greated than:', '6', '25', now())");
            // do not break and continue to the next version
          case '1.4.1':
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.2' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
          case '1.4.2':
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SHIPPING_BOXES_MANAGER' LIMIT 1;");
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.3' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
          case '1.4.3':
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SHIPPING_BOXES_MANAGER' LIMIT 1;");
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.4' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Connection Timeout', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CONNECTION_TIMEOUT', '15', 'Enter the maximum time limit in seconds that the server should wait when connecting to the FedEx server.', '6', '10', now())");
            break;
          case '1.4.4':
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SHIPPING_BOXES_MANAGER' LIMIT 1;");
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.5' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            break;
          case '1.4.5':
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SHIPPING_BOXES_MANAGER' LIMIT 1;");
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.6' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            break;
          case '1.4.6':
            $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SHIPPING_BOXES_MANAGER' LIMIT 1;");
            $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Always Free Shipping items for', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD', '1', 'Allow ALWAYS FREE SHIPPING Items to ship for free with:', '6', '30', 'zen_cfg_select_option(array(\'no methods\',\'Ground/Home only\',\'all methods\'),', now())");
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.7' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            break;
          case '1.4.7':
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.8' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            break;
          case '1.4.8':
            $db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.4.9' WHERE configuration_key = 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION' LIMIT 1;");
            break;                        
        }
      }
    }
    return $this->_check;
  }

  function install() {
    global $db;
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable FedEx Web Services','MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS','true','Do you want to offer FedEx shipping?','6','0','zen_cfg_select_option(array(\'true\',\'false\'),',now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Version Installed', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION', '1.4.9', '', '6', '0', now())"); 
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('FedEx Account Number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM', '', 'Enter FedEx Account Number', '6', '3', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('FedEx Meter Number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM', '', 'Enter FedEx Meter Number (You can get one at <a href=\"http://www.fedex.com/us/developer/\" target=\"_blank\">http://www.fedex.com/us/developer/</a>)', '6', '4', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable Address Validation','MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_VALIDATION','false','Would you like to use the FedEx Address Validation service to determine if an address is residential or commercial?','6','9','zen_cfg_select_option(array(\'true\',\'false\'),',now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Weight Units', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT', 'LB', 'Weight Units:', '6', '10', 'zen_cfg_select_option(array(\'LB\', \'KG\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('First line of street address', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1', '', 'Enter the first line of your ship-from street address, required', '6', '20', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Second line of street address', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2', '', 'Enter the second line of your ship-from street address, leave blank if you do not need to specify a second line', '6', '21', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('City name', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY', '', 'Enter the city name for the ship-from street address, required', '6', '22', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('State or Province name', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE', '', 'Enter the 2 letter state or province name for the ship-from street address, required for Canada and US', '6', '23', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Postal code', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL', '', 'Enter the postal code for the ship-from street address, required', '6', '24', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Phone number', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PHONE', '', 'Enter a contact phone number for your company, required', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable for Always Free Shipping', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING', 'false', 'Should this module be enabled even when all items in the cart are marked as ALWAYS FREE SHIPPING?', '6', '30', 'zen_cfg_select_option(array(\'true\',\'false\'),', now())");
	$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Always Free Shipping items for', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD', '1', 'Allow ALWAYS FREE SHIPPING Items to ship for free with:', '6', '30', 'zen_cfg_select_option(array(\'no methods\',\'Ground/Home only\',\'all methods\'),', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Drop off type', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF', '1', 'Dropoff type (1 = Regular pickup, 2 = request courier, 3 = drop box, 4 = drop at BSC, 5 = drop at station)?', '6', '30', 'zen_cfg_select_option(array(\'1\',\'2\',\'3\',\'4\',\'5\'),', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Express Saver', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER', 'true', 'Enable FedEx Express Saver', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Standard Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT', 'true', 'Enable FedEx Express Standard Overnight', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable First Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT', 'true', 'Enable FedEx Express First Overnight', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Priority Overnight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT', 'true', 'Enable FedEx Express Priority Overnight', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable 2 Day', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY', 'true', 'Enable FedEx Express 2 Day', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable International Priority', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY', 'true', 'Enable FedEx Express International Priority', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable International Economy', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY', 'true', 'Enable FedEx Express International Economy', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ground', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND', 'true', 'Enable FedEx Ground', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable International Ground', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND', 'true', 'Enable FedEx International Ground', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Freight', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT', 'true', 'Enable FedEx Freight', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Saturday Delivery', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SATURDAY', 'false', 'Enable Saturday Delivery', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Domestic Ground Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE', '', 'Add a domestic handling fee or leave blank (example: 15 or 15%)', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Domestic Express Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE', '', 'Add a domestic handling fee or leave blank (example: 15 or 15%)', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Home Delivery Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HOME_DELIVERY_HANDLING_FEE', '', 'Add a home delivery handling fee or leave blank (example: 15 or 15%)', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('International Ground Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_HANDLING_FEE', '', 'Add an international handling fee or leave blank (example: 15 or 15%)', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('International Express Handling Fee', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE', '', 'Add an international handling fee or leave blank (example: 15 or 15%)', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('FedEx Rates','MODULE_SHIPPING_FEDEX_WEB_SERVICES_RATES','LIST','FedEx Rates (LIST = FedEx default rates, ACCOUNT = Your discounted rates)','6','0','zen_cfg_select_option(array(\'LIST\',\'ACCOUNT\'),',now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Signature Option', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SIGNATURE_OPTION', '-1', 'Require a signature on orders greater than or equal to (set to -1 to disable):', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ready to Ship', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_READY_TO_SHIP', 'false', 'Enable using products_ready_to_ship field (requires Numinix Product Fields optional dimensions fields) to identify products which ship separately?', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Estimated Transit Time', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TRANSIT_TIME', 'false', 'Display the transit time for ground methods?', '6', '10', 'zen_cfg_select_option(array(\'true\', \'false\'), ', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Connection Timeout', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CONNECTION_TIMEOUT', '15', 'Enter the maximum time limit in seconds that the server should wait when connecting to the FedEx server.', '6', '10', now())");    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Insurance', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE', '0', 'Insure packages when order total is greated than:', '6', '25', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '98', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '25', 'zen_get_tax_class_title', 'zen_cfg_pull_down_tax_classes(', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER', '0', 'Sort order of display.', '6', '999', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Shipping Info', 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INFO', '', 'Add a description that will display in Fast and Easy AJAX Checkout', '6', '99', 'zen_cfg_textarea(', now())"); 
  }

  function remove() {
    global $db;
    $db->Execute("DELETE FROM ". TABLE_CONFIGURATION ." WHERE configuration_key in ('". implode("','",$this->keys()). "')");
  }

  function keys() {
    return array('MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATUS',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_VERSION', 
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ACT_NUM',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_METER_NUM',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_VALIDATION',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_WEIGHT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_1',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ADDRESS_2',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CITY',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STATE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_POSTAL',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PHONE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_DROPOFF',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREE_SHIPPING_METHOD',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_SAVER',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_STANDARD_OVERNIGHT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FIRST_OVERNIGHT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_PRIORITY_OVERNIGHT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_2DAY',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_PRIORITY',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_ECONOMY',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_GROUND', 
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_FREIGHT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INTERNATIONAL_GROUND',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SATURDAY',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TAX_CLASS',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HANDLING_FEE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_HOME_DELIVERY_HANDLING_FEE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_EXPRESS_HANDLING_FEE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_HANDLING_FEE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INT_EXPRESS_HANDLING_FEE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SIGNATURE_OPTION',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INSURE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_RATES',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_READY_TO_SHIP',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_TRANSIT_TIME',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_CONNECTION_TIMEOUT',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_ZONE',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_INFO',
                 'MODULE_SHIPPING_FEDEX_WEB_SERVICES_SORT_ORDER'
                 );
  }
}
