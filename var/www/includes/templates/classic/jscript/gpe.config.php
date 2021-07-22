<?php
/**
 *
 * @ By KIRA
 * @ QQ: 6171718
 * @ Email: kira@kpa7.net
 * @ Blog: http://zcbk.org/
 *
 * @ Goods pictures show enhanced ~ Configuration
 *
 */
?>
<?php 

echo '<script type="text/javascript">'."\n".'<!--'."\n".'$(function(){

$("#smallImg img").mouseover(function()
	{
		$("#mainImg img").attr("src",$(this).attr("src"))
	}
);
$("#smallImg a").mouseover(
	function()
	{
		$("#jqzoom").attr("href",$(this).attr("href"))
	}
);

	
$("#scrollImg").jCarouselLite(
	{btnNext:"#'.GPE_SCROLL_BUTTON_NEXT.'",
	btnPrev:"#'.GPE_SCROLL_BUTTON_PREV.'",
	circular:'.GPE_SCROLL_CIRCULAR.',
	vertical:'.GPE_SCROLL_VERTICAL.',
	visible:'. GPE_SCROLL_VISIBLE.',
	speed:'.GPE_SCROLL_SPEED.',
	scroll:'. GPE_SCROLL_QTY.',
	start:'. GPE_SCROLL_START.'});
$("#jqzoom").jqzoom(
	{position:"'.GPE_ZOOM_POSITION.'",
	showEffect:"'.GPE_ZOOM_SHOWEFFECT.'",
	hideEffect:"'.GPE_ZOOM_HIDEEFFECT.'",
	fadeinSpeed:"'.GPE_ZOOM_FADEINSPEED.'",
	fadeoutSpeed:"'.GPE_ZOOM_FADEOUTSPEED.'",
	preloadPosition:"'.GPE_ZOOM_PRELOADPOSITION.'",
	zoomType:"'.GPE_ZOOM_TYPE.'",
	preloadText:"'.GPE_ZOOM_PRELOAD_TEXT.'",
	showPreload:'.GPE_ZOOM_SHOW_PRELOAD.',
	title:'.GPE_ZOOM_TITLE.',
	lens:'.GPE_ZOOM_LENS.',
	imageOpacity:'.GPE_ZOOM_OPACITY.',
	zoomWidth:'.GPE_ZOOM_WIDTH.',
	zoomHeight:'.GPE_ZOOM_HEIGHT.',
	xOffset:'.GPE_ZOOM_X_OFFSET.',
	yOffset:'.GPE_ZOOM_Y_OFFSET.'});
/*	
$("#mainImg a,#scrollImg a").lightBox(
	{overlayBgColor:"'.GPE_LIGHTBOX_BG_COLOR.'",
	txtImage:"'.GPE_LIGHTBOX_TXT_IMAGE.'",
	txtOf:"'.GPE_LIGHTBOX_TXT_OF.'",
	imageLoading:\''.GPE_LIGHTBOX_IMAGE_LOADING.'\',
	imageBtnPrev:\''.GPE_LIGHTBOX_BUTTON_PREV.'\',
	imageBtnNext:\''.GPE_LIGHTBOX_BUTTON_NEXT.'\',
	imageBtnClose:\''.GPE_LIGHTBOX_BUTTON_CLOSE.'\',
	imageBlank:\''.GPE_LIGHTBOX_BLANK.'\',
	keyToClose:"'.GPE_LIGHTBOX_QUICK_CLOSE.'",
	keyToPrev:"'.GPE_LIGHTBOX_QUICK_PREV.'",
	keyToNext:"'.GPE_LIGHTBOX_QUICK_NEXT.'",
	overlayOpacity:'.GPE_LIGHTBOX_OPACITY.',
	containerBorderSize:'.GPE_LIGHTBOX_BORDER_SIZE.',
	containerResizeSpeed:'.GPE_LIGHTBOX_SPEED.',
	fixedNavigation:'.GPE_LIGHTBOX_NAVIGATION.'});
*/

});'."\n".'//-->'."\n".'</script>';

?>