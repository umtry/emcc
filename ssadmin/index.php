<?PHP
error_reporting(E_ALL ^ E_NOTICE);
require_once("../common.php");  
SlightPHP::setDebug(true);
SlightPHP::setAppDir("app");                        //后台
SlightPHP::setSplitFlag("-.");
SLanguage::setLanguageDir(LANGUAGE_DIR);            //语言包  
//define('DEBUG',1);
if (false === ($res = SlightPHP::run())) {
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    include '../404.html';
} else {
    echo $res;
}

/**
	End file,Don't add ?> after this.
*/