<?php 
$content = '';
if (zen_not_null($options_list)){
	$link_params = zen_get_all_get_params(array('options_values_id','sort','page','x','y'));
	$content .= '<ul class="narrow_by_wrapper">';
	foreach ($options_list as $options_id=>$options_list_entry){
		$content .= '<li class="attr-group">';
		$content .= '<div class="attr-name">'.$options_list_entry['option_name'].'</div>';
		$content .= '<ul class="attr-values-group">';
		$options_values_id_array = array_keys($options_list_entry['options_values']);
// 		var_dump($options_values_id_array);exit;
		foreach ($options_list_entry['options_values'] as $value_id=>$options_values_entry){
			if (in_array($value_id , $attr_id_array)){
				$options_values_id_link = array_diff($attr_id_array,array($value_id));
				$class_name = 'class="active"';
			}else{
				$options_values_id_link = array_merge($attr_id_array,array($value_id));
				$class_name = '';
			}

			$nofollow = '';
			if (zen_not_null($options_values_id_link)){
				if(AVAILABLE_ADD_NOFOLLOW != 0 && AVAILABLE_ADD_NOFOLLOW<=count($options_values_id_link)){
					$nofollow = 'rel="nofollow"';
				}
				$options_values_id_params = 'options_values_id='.implode('_', $options_values_id_link);
			}else 
				$options_values_id_params = '';
// 			echo $link_params.$options_values_id_params.'<br />';exit;
			$content .= '<li class="attr-value '.$class_name.'"><a href="'.zen_href_link(FILENAME_DEFAULT,$link_params.$options_values_id_params).'" '.$nofollow.'>'.$options_values_entry['option_value_name'].'('.$options_values_entry['products_num'].')</a></li>';
		}
		$content .= '</ul>';
		$content .= '</li>';
	}
	$content .= '</ul>';
}
?>