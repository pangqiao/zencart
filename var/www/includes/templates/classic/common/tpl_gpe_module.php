<?php
/**
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
<?php
	if($current_page_base == 'product_info') {
		if(GPE_MODULE_GPE_SWITCH == 'true') {
			echo '<link rel="stylesheet" type="text/css" href="'.DIR_WS_TEMPLATES.GPE_TEMPLATES_NAME.'/css/gpe.css">'."\n";
			if(GPE_JQUERY_LIBRARY_SWITCH == 'true') {
				if(GPE_JQUERY_LIBRARY_CDN_SWITCH == 'true') {
					if(GPE_JQUERY_LIBRARY_CDN_SOURCE == 'Microsoft') {
						echo '<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>'."\n";
					} else if(GPE_JQUERY_LIBRARY_CDN_SOURCE == 'jQuery') {
						echo '<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>'."\n";
					} else if(GPE_JQUERY_LIBRARY_CDN_SOURCE == 'Google') {
						echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>'."\n";
					} else if(GPE_JQUERY_LIBRARY_CDN_SOURCE == 'Sina') {
						echo '<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>'."\n";
					} echo '<script type="text/javascript" src="'.DIR_WS_TEMPLATES.GPE_TEMPLATES_NAME.'/jscript/jquery/jquery.gpe.library.js"></script>'."\n";
					 require($template->get_template_dir('/gpe.config.php',DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/gpe.config.php');
				} else {
					 echo '<script type="text/javascript" src="'.DIR_WS_TEMPLATES.GPE_TEMPLATES_NAME.'/jscript/jquery/jquery.1.7.pack.js"></script>'."\n".'<script type="text/javascript" src="'.DIR_WS_TEMPLATES.GPE_TEMPLATES_NAME.'/jscript/jquery/jquery.gpe.library.js"></script>'."\n";
					 require($template->get_template_dir('/gpe.config.php',DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/gpe.config.php');
				}
			} else {
					 echo '<script type="text/javascript" src="'.DIR_WS_TEMPLATES.GPE_TEMPLATES_NAME.'/jscript/jquery/jquery.gpe.library.js"></script>'."\n";
					 require($template->get_template_dir('/gpe.config.php',DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/gpe.config.php');
			}
		}
	}
?>