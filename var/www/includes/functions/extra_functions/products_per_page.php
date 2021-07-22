<?php
/*
* Set Number Of Products Per Page
* file version 2012-09-27
* Edited by rbarbour (ZCAdditions.com), Set Number Of Products Per Page (24)
*/
	function draw_max_ppp($page,$listing_page_name,$number_of_products_per_row, $total_number_of_products){
		$steps = explode(',', PRODUCT_LISTING_MAX_DISPLAY_STEPS); 
		$result = '';
		$max_display = 0;
		foreach ($steps as $step):
			if($step != 'All')
				if ((int)$step > $total_number_of_products)
					continue;
				else{
					$step = ceil((int)$step / $number_of_products_per_row) * $number_of_products_per_row;
					if ($max_display < $step)
						$max_display = $step;
				}
			else {
				$max_display = ceil($total_number_of_products / $number_of_products_per_row) * $number_of_products_per_row;
			}
			//echo'<br />$max_display='.$max_display.'<br />';

if ($_GET['max_display'] == $max_display or $_SESSION['product_listing_max_display'] == $max_display or $total_number_of_products > $max_display && !$_SESSION['product_listing_max_display']) {
$class = 'active_ppp';
} else {
$class = 'inactive_ppp';
}

if (PRODUCT_LISTING_DISPLAY_OPTION == 'true') {
$result .= '<option value="' . zen_href_link($page, zen_get_all_get_params(array('max_display',$listing_page_name)))."&amp;max_display=$max_display".'">'.$step. '</option>';
} else {
$result .= '<a href="'.zen_href_link($page, zen_get_all_get_params(array('max_display',$listing_page_name)))."&amp;max_display=$max_display".'" class="'.$class.'">'.$step.'</a> ';		
}

		endforeach;
		return $result;
	}
?>