<?php
/**
 * Footer Menu Definitions
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V3.0
 * @version $Id: footer_menu_deines.php 1.0 5/9/2009 Clyde Jones $
 */

/*BOF Menu Column 1 link Definitions*/
Define('TITLE_ONE', '<tophead>Quick Links</tophead><br /><br />');
Define('HOME', '<li><a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . HEADER_TITLE_CATALOG . '</a></li>');
Define('FEATURED','<li><a href="' . zen_href_link(FILENAME_FEATURED_PRODUCTS) . '">' .  TABLE_HEADING_FEATURED_PRODUCTS .  '</a></li>');
Define('SPECIALS', '<li><a href="' . zen_href_link(FILENAME_SPECIALS) . '">' . BOX_HEADING_SPECIALS . '</a></li>');
Define('NEWPRODUCTS', '<li><a href="' . zen_href_link(FILENAME_PRODUCTS_NEW) . '">' . BOX_HEADING_WHATS_NEW . '</a></li>');
Define('ALLPRODUCTS', '<li><a href="' . zen_href_link(FILENAME_PRODUCTS_ALL) . '">' .CATEGORIES_BOX_HEADING_PRODUCTS_ALL . '</a></li>');
/*EOF Menu Column 1 link Definitions*/

/*OF Menu Column 2 link Definitions*/
Define('TITLE_TWO', '<tophead>Information</tophead><br /><br />');
Define('ABOUT', '<li><a href="' . zen_href_link(FILENAME_ABOUT_US) . '">' . BOX_INFORMATION_ABOUT_US . '</a></li>');
Define('SITEMAP', '<li><a href="' . zen_href_link(FILENAME_SITE_MAP) . '">' . BOX_INFORMATION_SITE_MAP . '</a></li>');
Define('GVFAQ', '<li><a href="' . zen_href_link(FILENAME_GV_FAQ) . '">' . BOX_INFORMATION_GV . '</a></li>');
Define('COUPON', '<li><a href="' . zen_href_link(FILENAME_DISCOUNT_COUPON) . '">' .  BOX_INFORMATION_DISCOUNT_COUPONS . '</a></li>');
Define('UNSUBSCRIBE', '<li><a href="' . zen_href_link(FILENAME_UNSUBSCRIBE) . '">' . BOX_INFORMATION_UNSUBSCRIBE . '</a></li>');
/*EOF Menu Column 2 link Definitions*/

/*BOF Menu Column 3 link Definitions*/
Define('TITLE_THREE', '<tophead>Customer Service</tophead><br /><br />');
Define('CONTACT','<li><a href="' . zen_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a></li>');
Define('SHIPPING', '<li><a href="' . zen_href_link(FILENAME_SHIPPING) . '">' . BOX_INFORMATION_SHIPPING . '</a></li>');
Define('PRIVACY', '<li><a href="' . zen_href_link(FILENAME_PRIVACY) . '">' . BOX_INFORMATION_PRIVACY . '</a></li>');
Define('CONDITIONS','<li><a href="' . zen_href_link(FILENAME_CONDITIONS) . '">' . BOX_INFORMATION_CONDITIONS . '</a></li>');
Define('ACCOUNT', '<li><a href="' . zen_href_link(FILENAME_ACCOUNT, '', 'SSL') .'">' . HEADER_TITLE_MY_ACCOUNT . '</a></li>');
/*EOF Menu Column 3 link Definitions*/

/*BOF Menu Column 4 link Definitions*/
Define('TITLE_FOUR', '<tophead>Important Links</tophead><br /><br />');
/*The actual links are determined by "footer links" set in EZ-Pages
*EOF Menu Column 4 link Definitions
*/

/*BOF Footer Menu Definitions*/
Define('QUICKLINKS', '<dd class="first">
<ul>' . TITLE_ONE . HOME . FEATURED . SPECIALS . NEWPRODUCTS . ALLPRODUCTS . '</ul></dd>');
Define('INFORMATION', '<dd class="second">
<ul>' . TITLE_TWO . ABOUT . SITEMAP . GVFAQ . COUPON . UNSUBSCRIBE . '</ul></dd>');
Define('CUSTOMER_SERVICE', '<dd class="third">
<ul>' . TITLE_THREE . CONTACT . SHIPPING . PRIVACY . CONDITIONS . ACCOUNT . '</ul></dd>');
Define('IMPORTANT', '<dd><ul>' . TITLE_FOUR);
Define('IMPORTANT_END', '</ul></dd>');
/*EOF Footer Menu Definitions*/

//EOF