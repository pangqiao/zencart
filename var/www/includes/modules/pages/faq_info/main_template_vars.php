<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: main_template_vars.php 19690 2011-10-04 16:41:45Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @main_template_vars.php for faq info updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
/*
 * Extracts and constructs the data to be used on the faq info page
 */
  $module_show_faq_categories = FAQ_INFO_FAQ_CATEGORIES;
  $sql = "select count(*) as total
          from " . TABLE_FAQS . " p, " .
                   TABLE_FAQS_DESCRIPTION . " pd
          where    p.faqs_status = '1'
          and      p.faqs_id = '" . (int)$_GET['faqs_id'] . "'
          and      pd.faqs_id = p.faqs_id
          and      pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";


  $res = $db->Execute($sql);

  if ( $res->fields['total'] < 1 ) {
    $tpl_page_body = '/tpl_faq_info_nofaq.php';
  } else {
    $tpl_page_body = '/tpl_faq_info_display.php';
    $sql = "update " . TABLE_FAQS_DESCRIPTION . "
            set        faqs_viewed = faqs_viewed+1
            where      faqs_id = '" . (int)$_GET['faqs_id'] . "'
            and        language_id = '" . (int)$_SESSION['languages_id'] . "'";
    $res = $db->Execute($sql);
    $sql = "select p.*, pd.*
	       from   " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd
           where  p.faqs_status = '1'
           and    p.faqs_id = '" . (int)$_GET['faqs_id'] . "'
           and    pd.faqs_id = p.faqs_id
           and    pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";
    $faq_info = $db->Execute($sql);
  }

  require(DIR_WS_MODULES . zen_get_module_directory('faq_prev_next.php'));

  $faqs_name = $faq_info->fields['faqs_name'];
  $faqs_answer = $faq_info->fields['faqs_answer'];

  $faqs_id_current = (int)$_GET['faqs_id'];
  if (is_dir(DIR_WS_TEMPLATE . $current_page_base . '/extra_main_template_vars')) {
    if ($za_dir = @dir(DIR_WS_TEMPLATE . $current_page_base. '/extra_main_template_vars')) {
      while ($zv_file = $za_dir->read()) {
        if (strstr($zv_file, '*.php') ) {
          require(DIR_WS_TEMPLATE . $current_page_base . '/extra_main_template_vars/' . $zv_file);
        }
      }
    }
  }

  require($template->get_template_dir($tpl_page_body,DIR_WS_TEMPLATE, $current_page_base,'templates'). $tpl_page_body);
