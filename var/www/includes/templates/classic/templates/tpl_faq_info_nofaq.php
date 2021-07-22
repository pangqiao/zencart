<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: tpl_product_info_noproduct.php 2578 2005-12-15 19:31:34Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @tpl_faq_info_nofaq.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
?>
<div class="centerColumn" id="faqInfoNoProduct">
<div id="faqInfoNoFaqMainContent" class="content"><?php echo TEXT_FAQ_NOT_FOUND; ?></div>
<div class="buttonRow back"><?php zen_back_link() . zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>'; ?></div>
</div>