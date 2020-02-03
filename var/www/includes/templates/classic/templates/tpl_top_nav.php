<?php
/**
* Template designed by 12leaves.com
* 12leaves.com - Free ecommerce templates and design services
*
* @copyright Copyright 2003-2006 Zen Cart Development Team
* @copyright Portions Copyright 2003 osCommerce
* @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
* @version $Id: index.php 6550 2007-07-05 03:54:54Z drbyte $
*/    
?>



<div id="top_nav">
	<div id="tab_nav">
	<!--<div class="top-nav-left"></div>-->
	<div class="top-nav-right"></div>
		<ul class="list-style-none">
			<li class="home-link"><a href="<?php echo '' . HTTP_SERVER . DIR_WS_CATALOG;?>"><?php echo TOP_MENU_HOME;?></a></li>

	<?php
	if ($current_page_base == 'products_new') {	$active	= 'tab_active'; 
		} else { $active = '';
	}?>
			<li class="<?php echo $active;?>"><a href="index.php?main_page=products_new"><?php echo TOP_MENU_NEW_PRODUCTS;?></a></li>

	<?php
	if ($current_page_base == 'specials') { $active = 'tab_active'; 
		} else { $active = '';
	}?>
			<li class="<?php echo $active;?>"><a href="index.php?main_page=specials"><?php echo TOP_MENU_SPECIALS;?></a></li>

	<?php 
	if ($current_page_base == 'featured_products') { $active = 'tab_active'; 
		} else { $active = '';
	}?>
			<li class="<?php echo $active;?>"><a href="index.php?main_page=featured_products"><?php echo TOP_MENU_FEATURED_PRODUCTS;?></a></li>

	<?php
	if ($current_page_base == 'account' || $current_page_base == 'login' || $current_page_base == 'account_edit' || $current_page_base == 'address_book' || $current_page_base == 'account_password' || $current_page_base == 'account_newsletters' || $current_page_base == 'account_notifications') { $active = 'tab_active'; 
		} else { $active = '';
	}?>
			<li class="<?php echo $active;?>"><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo TOP_MENU_MY_ACCOUNT;?></a></li>

	<?php
	if ($current_page_base == 'shopping_cart') { $active = 'tab_active'; 
		} else { $active = '';
	}?>
			<li class="<?php echo $active;?>"><a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>"><?php echo TOP_MENU_VIEW_CART;?></a></li>
		</ul>
	</div>

</div>





<div class="clearBoth"></div>