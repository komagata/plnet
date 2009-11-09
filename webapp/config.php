<?php
require_once dirname(__FILE__).'/lib/Utils.php';
define('MOJAVI_ENV', Utils::env());
$conf = Utils::conf();
define('PEAR_DIR', $conf['pear_dir']);

/**
 * include_path setting.
 */
ini_set("include_path", ini_get("include_path")
    .PATH_SEPARATOR . PEAR_DIR
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/'
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/smarty/'
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/simplepie/'
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/simpletest/'
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/tagcloud/'
    .PATH_SEPARATOR . dirname(dirname(__FILE__)).'/lib/mixi/'
    .PATH_SEPARATOR . dirname(__FILE__).'/lib/'
    .PATH_SEPARATOR . dirname(__FILE__).'/tests/unit/'
);

/**
* An absolute file-system path to the webapp directory.
*/
define('BASE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
* An absolute file-system path to the log directory.
*
* Note: This directory must be writable by any user.
*/
define('LOG_DIR', BASE_DIR . 'logs/');

/**
* An absolute file-system path to the all-in-one class file Mojavi
* uses.
*/
define('MOJAVI_FILE', dirname(dirname(__FILE__)) . '/mojavi/mojavi-all-classes.php');

/**
* An absolute file-system path to the optional classes directory.
*/
define('OPT_DIR', dirname(dirname(__FILE__)) . '/mojavi/opt/');
// ----- WEB DIRECTORIES AND PATHS -----


/**
* An absolute web path where modules can store public information such as
* images and CSS documents.
*/
define('WEB_MODULE_DIR', '/modpub/');

/**
* An absolute web path to the index.php script.
*/
define('SCRIPT_PATH', $conf['root_url']);


// ----- ACCESSOR NAMES -----

/**
* The parameter name used to specify a module.
*/
define('MODULE_ACCESSOR', 'm');

/**
* The parameter name used to specify an action.
*/
define('ACTION_ACCESSOR', 'a');


// ----- MODULES AND ACTIONS -----


/**
* The action to be executed when an unauthenticated user makes a request for
* a secure action.
*/
define('AUTH_MODULE', 'Auth');
define('AUTH_ACTION', 'Login');

/**
* The action to be executed when a request is made that does not specify a
* module and action.
*/
define('DEFAULT_MODULE', 'Top');
define('DEFAULT_ACTION', 'Index');

/**
* The action to be executed when a request is made for a non-existent module
* or action.
*/
define('ERROR_404_MODULE', 'Default');
define('ERROR_404_ACTION', 'PageNotFound');

/**
* The action to be executed when an authenticated user makes a request for
* an action for which they do not possess the privilege.
*/
define('SECURE_MODULE', 'Auth');
define('SECURE_ACTION', 'Login');

/**
* The action to be executed when the available status of the application
* is unavailable.
*/
define('UNAVAILABLE_MODULE', 'Default');
define('UNAVAILABLE_ACTION', 'Unavailable');


// ----- MISC. SETTINGS -----


/**
* Whether or not the web application is available or if it's out-of-service
* for any reason.
*/
define('AVAILABLE', TRUE);

/**
* Should typical PHP errors be displayed? This should be used only for
* development purposes.
*
* 1 = on, 0 = off
*/
define('DISPLAY_ERRORS', 0);

/**
* The associative array that may contain a key that holds path information
* for a request, and the key name.
*
* 1 = $_SERVER array
* 2 = $_ENV array
*
* Note: This only needs set if URL_FORMAT = 2.
*/
define('PATH_INFO_ARRAY', 1);
define('PATH_INFO_KEY',   'PATH_INFO');

/**
* The format in which URLs are generated.
*
* 1 = GET format
* 2 = PATH format
*
* GET  format is ?key=value&key=value
* PATH format is /key/value/key/value
*
* Note: PATH format may required modifications to your webserver configuration.
*/
define('URL_FORMAT', 1);

/**
* Should we use sessions?
*/
define('USE_SESSIONS', TRUE);

// misc directories
define('ROOT_DIR', dirname(BASE_DIR) . '/');
define('DOC_ROOT_DIR', ROOT_DIR . 'www/');
define('BIN_DIR', ROOT_DIR . 'bin/');
define('CACHE_DIR', ROOT_DIR . 'cache/');
define('DB_BACKUP_DIR', ROOT_DIR . 'backup/');
define('BASE_LIB_DIR', ROOT_DIR . 'lib/');
define('TESTS_DIR', BASE_DIR . 'tests/');
define('FIXTURES_DIR', TESTS_DIR . 'fixtures/');
define('TESTUNIT_DIR', TESTS_DIR . 'unit/');

// mb_string
mb_internal_encoding('UTF-8');

// language
mb_language('Japanese');

// compatibility
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('file_get_contents');
PHP_Compat::loadFunction('file_put_contents');
require_once BASE_DIR.'lib/DATE.php';

// session lifetime
/*
$session_lifetime = 60 * 60 * 24 * 7;
ini_set('session.cookie_lifetime', $session_lifetime);
ini_set('session.gc_maxlifetime', $session_lifetime);
*/

// autoload
ini_set('unserialize_callback_func', 'autoload');
function autoload() {
    include_once BASE_DIR . 'lib/dataobject/Member.php';
}

// locale
define('LOCALE_DETECT', true);
define('DEFAULT_LOCALE', 'en');

// smarty
define('SMARTY_CACHE_DIR', ROOT_DIR.'cache/smarty');
define('SMARTY_CACHING', false);
define('SMARTY_CACHE_LIFETIME', 3600);
define('SMARTY_FORCE_COMPILE', false);
define('SMARTY_COMPILE_DIR', BASE_DIR.'templates_c');
define('SMARTY_DEBUGGING_CTRL', 'NONE');
define('SMARTY_DEBUGGING', false);

// dbo
$dbo_options['database'] = $conf['dsn'];
$dbo_options['schema_location'] = BASE_DIR.'configs';
$dbo_options['class_location'] = BASE_DIR.'lib/dataobject';
$dbo_options['require_prefix'] = BASE_DIR.'lib/dataobject';
$dbo_options['class_prefix'] = '';
$dbo_options['quote_identifiers'] = true;
$dbo_options['debug'] = 0;

// cache lite
define('CACHE_LITE_DIR', ROOT_DIR.'cache/lite/');
define('CACHE_LITE_AUTO_CLEANING', 128);

// feed number
define('PLNET_FEED_NUMBER', 32);

// default member data
define('PLNET_DEFAULT_DESIGN_ID', 2);

// mail
define('PLNET_ERROR_MAIL_TO', 'komagata@gmail.com,kawadu@gmail.com');

// simplepie
define('SIMPLEPIE_CACHE_DIR', CACHE_DIR.'simplepie');

// mixi
define('MIXI_CACHE_DIR', CACHE_DIR.'mixi/');

// config
define('GLU_NS', 'jp.plnet');
define('PLNET_ENTRIES_PER_PAGE', 10);


define('PLNET_LOGIN_LIFETIME', 31536000); // one year
define('DEV_FEED_URI', 'http://plnet.jp/plnet-dev/rss');
define('PLNET_CUSTOM_TEMPLATE_ID', 65535);
define('PLNET_DEFAULT_CSS', DOC_ROOT_DIR . 'styles/simple.css');
define('PLNET_OTHER_CATEGORY_ID', 8);

// sitemap
define('PLNET_SITEMAP_TOP_FREQ', 'daily');
define('PLNET_SITEMAP_TOP_PRIORITY', 0.8);
define('PLNET_SITEMAP_INDIVIDUAL_FREQ', 'never');
define('PLNET_SITEMAP_INDIVIDUAL_PRIORITY', 0.6);
define('PLNET_SITEMAP_ARCHIVE_FREQ', 'never');
define('PLNET_SITEMAP_ARCHIVE_PRIORITY', 0.4);
define('PLNET_SITEMAP_TAG_FREQ', 'never');
define('PLNET_SITEMAP_TAG_PRIORITY', 0.4);
define('PLNET_SITEMAP_SOURCE_FREQ', 'never');
define('PLNET_SITEMAP_SOURCE_PRIORITY', 0.4);

// resource
define('PLNET_DEFAULT_PHOTO', 'images/profile_icon.jpg');

// member register
$account_deny = array('login', 'logout', 'setting', 'register', 'php', 'tos', 'feedback', 'list', '404.html', '503.html', 'tags', 'tag', 'services', 'developer', 'cacti', 'mixi_dialy', 'mixi_profile');

// DB_DataObject initialize
require_once BASE_DIR.'lib/DBUtils.php';
DBUtils::initialize();
?>
