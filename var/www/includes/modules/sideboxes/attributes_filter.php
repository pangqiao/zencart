<?php
$categories = array();
zen_get_subcategories($categories,$current_category_id);
$categories[] = $cur_category;//分类

require(zen_get_index_filters_directory('default_filter.php'));
$r = $db->Execute($listing_sql,false,false,604800);//缓存7天

if ($r->RecordCount()>0){
	
	$sql = 'select p.products_id,pa.options_id,po.products_options_name,pa.options_values_id,pov.products_options_values_name,count(options_values_id) as products_num from '.TABLE_PRODUCTS_ATTRIBUTES.' pa '.
	'join '.TABLE_PRODUCTS_OPTIONS.' po on po.products_options_id=pa.options_id '.
	'join '.TABLE_PRODUCTS_OPTIONS_VALUES.' pov on pov.products_options_values_id=pa.options_values_id '.
	'join '.TABLE_PRODUCTS.' p on p.products_id=pa.products_id '.
	'join ('.$listing_sql.') t on t.products_id=pa.products_id '.
	'where po.products_options_type IN ('.AVAILABLE_FILTER_RANGE.') group by pa.options_values_id';

	$r = $db->Execute($sql,false,false,604800);//缓存7天
	if ($r->RecordCount()>0){
		while (!$r->EOF){
			if (!isset($options_list[$r->fields['options_id']])){
				$options_list[$r->fields['options_id']] = array('option_name'=>$r->fields['products_options_name'],'options_values'=>array());
			}	
			$options_list[$r->fields['options_id']]['options_values'][$r->fields['options_values_id']] =
			array(
					'option_value_name'=>$r->fields['products_options_values_name'],
					'option_value_id'=>$r->fields['options_values_id'],
					'products_num'=>$r->fields['products_num'],
// 					'link'=>zen_href_link(FILENAME_DEFAULT,$link_params.'options_values_id='.implode('_', $options_values_id_link)),
			);							

			$r->MoveNext();
		}
		$title = TEXT_ATTRIBUTES_FILTER_TITLE;
		if (zen_not_null($attr_id_array)){
			$title .= '<a class="clear-all" href="'.zen_href_link(FILENAME_DEFAULT,zen_get_all_get_params(array('options_values_id','sort'))).'">'.TEXT_ATTRIBUTES_CLEAR_ALL.'</a>';
		}
		$link = false;
		
		require ($template->get_template_dir('tpl_attributes_filter.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_attributes_filter.php');
		require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);	
	}
}