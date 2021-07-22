<?php
/**
	* Side Box Template
	*
	* @package templateSystem
	* @copyright Copyright 2003-2005 Zen Cart Development Team
	* @copyright Portions Copyright 2003 osCommerce
	* @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
	* @version $Id: tpl_best_sellers.php 2982 2006-02-07 07:56:41Z birdbrain $
*/

$content = '';
$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";

// Defining the MARQUEE
$total = sizeof($bestsellers_list);
if ($total > 1) { //if more than one bestseller exists in the db then let the scrolling begin
	 if(!strpos($_SERVER["HTTP_USER_AGENT"],"Chrome")){
	$content .= '<MARQUEE height= "1000px" behavior= "alternate" align= "left" direction= "up" scrollamount= "1" scrolldelay= "5" onmouseover=\'this.stop()\' onmouseout=\'this.start()\'>';
	}
} elseif ($total == 1) {// If only one bestseller exists in the db then the bestseller box should remain static
	// Do nothing
} else { //If there are no bestsellers then this text is displayed
	$content .= "No bestsellers this month!";
}

// Defining the actual bestsellers
$content .= '<div class="wrapper"><table cellpadding=2 cellspacing=0 border=0>' ."\n";
for ($i = 1; $i <= $total; $i++) {
	$imgLink = DIR_WS_IMAGES . $bestsellers_list[$i]['image'];
	if ($i == 2) {$content .= '';}
	$content .= '<tr><td valign="center"; align="center";><a href="' . zen_href_link(zen_get_info_page($bestsellers_list[$i]['id']), 'products_id=' . $bestsellers_list[$i]['id']) . '">' . 	zen_image($imgLink, zen_trunc_string($bestsellers_list[$i]['name']), SMALL_IMAGE_WIDTH/2, SMALL_IMAGE_HEIGHT/2) . '</a></td><td valign=top><a href="' . zen_href_link(zen_get_info_page($bestsellers_list[$i]['id']), 'products_id=' . $bestsellers_list[$i]['id']) . '">' . zen_trunc_string($bestsellers_list[$i]['name'], BEST_SELLERS_TRUNCATE, BEST_SELLERS_TRUNCATE_MORE) . '</a><BR><div style= "align: left; color:#FF0000;">' . zen_get_products_display_price($bestsellers_list[$i]['id']) . '</div></td></tr>' . 	"\n";
}
$content .= '</table></div>' . "\n";

if ($total > 1) {
	 if(!strpos($_SERVER["HTTP_USER_AGENT"],"Chrome"))
	 {
		$content .= '</MARQUEE>';
	 }
}

$content .= '</div>';
?>