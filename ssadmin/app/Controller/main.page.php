<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_main extends Controller_basepage {
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }
    //首页
    public function pageIndex(){
        return $this->srender('main/index.html',$this->tvar);
    }
    
}
