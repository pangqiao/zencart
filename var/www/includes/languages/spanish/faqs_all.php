<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faqs_all.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 * 01/10/2013 added spanish language per post #43 in support forum by arsanat
 */
 
define('NAVBAR_TITLE', 'Preguntas');
define('HEADING_TITLE', 'Preguntas Frecuentes');
define('TEXT_NO_FAQS', 'No hay preguntas registradas ahora, por favor regrese dentro de un tiempo.');
define('TEXT_INFO_SORT_BY_FAQS_NAME', 'Nombre de la Pregunta');
define('TEXT_INFO_SORT_BY_FAQS_NAME_DESC', 'Nombre - Descendente');
define('TEXT_INFO_SORT_BY_FAQS_CATEGORY', 'Categoría');
define('TEXT_INFO_SORT_BY_FAQS_DATE_DESC', 'Fecha de creación - de nueva a vieja');
define('TEXT_INFO_SORT_BY_FAQS_DATE', 'Fecha de creación - de vieja a nueva');
define('TEXT_INFO_SORT_BY_FAQS_SORT_ORDER', 'Mostrar por defecto');
define('TEXT_DISPLAY_NUMBER_OF_FAQS_ALL', 'Mostrando de la <strong>%d</strong> a la <strong>%d</strong> (de las <strong>%d</strong> preguntas)');
define('SQL_SHOW_FAQ_INDEX_LISTING',"select configuration_key, configuration_value from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'SHOW_FEATURED_FAQS_INDEX' and configuration_value > 0 order by configuration_value");
define('TABLE_HEADING_FEATURED_FAQS','Las Preguntas Más Frecuentes');
?>