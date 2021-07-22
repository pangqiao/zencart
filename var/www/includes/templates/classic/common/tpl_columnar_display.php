<?php
/**
 * Common Template - tpl_columnar_display.php
 *
 * This file is used for generating tabular output where needed, based on the supplied array of table-cell contents.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_columnar_display.php 3157 2006-03-10 23:24:22Z drbyte $
 */

?>
<?php
  if ($title) {
  ?>
<?php echo $title; ?>
<?php
 }
 ?>

<div id="layer_switch" class="index_layer right_big_con margin_t relative" style="width:834px;">
<div id="BestDeal" class="show">
<ul style="list-style-type:none">


<?php
if (is_array($list_box_contents) > 0 ) {

$MaxCol=0;
 for($row=0;$row<sizeof($list_box_contents);$row++) {
    $params = "";
    //if (isset($list_box_contents[$row]['params'])) $params .= ' ' . $list_box_contents[$row]['params'];
?>

<?php
    for($col=0;$col<sizeof($list_box_contents[$row]);$col++) {
      $r_params = "";
      if (isset($list_box_contents[$row][$col]['params'])) $r_params .= ' ' . (string)$list_box_contents[$row][$col]['params'];
	 $r_params = str_replace(" back", "", $r_params);
     $r_params = str_replace("50", "100", $r_params);
     $r_params = str_replace("33", "100", $r_params);
	 
	 // bof: Replace all widths with 100% for Column Divider Pro
	 $startpos = strpos($r_params, "width:");
	 if ($startpos != false) {
		$endpos = strpos($r_params, ";", $startpos);
		$res = substr($r_params, $startpos, ($endpos - $startpos ));
		$r_params = str_replace($res, "width:100%", $r_params);
	 }
	 // eof: Replace all widths with 100% for Column Divider Pro
	 
	 if (isset($list_box_contents[$row][$col]['text'])) {
?>
    <?php 
	echo '<li >'. $list_box_contents[$row][$col]['text'] .  '</li>' . "\n";
     ?>
    <?php if ($row == 0 && $col < (sizeof($list_box_contents[$row]) - 1)) {
            
          	$MaxCol += 1;
          }

      }
    }
?>


<br class="clearBoth" />

<?php
  }
}
?>
</ul>
 </div>
 </div>