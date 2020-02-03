<?php
/**
 * Page Template - tpl_ezpages_footer_menu.php
 *
 * Displays EZ-Pages footer content.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_ezpages_footer_menu.php 1.0 5/9/2009 Clyde Jones $
 */

/**
 * require code to show EZ-Pages list
*/
  include(DIR_WS_MODULES . zen_get_module_directory('ezpages_bar_footer.php'));
?>
<?php if (sizeof($var_linksList) >= 1) { ?>
<?php for ($i=1, $n=sizeof($var_linksList); $i<=$n; $i++) {  ?>
  <li><a href="<?php echo $var_linksList[$i]['link']; ?>"><?php echo $var_linksList[$i]['name']; ?></a></li>
<?php } // end FOR loop ?>
<?php } ?>