<?php
/** MOD Product Listing Sorter - spanish
*\includes\languages\spanish\extra_definitions\product_listing_sorter_en.php
* +----------------------------------------------------------------------+
* | This source file is subject to version 2.0 of the GPL license,       |
* | that is bundled with this package in the file LICENSE, and is        |
* | available through the world-wide-web at the following url:           |
* | http://www.zen-cart.com/license/2_0.txt.                             |
* +----------------------------------------------------------------------+
*
*/ 

define('PRODUCT_LISTING_SORTER_LIST', '
Nombre (asc):pd.products_name;
Nombre (desc):pd.products_name DESC;
Modelo (asc):p.products_model;
Modelo (desc):p.products_model DESC;
Precio (low to high):p.products_price_sorter;
Precio (high to low):p.products_price_sorter DESC;
Fabricante:m.manufacturers_name;
Último:p.products_date_added DESC;
Más Vendido: p.products_ordered DESC
');

define('TEXT_PRODUCT_LISTING_SORTER', 'Ordenar por:');//label for drop-down
define('TEXT_PRODUCT_LISTING_SORTER_NAMES', 'Elige..');//first item in drop-down when no sorting options selected
define('TEXT_PRODUCT_LISTING_SORTER_NAMES_RESET', '-Reseteo-');//first item in drop-down when a sorting option is already in use


