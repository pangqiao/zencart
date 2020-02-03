<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: product_reviews_write.php 3159 2006-03-11 01:35:04Z drbyte $
 */

define('NAVBAR_TITLE', 'Reviews');

define('SUB_TITLE_FROM', 'Written by: ');
if( $_SESSION['customer_id'] ) {
  define('SUB_TITLE_REVIEW', 'For your privacy, your name will be displayed as <b>%s %1.1s.</b> when the review is posted.');
} else {
  define('SUB_TITLE_REVIEW', 'Your name will be displayed as the text you enter in <em>Reviewer\'s Name</em>. For your privacy, we suggest <b>not</b> using your full name.');
}
define('SUB_TITLE_RATING', 'Choose a ranking for this item. 1 star is the worst and 5 stars is the best.');

define('TEXT_NOTES', 'Notes:');
define('TEXT_NO_HTML', 'HTML tags are not supported in your review\'s text.');
define('TEXT_BAD', 'Worst');
define('TEXT_GOOD', 'Best');
define('TEXT_PRODUCT_INFO', '');

define('TEXT_APPROVAL_REQUIRED', 'Reviews require prior approval before they will be displayed');

define('EMAIL_REVIEW_PENDING_SUBJECT','Product Review Pending Approval: %s');
define('EMAIL_PRODUCT_REVIEW_CONTENT_INTRO','A Product Review for %s has been submitted and requires your approval.'."\n\n");
define('EMAIL_PRODUCT_REVIEW_CONTENT_DETAILS','Review Details: %s');

define('MESSAGE_REVIEW_SUBMITTED', 'Your review has been submitted.');
define('MESSAGE_REVIEW_SUBMITTED_APPROVAL', 'Your review has been submitted for approval.');
define('MESSAGE_REVIEW_WRITE_NEEDS_LOGIN', 'You need to sign into your account to write a review.');
define('MESSAGE_REVIEW_TEXT_MIN_LENGTH', 'Add a few more words to your review text. A review needs to have at least ' . REVIEW_TEXT_MIN_LENGTH . ' characters.');

define('JS_REVIEW_NAME', 'Your Name needs to have at least ' . REVIEW_NAME_MIN_LENGTH . ' characters.');
define('TEXT_REVIEW_NAME', 'Your Name:');
define('TEXT_REVIEW_TEXT', 'Tell us what you think and share your opinions with others; please focus your comments on the product.');

?>