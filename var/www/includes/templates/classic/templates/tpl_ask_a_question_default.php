<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// |  Ask a question page.  Note that this uses the email settings from   |
// |  the contact_us page; there are no separate settings.                | 
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//
?>
<div class="centerColumn" id="askAQuestion">
<?php echo zen_draw_form('ask_a_question', zen_href_link(FILENAME_ASK_A_QUESTION, 'action=send&products_id=' . $_GET['products_id'])); ?>

<h1><?php echo HEADING_TITLE . $product_info->fields['products_name']; ?></h1>


<?php
  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?>
<div class="mainContent success"><?php echo TEXT_SUCCESS; ?></div>

<div class="buttonRow"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

<?php
  } else {
?>

<?php echo '<a href="' . zen_href_link(zen_get_info_page($_GET['products_id']), 'products_id=' . $_GET['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $product_info->fields['products_image'], $product_info->fields['products_name'], IMAGE_PRODUCT_LISTING_WIDTH, IMAGE_PRODUCT_LISTING_HEIGHT) . '</a>'; ?>

<div id="contactUsNoticeContent" class="content">
<?php
/**
 * require html_define for the contact_us page.  
 */
  require($define_page);
?>
</div>

<?php if ($messageStack->size('contact') > 0) echo $messageStack->output('contact'); ?>

<fieldset id="contactUsForm">
<legend><?php echo FORM_TITLE; ?></legend>
<div class="alert forward"><?php echo FORM_REQUIRED_INFORMATION; ?></div>
<br class="clearBoth" />

<?php
// show dropdown if set
    if (CONTACT_US_LIST !=''){
?>
<label class="inputLabel" for="send-to"><?php echo SEND_TO_TEXT; ?></label>
<?php echo zen_draw_pull_down_menu('send_to',  $send_to_array, 'id=\"send-to\"') . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?>
<br class="clearBoth" />
<?php
    }
?>

<label class="inputLabel" for="contactname"><?php echo ENTRY_NAME; ?></label>
<?php echo zen_draw_input_field('contactname', $name, ' size="40" id="contactname"') . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?>
<br class="clearBoth" />

<label class="inputLabel" for="email-address"><?php echo ENTRY_EMAIL; ?></label>
<?php echo zen_draw_input_field('email', ($error ? $_POST['email'] : $email), ' size="40" id="email-address"') . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?>
<br class="clearBoth" />

<label for="enquiry"><?php echo ENTRY_ENQUIRY . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?></label>
<?php echo zen_draw_textarea_field('enquiry', '30', '7', '', 'id="enquiry"'); ?>

</fieldset>

<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_SEND, BUTTON_SEND_ALT); ?></div>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>
<?php
  }
?>
</form>
</div>

