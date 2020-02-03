<?php 
// control multiple wishlist functionality
if(UN_DB_MODULE_WISHLISTS_ENABLED == 'true'){
	define('UN_MODULE_WISHLISTS_ENABLED', true);
} else {
	define('UN_MODULE_WISHLISTS_ENABLED', false);}
if(UN_DB_ALLOW_MULTIPLE_WISHLISTS == 'true'){
	define('UN_ALLOW_MULTIPLE_WISHLISTS', true);
} else {
	define('UN_ALLOW_MULTIPLE_WISHLISTS', false);}
if(UN_DB_DISPLAY_CATEGORY_FILTER == 'true'){
	define('UN_DISPLAY_CATEGORY_FILTER', true);
} else {
	define('UN_DISPLAY_CATEGORY_FILTER', false);}
if(UN_DB_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT == 'true'){
	define('UN_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT', true);
} else {
	define('UN_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT', false);}

// template header
define('UN_HEADER_TITLE_WISHLIST', 'Verlanglijstje');

// wishlist sidebox
define('UN_BOX_HEADING_WISHLIST', 'Verlanglijstje');
define('UN_BUTTON_IMAGE_WISHLIST_ADD', 'wishlist_add.gif');
define('UN_BUTTON_WISHLIST_ADD_ALT', 'Op Verlanglijstje');
define('UN_BOX_WISHLIST_ADD_TEXT', 'Klik om het artikel op het verlanglijstje te zetten.');
define('UN_BOX_WISHLIST_LOGIN_TEXT', '<p><a href="' . zen_href_link(FILENAME_LOGIN, '', 'NONSSL') . '">Meld je aan</a> zodat het artikel op het verlanglijstje gezet kan worden.</p>');

// control form
define('UN_TEXT_SORT', 'Sorteren');
define('UN_TEXT_SHOW', 'Bekijken');
define('UN_TEXT_VIEW', 'Overzicht');
define('UN_TEXT_ALL_CATEGORIES', 'Alle Categorieen');

// more
define('UN_TEXT_ADD_WISHLIST', 'Op verlanglijstje');
define('UN_TEXT_REMOVE_WISHLIST', 'Verwijder van verlanglijstje');
define('UN_BUTTON_IMAGE_SAVE', BUTTON_IMAGE_UPDATE);
define('UN_BUTTON_SAVE_ALT', BUTTON_UPDATE_ALT);
define('UN_TEXT_EMAIL_WISHLIST', 'Email vriend(in) mijn verlanglijstje');
define('UN_TEXT_FIND_WISHLIST', 'Zoek verlanglijstje van vriend(in)');
define('UN_TEXT_NEW_WISHLIST', 'Maak een nieuw verlanglijstje');
define('UN_TEXT_MANAGE_WISHLISTS', 'Beheer mijn verlanglijstjes');
define('UN_TEXT_WISHLIST_MOVE', 'Verplaats artikelen tussen verlanglijstjes');
define('SUCCESS_ADDED_TO_WISHLIST_PRODUCT', 'Het artikel is aan het verlanglijstje toegevoegd ...');

define('UN_TEXT_PRIORITY', 'Prioriteit');
define('UN_TEXT_DATE_ADDED', 'Datum toegevoegd');
define('UN_TEXT_QUANTITY', 'Hoeveelheid');
define('UN_TEXT_COMMENT', 'Commentaar');

define('UN_TEXT_PRIORITY_0', '0 - Deze wil ik niet');
define('UN_TEXT_PRIORITY_1', '1 - Wil ik misschien hebben');
define('UN_TEXT_PRIORITY_2', '2 - Zou ik wel willen hebben');
define('UN_TEXT_PRIORITY_3', '3 - Wil ik graag hebben');
define('UN_TEXT_PRIORITY_4', '4 - Moet ik gewoon hebben');

// product lists
define('UN_TEXT_NO_PRODUCTS', 'Geen artikelen op dit moment.');
define('UN_MAX_DISPLAY_EXTENDED', 10);
define('UN_MAX_DISPLAY_COMPACT', 20);
define('UN_TEXT_COMPACT', 'Compact');
define('UN_TEXT_EXTENDED', 'Uitgebreid');

// general
define('UN_LABEL_DELIMITER', ': ');
define('UN_TEXT_REMOVE', 'Verwijder');
define('UN_EMAIL_SEPARATOR', "-------------------------------------------------------------------------------\n");
define('UN_TEXT_DATE_AVAILABLE', 'Datum beschikbaar: %s');
define('UN_TEXT_FORM_FIELD_REQUIRED', '*');
define('TEXT_OPTION_DIVIDER', '&nbsp;-&nbsp;');

// tables
define('UN_TABLE_HEADING_PRODUCTS', 'Artikel Naam');
define('UN_TABLE_HEADING_PRICE', 'Prijs');
define('UN_TABLE_HEADING_BUY_NOW', 'Winkelwagen');
define('UN_TABLE_HEADING_QUANTITY', 'Hoev.');
define('UN_TABLE_HEADING_WISHLIST', 'Verlanglijstje');
define('UN_TABLE_HEADING_SELECT', 'Selecteer');

//errors
define('UN_ERROR_GET_ID', 'Kan geen verlanglijstje ophalen, id standaard lijstje niet gevonden.');
define('UN_ERROR_GET_CUSTDATA', 'Fout bij het ophalen van de klant gegevens.');
define('UN_ERROR_GET_PERMISSION', 'Onvoldoende rechten om de actie uit te voeren.');
define('UN_ERROR_GET_WISHLIST', 'Kan geen verlanglijstje vinden.');
define('UN_ERROR_GET_WISHLIST_ID', 'Kan geen verlanglijstje vinden: id not set.');
define('UN_ERROR_FIND_WISHLIST', 'Kan geen verlanglijstje vinden.');
define('UN_ERROR_IS_PRIVATE', 'Kan niet vaststellen of verlanglijstje prive is.');
define('UN_ERROR_MAKE_DEFAULT', 'Kan verlanglijstje niet op standaard instellen.');
define('UN_ERROR_MAKE_DEFAULT_ZERO', 'Kan verlanglijstje niet op nul stellen.');
define('UN_ERROR_MAKE_PUBLIC', 'Fout bij het openbaar maken van verlanglijstje.');
define('UN_ERROR_MAKE_PRIVATE', 'Fout bij het prive maken van verlanglijstje.');
define('UN_ERROR_CREATE_DEFAULT', 'Fout bij het maken van verlanglijstje.');
define('UN_ERROR_IN_WISHLIST', 'Kan niet vaststellen of het artikel op verlanglijstje staat.');
define('UN_ERROR_CREATE_WISHLIST', 'Kan geen verlanglijstje maken.');
define('UN_ERROR_ADD_WISHLIST', 'Kan artikel niet toevoegen aan verlanglijstje.');
define('UN_ERROR_EDIT_WISHLIST', 'Kan artikel op verlanglijstje niet bewerken.');
define('UN_ERROR_ADD_PRODUCT_WISHLIST', 'Kan artikel niet toevoegen aan verlanglijstje.');
define('UN_ERROR_DELETE_WISHLIST', 'Kan verlanglijstje niet verwijderen.');
define('UN_ERROR_DELETE_DEFAULT_WISHLIST', 'Kan standaard verlanglijstje niet verwijderen.');
define('UN_ERROR_DELETE_PRODUCT_WISHLIST', 'Kan artikel niet van verlanglijstje af halen.');