<?php
/**
 * Common Template - tpl_footer.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_footer.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_footer = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_footer.php 4821 2006-10-23 10:54:15Z drbyte $
 */
require(DIR_WS_MODULES . zen_get_module_directory('footer.php'));
?>

<?php
if (!isset($flag_disable_footer) || !$flag_disable_footer) {
?>

<div id="footer">

	<!--bof-navigation display -->
	<div id="navSuppWrapper">
		<!--BOF footer menu display-->
		<?php require($template->get_template_dir('tpl_footer_menu.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_footer_menu.php');?>
		<!--EOF footer menu display-->
	</div>
	

</div>

	<!-- bof productTags-->
	<div style="width:100%; word-spacing:5px; margin:10px 0;text-align:center">Product Tags:
		<?php

		// display productTagList
		foreach(range('a', 'z') as $letter) {
		echo '<a target="_top" href="' .
		HTTP_SERVER.DIR_WS_CATALOG.'producttags/'.$letter.'/" >'.strtoupper
		($letter).'</a> ';
		}
		echo '<a target="_top" href="' .
		HTTP_SERVER.DIR_WS_CATALOG.'producttags/0-9/" >0-9</a> ';
		?>
	</div>

	<!-- eof productTags-->
	<!--eof-navigation display -->
	<!--bof- site copyright display -->
	<div id="siteinfoLegal" class="legalCopyright"><?php echo FOOTER_TEXT_BODY; ?></div>
	<!--eof- site copyright display -->

	<!--bof-ip address display -->
	<?php
	if (SHOW_FOOTER_IP == '1') {
	?>
	<div id="siteinfoIP"><?php echo TEXT_YOUR_IP_ADDRESS . '  ' . $_SERVER['REMOTE_ADDR']; ?></div>
	<?php
	}
	?>
	<!--eof-ip address display -->

	<!--bof-banner #5 display -->
	<?php
	  if (SHOW_BANNERS_GROUP_SET5 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET5)) {
	    if ($banner->RecordCount() > 0) {
	?>
	<div id="bannerFive" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
	<?php
	    }
	  }
	?>
	<!--eof-banner #5 display -->

	<?php
	} // flag_disable_footer
	?>
	
	<script type="text/javascript" src="includes/templates/classic/jscript/BackToTop.jquery.js"></script>
	<script type="text/javascript" src="includes/templates/classic/jscript/jquery-ui.js"></script>

	<a id="BackToTop" style="display: none;" href="#body"><span></span></a>
	
	    <script type="text/javascript">
		$(document).ready(function(){
			BackToTop({
				autoShow : true,
				appearMethod : 'fade',
				timeEffect : 500,
				effectScroll :  'linear',
				autoShowOffset :  '0',
				opacity :  1,
				top :  10			});
		});
	</script>