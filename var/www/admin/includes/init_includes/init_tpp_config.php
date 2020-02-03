<?php

    $tpp_menu_title = 'Tabbed Products Pro - Configuration';
    $tpp_menu_text = 'Set Tabbed Products Pro options';
	$tpp_menu_old_title1 = 'TPP - Config';
	$tpp_menu_old_title2 = 'Tabbed Products Config';

    // Configuration Values to create or preserve
    $menu_items_tpp = array(
            array('TPP_VERSION','1.10',1001,false),
            array('TPP_GLOBAL_ENABLE_TABS','1',1011,array('0','1')),
            array('TPP_GLOBAL_MAIN_IMAGE_ON_TAB','0',1021,array('0','1')),
            array('TPP_GLOBAL_PROD_DESC_ON_TAB','1',1031,array('0','1')),
            array('TPP_GLOBAL_ATTRIBUTES_ON_TAB','0',1041,array('0','1')),
            array('TPP_GLOBAL_ATTRIBUTES_ON_ATC_TAB','0',1051,array('0','1')),
            array('TPP_GLOBAL_DETAILS_ON_TAB','1',1061,array('0','1')),
            array('TPP_GLOBAL_ADD_TO_CART_ON_TAB','0',1071,array('0','1')),
            array('TPP_GLOBAL_ADDL_IMAGES_ON_TAB','1',1081,array('0','1')),
            array('TPP_GLOBAL_CUST_ALSO_PURCH_ON_TAB','1',1091,array('0','1')),
            array('TPP_GLOBAL_CROSS_SELL_ON_TAB','0',1101,array('0','1')),
            array('TPP_GLOBAL_REVIEWS_ON_TAB','1',1111,array('0','1')),
            array('TPP_GLOBAL_CUSTOM1_ON_TAB','0',1121,array('0','1')),
            array('TPP_GLOBAL_CUSTOM2_ON_TAB','0',1131,array('0','1')),
            array('TPP_GLOBAL_CUSTOM3_ON_TAB','0',1141,array('0','1')),
            array('TPP_SHOW_TAB_HEADERS','1',1151,array('0','1'))
            );

    // Legacy Configuration Values to Delete Completely
    $menu_items_delete = array(
            array('GLOBAL_ENABLE_TABS'),
            array('GLOBAL_MAIN_IMAGE_ON_TAB'),
            array('GLOBAL_PROD_DESC_ON_TAB'),
            array('GLOBAL_ATTRIBUTES_ON_TAB'),
            array('GLOBAL_ATTRIBUTES_ON_ATC_TAB'),
            array('GLOBAL_DETAILS_ON_TAB'),
            array('GLOBAL_ADD_TO_CART_ON_TAB'),
            array('GLOBAL_ADDL_IMAGES_ON_TAB'),
            array('GLOBAL_CUST_ALSO_PURCH_ON_TAB'),
            array('GLOBAL_CROSS_SELL_ON_TAB'),
            array('GLOBAL_REVIEWS_ON_TAB'),
            array('SHOW_TAB_HEADERS'),
            array('TPP_PAGES'),
            array('TPP_STYLESHEET')
            );

    /* find if TPP Configuration Config Exists */
    $sql = "SELECT * FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title = '".$tpp_menu_title."'";
    $original_config = $db->Execute($sql);

    if($original_config->RecordCount())
    {
        // updating existing entry
        $sql = "UPDATE ".TABLE_CONFIGURATION_GROUP." SET 
                configuration_group_description = '".$tpp_menu_text."' 
                WHERE configuration_group_title = '".tpp_menu_title."'";
        $db->Execute($sql);
        $sort = $original_config->fields['sort_order'];

    }else{
        /* Find max sort order */
        $sql = "SELECT (MAX(sort_order)+2) as sort FROM ".TABLE_CONFIGURATION_GROUP;
        $result = $db->Execute($sql);
        $sort = $result->fields['sort'];

        /* Create configuration group */
        $sql = "INSERT INTO ".TABLE_CONFIGURATION_GROUP." (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, '".$tpp_menu_title."', '".$tpp_menu_text."', ".$sort.", '1')";
        $db->Execute($sql);

        /* Delete old configuration group */
        $sql = "DELETE FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title = '".$tpp_menu_old_title1."'";
        $db->Execute($sql); 
        $sql = "DELETE FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title = '".$tpp_menu_old_title2."'";
        $db->Execute($sql); 
   }

    /* Find Config ID of TPP */
    $sql = "SELECT configuration_group_id FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title='".$tpp_menu_title."' LIMIT 1";
    $result = $db->Execute($sql);
        $tpp_configuration_id = $result->fields['configuration_group_id'];


    // add items to the Config Menu 
    foreach($menu_items_tpp as $menu_item)
    {
    xxxx_create_menu_item($menu_item[0],$menu_item[1],$menu_item[2],$tpp_configuration_id,$menu_item[3]);
    }

    // delete legacy configuration options
    foreach($menu_items_delete as $del)
    {
        $sql ="DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key = '".$del[0]."'";
        $db->Execute($sql);
    }


    // now un-register v1.08 TPP admin page
    zen_deregister_admin_pages('TPP');
     // now register the admin page
    // Display TPP in Config Menu   zen_deregister_admin_pages('configTPP');
    zen_deregister_admin_pages('configTPP');
    zen_register_admin_page('configTPP',
        'BOX_CONFIGURATION_TPP', 'FILENAME_CONFIGURATION',
        'gID=' . $tpp_configuration_id, 'configuration', 'Y',
        $sort);


    if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.tpp.php'))
    {
        if(!unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.tpp.php'))
	{
		$messageStack->add('The auto-loader '.DIR_FS_ADMIN.'includes/auto_loaders/config.tpp.php has not been deleted. For Tabbed Products Pro to work you must delete this file manually.','error');
	};
    }

       $messageStack->add('Tabbed Products Pro v1.10 install completed!','success');


//=======================================
// niccol standard create menu item function
//=======================================

    function xxxx_create_menu_item($c_key,$default,$sort,$config_id,$values)
    {
            global $db;
            $title = $c_key.'_TITLE';


            $text = $c_key.'_TEXT';

            
            $sql = "SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = '".$c_key."' LIMIT 1";
            $results = $db->Execute($sql);
            
            $config_value = ($results->fields['configuration_value'] !='')?$results->fields['configuration_value']: $default;

            $sql ="DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key = '".$c_key."'";
            $db->Execute($sql);

            if($values)
            {
                foreach($values as $v)
                {
                $v_string .= "''".$v."'',";
                }
                $v_arr = substr($v_string,0,-1);
                $sql = "INSERT INTO ".TABLE_CONFIGURATION." (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES (NULL, '".constant($title)."', '".$c_key."', '".$config_value."', '".constant($text)."', ".$config_id.", ".$sort.", now(), now(), NULL, 'zen_cfg_select_option(array(".$v_arr."),')";
            }else{
                // text input type
                $sql = "INSERT INTO ".TABLE_CONFIGURATION." (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES (NULL, '".constant($title)."', '".$c_key."', '".$config_value."', '".constant($text)."', ".$config_id.", ".$sort.", now(), now(), NULL, NULL)";
            }
    
            $db->Execute($sql);  



            return true;

    }