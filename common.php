<?PHP

define("ROOT", dirname(__FILE__));
define("ROOT_DATA", ROOT . "/data");
define("ROOT_CONFIG", ROOT . "/config");
define("ROOT_THEMES", ROOT . "/themes");
define('INCLUDES_DIR', ROOT . "/includes");
define("LANGUAGE_DIR", ROOT . '/languages');
define('PLUGINS_DIR', ROOT . "/framework/plugins");
require_once(ROOT . "/framework/SlightPHP.php");

function __autoload($class) {
    if ('Model' == substr($class, 0, 5) || 'Service' == substr($class, 0, 7)) {
        $file = INCLUDES_DIR . "/" . str_replace("_", "/", $class) . ".class.php";
    } elseif ($class{0} == "S") {
        $file = PLUGINS_DIR . "/$class.class.php";
    } else {
        $file = SlightPHP::$appDir . "/" . str_replace("_", "/", $class) . ".class.php";
    }
    if (file_exists($file))
        return require_once($file);
}

spl_autoload_register('__autoload');

/**
	End file,Don't add ?> after this.
*/
