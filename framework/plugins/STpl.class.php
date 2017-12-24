<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

if(!defined("SLIGHTPHP_PLUGINS_DIR"))define("SLIGHTPHP_PLUGINS_DIR",dirname(__FILE__));
require_once(SLIGHTPHP_PLUGINS_DIR."/tpl/Tpl.php");
/**
 * @package SlightPHP
 */
class STpl extends Tpl{
    static $engine;
    public function __construct(){
        $site_url = SUtil::getSettings("siteurl");
        $siteurl = array();
        $siteurl['www'] = $site_url;
        $siteurl['ssadmin'] = SConstant::SSADMIN;
        $siteurl['assets'] = $site_url."/assets";
        $siteurl['assets_admin'] = $site_url."/assets/admin";
        $siteurl['assets_curtemplate'] = $site_url."/assets/".CUR_TEMPLATEDIR;
        $siteurl['upload'] = $site_url."/upload";

        $this->assign('siteurl', $siteurl);
    }
    /**
    * 前台模版
    */
    public function render($tpl,$parames=array(),$aj_tpl=false){
        parent::$compile_dir = "data".DIRECTORY_SEPARATOR."tcache_w".DIRECTORY_SEPARATOR.SlightPHP::$templatesDir;
        parent::$template_dir= "themes".DIRECTORY_SEPARATOR.SlightPHP::$templatesDir;
        if (!empty($parames)) {
            foreach($parames as $key=>$value){
                $this->assign($key,$value);
            }
        }
        
        if($aj_tpl == true){
            ob_start();
            parent::fetch("$tpl");
            $data = ob_get_contents();
            ob_end_clean();
            return $data;
        }                
        return parent::fetch("$tpl");
    }
    /**
    * 后台模版
    */
    public function srender($tpl,$parames=array(),$aj_tpl=false){
        parent::$compile_dir = SlightPHP::$appDir.DIRECTORY_SEPARATOR."templates_c";
        parent::$template_dir= SlightPHP::$appDir.DIRECTORY_SEPARATOR."templates";
        if (!empty($parames)) {
            foreach($parames as $key=>$value){
                $this->assign($key,$value);
            }
        }     
        
        if($aj_tpl == true){
            ob_start();
            parent::fetch("$tpl");
            $data = ob_get_contents();
            ob_end_clean();
            return $data;
        }
                   
        return parent::fetch("$tpl");
    }
    /**
     * 302 redirect
     */
    public function redirect($url) {
    	header('Location:'.$url);
    	exit;
    }
}
?>
