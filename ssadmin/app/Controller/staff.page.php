<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_staff extends Controller_basepage {
    private $tvar;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }
    
    //列表
    public function pageList($inPath){
        $this->isCanUse(12);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        $staffModel = new Model_ss_staff();
        
        $rs_data = $staffModel->get_list(array(1), 'order by stf_id desc', '*', $limit, $page, 1);
        $this->tvar = array(
            "staff"=>$rs_data['list']
        );
        $this->pageBar($rs_data['count'],$limit,$page,$inPath);
        return $this->srender('staff/list.html',$this->tvar);
    }
    
    function pageBoxestaff(){
        $this->isCanUse(12);
        $staffModel = new Model_ss_staff();
        if ($_POST['handle'] == 'save') {
            $stf_id = $this->ajpost('stf_id', 'int', 0);
            $params['stf_name'] = $this->ajpost('stf_name','string','',false);
            $params['real_name'] = $this->ajpost('real_name');
            $params['service'] = $this->ajpost('service','string','',false);
            $params['phone'] = $this->ajpost('phone','string','',false);
            $params['address'] = $this->ajpost('address','string','',false);
            $params['status'] = $this->ajpost('status', 'int', 1);

            $rs = $staffModel->edit($params, $stf_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $staffModel->getError();
                $this->printJson();
            }
            
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.location.reload";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $stf_id = $this->get("stf_id", "int", 0);
        $staff = array();
        if ($stf_id > 0) {
            $staff = $staffModel->getOne(array('stf_id' => $stf_id));
        }
        
        $this->tvar = array(
            "staff" => $staff
        );
        return $this->srender('staff/boxestaff.html', $this->tvar);
    }

    
}
