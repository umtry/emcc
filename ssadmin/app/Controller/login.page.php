<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_login extends Controller_basepage {
    private $tServ;
    public function __construct(){
        parent::__construct();
        $this->tServ = new Service_ss_admin();
        $this->tvar = array();
    }
    //
    public function pageIndex(){
        if($_POST){
            $user_name = $this->ajpost('user_name','string','',false);
            $user_pass = $this->ajpost('user_pass','string','',false);
            $rs = $this->tServ->do_login($user_name, $user_pass);
            if($rs === false){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $this->tServ->getError();
                $this->printJson();
            }
            $this->outData['method'] = 'location';
            $this->outData['location'] = SConstant::SSADMIN;
            $this->printJson();
        }
        return $this->srender('main/login.html',$this->tvar);
    }

    public function pageLogout(){
        $this->logout();
        header("Location:".SConstant::SSADMIN);
    }
}
