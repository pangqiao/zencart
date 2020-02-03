<?php
define('SEO_CONFIGURATION_GROUP_TITLE', 'Ultimate SEO');
define('SEO_CONFIGURATION_GROUP_DESCRIPTION', 'Options for Ultimate SEO URLs');

define('USU_VERSION_TITLE', SEO_CONFIGURATION_GROUP_TITLE . ' Version');
define('USU_VERSION_DESCRIPTION', 'Installed Version');

define('SEO_ENABLED_TITLE', 'Enable SEO URLs?');
define('SEO_ENABLED_DESCRIPTION', 'This is a global setting and can be used to enable or disable this module completely.');
define('SEO_URL_END_TITLE', 'Rewritten URLs end with');
define('SEO_URL_END_DESCRIPTION', 'If you want your rewritten URLs to end with a certain suffix add one here. Common suffixes are \'.html\', \'.htm\', or leaving this field blank.');
define('SEO_URL_CPATH_TITLE', 'Generate cPath parameters');
define('SEO_URL_CPATH_DESCRIPTION', 'By default Zen Cart generates a cPath parameter for product pages. These are used to keep linked products in the correct category. In automatic mode the cPath is removed only if not needed.');
define('SEO_URL_FORMAT_TITLE', 'Format of rewritten URLs');
define('SEO_URL_FORMAT_DESCRIPTION', 'You can select from a list of commonly generated formats.<br /><b>Original:</b><br /><i>Categories:</i> category-name-c-34<br /><i>Products:</i> product-name-p-54<br /><br /><b>Category Parent:</b><br /><i>Categories:</i> parent-category-name-c-34<br /><i>Products:</i> parent-product-name-p-54');
define('SEO_URL_CATEGORY_DIR_TITLE', 'Categories as directories');
define('SEO_URL_CATEGORY_DIR_DESCRIPTION', 'You can select from a list of commonly generated formats.<br /><b>Off:</b> disables displaying categories as directories<br /><br /><b>Short:</b> use the settings from \'Format of rewritten URLs\'<b>Full:</b> uses full category paths<br /><br />');
define('SEO_URLS_FILTER_PCRE_TITLE', 'Enter PCRE filter rules for generated URLs');
define('SEO_URLS_FILTER_PCRE_DESCRIPTION', 'This setting uses PCRE rules to filter generated urls.<br><br>The format <b>MUST</b> be in the form: <b>find1=>replace1,find2=>replace2</b>. This filter is run before character conversions and stripping of special characters. If you want a dash - in your URLS, use a single space. Also note you must double escape back slashes.');
define('SEO_URLS_FILTER_CHARS_TITLE', 'Enter special character conversions');
define('SEO_URLS_FILTER_CHARS_DESCRIPTION', 'This setting will replace a single byte character with another single byte character.<br><br>The format <b>MUST</b> be in the form: <b>char=>conv,char2=>conv2</b>');
define('SEO_URLS_REMOVE_CHARS_TITLE', 'Remove these characters from URLs');
define('SEO_URLS_REMOVE_CHARS_DESCRIPTION', 'This allows you remove certain problematic characters from the generated URLs.<br /><br /><i>non-alphanumerical:</i> removes all non-alphanumerical characters<br /><i>punctuation:</i> removes all punctuation characters');
define('SEO_URLS_FILTER_SHORT_WORDS_TITLE', 'Filter Short Words');
define('SEO_URLS_FILTER_SHORT_WORDS_DESCRIPTION', 'This setting will filter words less than or equal to the value from the URL.');
define('SEO_URLS_USE_W3C_VALID_TITLE', 'Output W3C valid URLs (parameter string)?');
define('SEO_URLS_USE_W3C_VALID_DESCRIPTION', 'This setting will force the output of W3C valid URLs.');
define('SEO_REWRITE_TYPE_TITLE', 'Choose URL Rewrite Engine');
define('SEO_REWRITE_TYPE_DESCRIPTION', 'Choose which URL Rewrite Engine to use.');
define('SEO_URLS_ONLY_IN_TITLE', 'Enter pages to allow rewrite');
define('SEO_URLS_ONLY_IN_DESCRIPTION', 'You can limit the pages which will be rewritten by specifying them here. If no pages are specified all pages will be rewritten. <br><br>The format is comma delimited and <b>MUST</b> be in the form: <b>page1,page2,page3</b> or <b>page1, page2, page3</b>');
define('SEO_USE_CACHE_GLOBAL_TITLE', 'Enable SEO cache to save queries?');
define('SEO_USE_CACHE_GLOBAL_DESCRIPTION', 'This is a global setting and will turn off caching completely.');
define('SEO_USE_CACHE_PRODUCTS_TITLE', 'Enable product cache?');
define('SEO_USE_CACHE_PRODUCTS_DESCRIPTION', 'This will turn off caching for the products.');
define('SEO_USE_CACHE_CATEGORIES_TITLE', 'Enable categories cache?');
define('SEO_USE_CACHE_CATEGORIES_DESCRIPTION', 'This will turn off caching for the categories.');
define('SEO_USE_CACHE_MANUFACTURERS_TITLE', 'Enable manufacturers cache?');
define('SEO_USE_CACHE_MANUFACTURERS_DESCRIPTION', 'This will turn off caching for the manufacturers.');
define('SEO_USE_CACHE_EZ_PAGES_TITLE', 'Enable ez pages cache?');
define('SEO_USE_CACHE_EZ_PAGES_DESCRIPTION', 'This will turn off caching for ez pages.');
define('SEO_USE_REDIRECT_TITLE', 'Enable automatic redirects?');
define('SEO_USE_REDIRECT_DESCRIPTION', 'This will activate the automatic redirect code and send 301 headers for old to new URLs.');
define('SEO_URLS_CACHE_RESET_TITLE', 'Reset SEO URLs Cache');
define('SEO_URLS_CACHE_RESET_DESCRIPTION', 'This will reset the cache data for SEO');

define('SEO_INSTALL_SUCCESS', SEO_CONFIGURATION_GROUP_TITLE. ' installation completed!');
define('SEO_INSTALL_ERROR', SEO_CONFIGURATION_GROUP_TITLE. ' installation failed!');
define('SEO_UNINSTALL_SUCCESS', SEO_CONFIGURATION_GROUP_TITLE. ' removal completed!');
define('SEO_UNINSTALL_ERROR', SEO_CONFIGURATION_GROUP_TITLE. ' removal failed!');
define('SEO_INSTALL_ERROR_AUTOLOAD', 'The auto-loader file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions');
define('SEO_INSTALL_ERROR_FILE_NOT_FOUND', 'Filesystem Error: Unable to access \'%s\'. Please make sure you uploaded the file and your webserver has access to read the file!');
define('SEO_INSTALL_ERROR_FILE_FOUND', 'The file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions');
define('SEO_INSTALL_ERROR_SORT_ORDER', 'Database Error: Unable to access sort_order in table \'%s\'!');
define('SEO_UNINSTALL_ERROR_DELETE', 'Database Error: Unable to delete \'%s\' in table \'%s\'!');
define('SEO_UNINSTALL_ERROR_TABLE', 'Database Error: Unable to remove table \'%s\'!');
define('SEO_UNINSTALL_ERROR_ADMIN_PAGES', 'Unable to remove the registration of admin pages');

define('SEO_CONFIG_ADJUSTED', 'Due to the setting you have selected for \'%s\'<br />the option for \'%s\' was changed to \'%s\'!');
