<?php
/**
* @author fenngle
* @time 2014-08-11 
*/
class Controller_main extends Controller_basepage {
    public function __construct(){
        parent::__construct();
        $this->tvar = array();
    }
    //首页
    function pageIndex(){
        $this->tvar = array(
        );
        $this->render('main/index.html',$this->tvar);
    }
    
    //图片热点
    function pageHotp(){
        
        $this->render('main/index.html',$this->tvar);
    }

}
