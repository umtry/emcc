<?PHP
error_reporting(E_ALL ^ E_NOTICE);
require_once("./common.php");
define("CUR_TEMPLATEDIR", SUtil::getSettings("template"));
SlightPHP::setDebug(true);
SlightPHP::setAppDir("www");                                                //前台
SlightPHP::setTemplatesDir(CUR_TEMPLATEDIR);                                //网站模版
SlightPHP::setSplitFlag("-.");
SLanguage::setLanguageDir(LANGUAGE_DIR);                                    //语言包

if($_REQUEST['debug']=='yes'){ 
	define('DEBUG',1);
}
if (false === ($res = SlightPHP::run())) {
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    include './404.html';
} else {
    echo $res;
}

/**
	End file,Don't add ?> after this.
*/