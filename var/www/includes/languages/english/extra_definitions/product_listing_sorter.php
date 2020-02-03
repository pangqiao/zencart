<?php
/** MOD Product Listing Sorter
*\includes\languages\english\extra_definitions\product_listing_sorter_en.php
* +----------------------------------------------------------------------+
* | This source file is subject to version 2.0 of the GPL license,       |
* | that is bundled with this package in the file LICENSE, and is        |
* | available through the world-wide-web at the following url:           |
* | http://www.zen-cart.com/license/2_0.txt.                             |
* +----------------------------------------------------------------------+
*
*/ 

define('PRODUCT_LISTING_SORTER_LIST', '
Name (asc):pd.products_name;
Name (desc):pd.products_name DESC;
Model (asc):p.products_model;
Model (desc):p.products_model DESC;
Price (low to high):p.products_price_sorter;
Price (high to low):p.products_price_sorter DESC;
Manufacturer:m.manufacturers_name;
Newest:p.products_date_added DESC;
Most Popular: p.products_ordered DESC
');

define('TEXT_PRODUCT_LISTING_SORTER', 'Sort by: ');//label for drop-down
define('TEXT_PRODUCT_LISTING_SORTER_NAMES', 'Price...');//first item in drop-down when no sorting options selected
define('TEXT_PRODUCT_LISTING_SORTER_NAMES_RESET', '-Reset-');//first item in drop-down when a sorting option is already in use


