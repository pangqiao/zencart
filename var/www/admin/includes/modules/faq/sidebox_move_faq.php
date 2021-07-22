<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: sidebox_move_product.php 3009 2006-02-11 15:41:10Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @sidebox_move_faq.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_FAQ . '</b>');
        $type_faq_admin_handler = faq;
        $contents = array('form' => zen_draw_form('faqs', $type_faq_admin_handler, 'action=move_faq_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $pInfo->faqs_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_FAQS_INTRO, $pInfo->faqs_name));
        $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_FAQ_CATEGORIES . '<br /><b>' . zen_output_generated_faq_category_path($pInfo->faqs_id, 'faq') . '</b>');
        $contents[] = array('text' => '<br />' . sprintf(TEXT_MOVE, $pInfo->faqs_name) . '<br />' . zen_draw_pull_down_menu('move_to_faq_category_id', zen_get_faq_category_tree(), $current_faq_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $pInfo->faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
?>