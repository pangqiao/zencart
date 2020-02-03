<?php
/**
 * Common Template - tpl_header.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_header.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_header = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version GIT: $Id: Author: Ian Wilson  Tue Aug 14 14:56:11 2012 +0100 Modified in v1.5.1 $
 * @version $Id: Integrated COWOA v2.4  - 2007 - 2013
 */
?>

<?php
  // Display all header alerts via messageStack:
  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }
  if (isset($_GET['error_message']) && zen_not_null($_GET['error_message'])) {
  echo htmlspecialchars(urldecode($_GET['error_message']), ENT_COMPAT, CHARSET, TRUE);
  }
  if (isset($_GET['info_message']) && zen_not_null($_GET['info_message'])) {
   echo htmlspecialchars($_GET['info_message'], ENT_COMPAT, CHARSET, TRUE);
} else {

}
?>


<!--bof-header logo and navigation display-->
<?php
if (!isset($flag_disable_header) || !$flag_disable_header) {
?>

<div id="header-wrapper">
     <div id="header-top">
		 <div id="navMain">
			<ul class="back">
				<li><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">'; ?><?php echo HEADER_TITLE_CATALOG; ?></a></li>
				
				<!-- BOF FAQS LINK deleted by QiaoWei
				<li><a href="<?php echo zen_href_link(FILENAME_FAQS, '', 'NONSSL'); ?>"><?php echo BOX_INFORMATION_FAQS; ?></a></li>
				<!-- EOF FAQS LINK -->
				
				<?php if (($_SESSION['customer_id']) && (!$_SESSION['COWOA']=='True')) { ?>
				<li><a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
				<li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>
				<?php
				} else {
				if (STORE_STATUS == '0') {
				?>
				<li><a href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>
				<?php } } ?>
				
				<!--Dirty hack by QiaoWei-->
				<li><?php echo '<a href="/index.php?main_page=wishlists">'; ?><?php echo Wishlist; ?></a></li>
			</ul>
		 </div>
		
		 <div id="header-topNav">
			 <div id="header-languages"><?php require(DIR_WS_MODULES . 'sideboxes/languages_header.php'); ?></div>
			 <div id="header-currencies"><?php require(DIR_WS_MODULES . 'sideboxes/currencies_header.php'); ?></div>
		 </div>
     </div>
           

     <div id="header-middle">

         <div id="header-logo"><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>'; ?></div>
         <div id="header-middle-right">
               	<div id="header-tagline"><?php echo HEADER_SALES_TEXT;?></div>
               	<div id="header-search-wrapper">
                    <div id="header-search"><?php require(DIR_WS_MODULES . 'sideboxes/search_header.php'); ?></div>
	                <div id="header-cart">
						<div id="header-right-cart">
							<div id="header-right-cart-right">
								<a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'); ?>">
								<cart><?php echo $_SESSION['cart']->count_contents();?> item(s)</cart>
								<br /> <cartTotal><?php echo $currencies->format($_SESSION['cart']->show_total());?></cartTotal>
								</a>
							</div>

							<div id="header-right-cart-left"></div>
						</div>
                    </div>
            	</div>
         </div>
     </div>

	

</div>
<div class="clearBoth"></div>
<?php require($template->get_template_dir('tpl_top_nav.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_top_nav.php'); ?>


<?php } ?>
