<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_products_quantity_discounts.php 3291 2006-03-28 04:03:38Z ajeh $
 */

?>
<div id="productQuantityDiscounts">
<?php
  if ($zc_hidden_discounts_on) {
?>
  <table border="1" cellspacing="2" cellpadding="2">
    <tr>
      <td colspan="1" align="center">
      <?php echo TEXT_HEADER_DISCOUNTS_OFF; ?>
      </td>
    </tr>
    <tr>
      <td colspan="1" align="center">
      <?php echo $zc_hidden_discounts_text; ?>
      </td>
    </tr>
  </table>
<?php } else { ?>
<div>
<h4 class="wholesaleprice"><?php echo 'Wholesale Price' ?></h4>

  <table  id="discount_info">

	<tr>
      <th align="center">Qty.Range(unit)</th>
      <th align="center">Price(per unit)</th>
      <th align="center">Discount</th>
    </tr>
    <tr>
      <td align="center"><?php echo $show_qty; ?></td>
	  <td><?php echo $currencies->display_price($show_price, zen_get_tax_rate($products_tax_class_id)); ?></td>
	  <td></td>
	</tr>

<?php
  foreach($quantityDiscounts as $key=>$quantityDiscount) {
  echo '<tr>';
		if($products_discount_type==1){
			$discounts=(100-$quantityDiscount['discounted_price']/$show_price*100).'%';
		}else{
			$discounts=$currencies->display_price(($show_price-$quantityDiscount['discounted_price']), zen_get_tax_rate($products_tax_class_id)); 
		}
?>
	<td align="center"><?php echo $quantityDiscount['show_qty'] ;?></td>
	<td><?php echo $currencies->display_price($quantityDiscount['discounted_price'], zen_get_tax_rate($products_tax_class_id)); ?></td>
	<td><?php echo $discounts; ?></td>
<?php
    $disc_cnt++;
    if ($discount_col_cnt == $disc_cnt && !($key == sizeof($quantityDiscount))) {
      $disc_cnt=0;
?>

<?php
    }
	echo '<tr>';
  }
?>
</tr>
<?php
  if ($disc_cnt < $columnCount) {
?>
    <td align="center" colspan="3"> &nbsp; </td>
<?php } ?>
    </tr>
<?php
  if (zen_has_product_attributes($products_id_current)) {
?>
    <tr>
      <td colspan="<?php echo $columnCount+1; ?>" align="center">
        <?php echo TEXT_FOOTER_DISCOUNT_QUANTITIES; ?>
      </td>
    </tr>
<?php } ?>
  </table>
  </div>
<?php } // hide discounts ?>
</div>
