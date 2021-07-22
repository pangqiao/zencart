<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_product_info_display.php 19690 2011-10-04 16:41:45Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_faq_info_display.php updated 2012-10-12 to be v1.5 compatible kamelion0927
 */
?>
<div class="centerColumn" id="faqInfo">
<?php if (DEFINE_FAQ_BREADCRUMB_STATUS == '1') { ?>
    <div class="breadCrumb" colspan="2"><?php echo $faq_breadcrumb; ?></div>
<?php } ?>

<!--bof Prev/Next top position -->
<?php if (FAQ_INFO_PREVIOUS_NEXT_STATUS == 1 and (FAQ_INFO_PREVIOUS_NEXT_POSITION == 1 or FAQ_INFO_PREVIOUS_NEXT_POSITION == 3)) { ?>
<div class="faqNextPrevWrapperTop centeredContent">
<?php
/**
 * display the faq previous/next helper
 */
require($template->get_template_dir('/tpl_faq_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_faq_next_previous.php'); ?>
</div>
<?php } ?>
<!--eof Prev/Next top position-->

<h1 id="faqInfoHeading"><?php echo HEADING_TITLE; ?></h1>

<div class="faqQuestionHeader"><?php echo FAQ_QUESTION; ?></div>
<h3 class="faqQuestion"><?php echo $faqs_name; ?></h3>

<div class="faqAnswerHeader"><?php echo FAQ_ANSWER; ?></div>
<div class="faqAnswer"><?php echo stripslashes($faqs_answer); ?></div>

<!--bof Prev/Next top position -->
<?php if (FAQ_INFO_PREVIOUS_NEXT_STATUS == 1 and (FAQ_INFO_PREVIOUS_NEXT_POSITION == 2 or FAQ_INFO_PREVIOUS_NEXT_POSITION == 3)) { ?>
<div class="faqNextPrevWrapperBottom centeredContent">
<?php
/**
 * display the faq previous/next helper
 */
require($template->get_template_dir('/tpl_faqs_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_faqs_next_previous.php'); ?>
</div>
<?php } ?>
<!--eof Prev/Next top position-->

<?php if (FAQ_INFO_BACK_BUTTON == 1) { ?>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>
<?php } ?>
</div>