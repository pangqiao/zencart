<?php
define('AVAILABLE_FILTER_RANGE', '5');//格式:1,2,3;有效的属性筛选范围:0-Dropdown,1-Text,2-Radio,3-Checkbox,4-File,5-Read Only
define('AVAILABLE_ADD_NOFOLLOW', 0);//0-属性筛选链接不加nofollow,1-n为属性组合大于这个数据时加;例如:当为1时,白色+24码+..就加,只有白色或24码就不加
$autoLoadConfig[160][] = array('autoType'=>'init_script','loadFile'=> 'init_attrbutes_filter.php');