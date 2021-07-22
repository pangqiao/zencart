<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_main_product_image.php 18698 2011-05-04 14:50:06Z wilt $
 *
 * @ By KIRA
 * @ QQ: 6171718
 * @ Email: kira@kpa7.net
 * @ Blog: http://zcbk.org/
 *
 * @ Goods pictures show enhanced ~ module
 *
 */
?>
<?php require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE)); ?>
<div  class="left_cp">
	<?php if(GPE_MODULE_GPE_SWITCH == 'true') { ?>
		<?php
			$mainImg = '<a id="jqzoom" title="'.$products_name.'">' . zen_image($products_image_medium, $products_name, 340, 0) . '</a>';//MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT
			if(GPE_SCROLL_VERTICAL == 'true') {
				if(GPE_SCROLL_POSITION == 'left') {
					$mainPosition = 'rightDiv';
					$scrollPosition = 'leftDiv scrollVertical';
					$floatLeft = '';
					$floatRight = '';
				} else {
					$mainPosition = 'leftDiv';
					$scrollPosition = 'leftDiv scrollVertical';
					$floatLeft = '';
					$floatRight = '';
				}
			} else {
					$mainPosition = 'leftDiv';
					$scrollPosition = 'scrollLevel';
					$floatLeft = 'leftDiv';
					$floatRight = 'rightDiv';
			}
		?>
		<?php require(DIR_WS_MODULES . zen_get_module_directory('additional_images.php'));?>
		<div class="imgBox">
			<div id="mainImg" class="<?php echo $mainPosition ;?>">
				<?php echo $mainImg ;?>
			</div>
			<?php if(GPE_SCROLL_VERTICAL == 'false') { ?>
				<div class="clear"></div>
			<?php } ?>
			<div id="smallImg" class="<?php echo $scrollPosition ;?>">
				<?php if ($flag_show_product_info_additional_images != 0 && $num_images > 4) { ?>
					<?php echo '<a id="'.GPE_SCROLL_BUTTON_PREV.'" class="'.$floatLeft.'"></a>' ;?>
					<div id="scrollImg" class="<?php echo $floatLeft ;?>">
						<ul style="margin: 0px; padding: 0px; position: relative; list-style-type: none; z-index: 1; width: 704px; left: 0px;">
							<?php
									if (is_array($list_box_contents) > 0 ) {
										$collen = count($list_box_contents[0]);
										for($row=0;$row<sizeof($list_box_contents);$row++) {
											for($col=0;$col<sizeof($list_box_contents[$row]);$col++) {
												if (isset($list_box_contents[$row][$col]['text'])) {
													echo '<li class="'.$floatLeft.' hover">'.$list_box_contents[$row][$col]['text'].'</li>';
												}
											}
										}
									}	echo '<li class="'.$floatLeft.' hover">'.$mainImg.'</li>' ;
							?>
						</ul>
					</div>
					<?php echo '<a id="'.GPE_SCROLL_BUTTON_NEXT.'" class="'.$floatRight.'"  onselectstart="return false;" ></a>' ;?>
				<?php } else if ($num_images == 0){ } else { ?>
					<div id="scrollImg" class="noButton leftDiv">
						<ul style="margin: 0px; padding: 0px; position: relative; list-style-type: none; z-index: 1; width: 704px; left: 0px;">
							<?php
									if (is_array($list_box_contents) > 0 ) {
										$collen = count($list_box_contents[0]);
										for($row=0;$row<sizeof($list_box_contents);$row++) {
											for($col=0;$col<sizeof($list_box_contents[$row]);$col++) {
												if (isset($list_box_contents[$row][$col]['text'])) {
													echo '<li class="'.$floatLeft.' hover">'.$list_box_contents[$row][$col]['text'].'</li>';
												}
											}
										}
									} echo '<li class="'.$floatLeft.' hover">'.$mainImg.'</li>' ;
							?>
						</ul>
					</div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	<?php } else { ?>
			<script language="javascript" type="text/javascript"><!--
			document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . zen_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $_GET['products_id']) . '\\\')">' . zen_image(addslashes($products_image_medium), addslashes($products_name), MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . TEXT_CLICK_TO_ENLARGE . '</span></a>'; ?>');
			//--></script>
			<noscript>
			<?php
				echo '<a href="' . zen_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $_GET['products_id']) . '" target="_blank">' . zen_image($products_image_medium, $products_name, MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT) . '<br /><span class="imgLink">' . TEXT_CLICK_TO_ENLARGE . '</span></a>';
			?>
			</noscript>
	<?php } ?>
	
	<br class="clearBoth" />
	
	<!--eof Product askQuestiont qiaowei -->
	<div id= "inforBar">
		<div id="askQuestion" >
		   <?php echo '<a href="' . zen_href_link(FILENAME_ASK_A_QUESTION, 'products_id='
		   . $_GET['products_id']) . '">' .
		   zen_image_button('button_ask_question.png', BUTTON_ASK_A_QUESTION_ALT) .
		   '</a>'; ?>
		</div>

		<!--bof Wishlist button qiaowei-->
		<?php if (UN_MODULE_WISHLISTS_ENABLED) { ?>
		<div id="productWishlistLink">
			<?php echo zen_image_submit(UN_BUTTON_IMAGE_WISHLIST_ADD, UN_BUTTON_WISHLIST_ADD_ALT, 'name="wishlist" value="yes"'); 
			//print_r($_REQUEST);
			?></div>

			<?php }
			else
			{}
			?>
		</div>
		<!--eof Wishlist button -->
		
	<br class="clearBoth" />
	
	</div>
	
