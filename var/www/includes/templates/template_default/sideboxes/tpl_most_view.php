<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_best_sellers.php 2982 2006-02-07 07:56:41Z birdbrain $
 * @version $Id: tpl_best_sellers.php 2982 2007-12-15 21:00:00 TRUST IT - www.trustit.ca - ahmad@trustit.ca $
 * @version $Id: tpl_most_view.php 2982 2008-09-15 21:00:00 Remigio Ruberto - www.100asa.it $
 */
  $content = '';
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";
  $content .= '<div class="wrapper"><table cellpadding=0 cellspacing=0 border=0>' .  "\n";
  for ($i=1; $i<=sizeof($bestsellers_list); $i++) {
    $imgLink =  DIR_WS_IMAGES . $bestsellers_list[$i]['image'];
	//if ($i>=2) {$content .= '<tr><td colspan=2><hr></td></tr>';}
    $content .= '<tr><td valign=top><a href="' . zen_href_link(zen_get_info_page($bestsellers_list[$i]['id']), 'products_id=' . $bestsellers_list[$i]['id']) . '">' . 	zen_image($imgLink, zen_trunc_string($bestsellers_list[$i]['name']), SMALL_IMAGE_WIDTH/2, SMALL_IMAGE_HEIGHT/2) . '</a></td><td valign=top><a href="' . zen_href_link(zen_get_info_page($bestsellers_list[$i]['id']), 'products_id=' . $bestsellers_list[$i]['id']) . '">' . zen_trunc_string($bestsellers_list[$i]['name'], BEST_SELLERS_TRUNCATE, BEST_SELLERS_TRUNCATE_MORE) . '</a><BR><div align=right>' . zen_get_products_display_price($bestsellers_list[$i]['id']) . '</div></td></tr>' . 	"\n";
  }
  $content .= '</table></div>' . "\n";
  $content .= '</div>';

?>
