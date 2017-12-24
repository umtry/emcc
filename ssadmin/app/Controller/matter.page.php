<?php

/**
 * 事项管理
 * @author fenngle
 * @time 2014-08-11
 */
class Controller_matter extends Controller_basepage {
    public function __construct() {
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }

    //事项分类管理
    function pageMcate() {
        $this->isCanUse(16);
        $systemServ = new Service_ss_system();
        $rs_data = $systemServ->get_mcate_list();
        $this->tvar = array(
            "rs_data" => $rs_data
        );
        return $this->srender('matter/mcate.html', $this->tvar);
    }
    //事项类型编辑
    function pageBoxmcate() {
        $this->isCanUse(16);
        $systemServ = new Service_ss_system();
        $mattercateModel = new Model_ss_mattercate();
        if ($_POST['handle'] == 'save') {
            $st_id = $this->ajpost('st_id', 'int', 0);
            $params['parent_id'] = $this->ajpost('parent_id', 'int', 0);
            $params['st_name'] = $this->ajpost('st_name');
            $params['sort'] = $this->ajpost('sort', 'int');
            $params['status'] = $this->ajpost('status', 'int');
            $params['seq'] = "0";
            if ($params['parent_id']) {
                $pdata = $mattercateModel->getOne(array('st_id' => $params['parent_id']), "st_id,seq");
                $params['seq'] = $pdata['seq'] . "," . $pdata['st_id'];
            }
            $rs = $systemServ->edit_mcate($this->_userid, $this->_username, $params, $st_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $systemServ->getError();
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = 'window.parent.location.reload';
            $this->outData['runFunction2'] = 'parentDialog.close';
            //$this->outData['msg'] = '添加成功|S';
            $this->printJson();
        }

        $st_id = $this->get("st_id", "int", 0);
        $stustatu = array();
        $stustatus = $systemServ->get_mcate_list();
        if ($st_id > 0) {
            foreach ($stustatus as $val) {
                if ($val['st_id'] == $st_id) {
                    $stustatu = $val;
                }
            }
        }

        $this->tvar = array(
            'stustatus' => $stustatus,
            'stustatu' => $stustatu
        );
        return $this->srender('matter/boxmcate.html', $this->tvar);
    }
    
    
    /**
     * 事项列表
     */
    function pageList($inPath){
        $this->isCanUse(11);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        $matterServ = new Service_ss_matter();
        $mattercateModel = new Model_ss_mattercate();
        $staffModel = new Model_ss_staff();
        
        $get['intention'] = $this->get('intention','int',0);
        $get['real_name'] = $this->get('real_name','string','',false);
        $get['phone'] = $this->get('phone','string','',false);
        $get['status'] = $this->get('status','int',0);
        $get['pay_status'] = $this->get('pay_status','int',0);
        
        $orderby = "order by sm.id desc";
        $fields = "sm.*,su.phone,su.area_name,su.address";
        //$rs_data = $matterModel->get_list($get, 'order by id desc', $limit, $page, 1);
        $rs_data = $matterServ->get_matter_mutilist($get, $orderby,$fields, $limit, $page, 1);
        
        
        $mattercate = $mattercateModel->get_list(array('status'=>1), 'order by st_id asc');
        $staffs = $staffModel->getList(array(1));
        $tmp_staffs = array();
        foreach($staffs as $val){
            $tmp_staffs[$val['stf_id']] = $val['real_name'];
        }
        $this->tvar = array(
            "matter"=>$rs_data['list'],
            "mattercate"=>$mattercate['list'],
            "tmp_staffs"=>$tmp_staffs,
            "matter_status" => SStatic::$matter_status,
            "pay_status" => SStatic::$pay_status,
            "get"=>$get
        );
        $this->pageBar($rs_data['count'],$limit,$page,$inPath);
        return $this->srender('matter/list.html',$this->tvar);
    }
    function pageBoxematter() {
        $this->isCanUse(11);
        $matterModel = new Model_ss_matter();
        if ($_POST['handle'] == 'save') {
            $id = $this->ajpost('id', 'int', 0);
            $pay_status = $this->ajpost('pay_status', 'int');
            $status = $this->ajpost('status', 'int');
            $rs = $matterModel->update(array('id'=>$id), array('status'=>$status,'pay_status'=>$pay_status));
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = "修改失败";
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = 'window.parent.location.reload';
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $id = $this->get("id", "int");
        $matter = $matterModel->getOne(array('id'=>$id), 'id,status,pay_status');
        $this->tvar = array(
            'matter' => $matter,
            "matter_status" => SStatic::$matter_status,
            "pay_status" => SStatic::$pay_status
        );
        return $this->srender('matter/boxematter.html', $this->tvar);
    }
    function pageDmatter(){
        $this->isCanUse(11);
        $id = $this->ajpost('id', 'int');
        $matterModel = new Model_ss_matter();
        $rs = $matterModel->delete(array('id' => $id));
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '删除失败';
            $this->printJson();
        }
        $this->outData['method'] = 'write';
        $this->outData['runFunction'] = 'window.location.reload';
        $this->printJson();
    }
    
    
    /**
     * 收发件列表
     */
    function pageExpress($inPath){
        $this->isCanUse(19);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        $matterServ = new Service_ss_matter();
        $massServ = new Service_ss_mass();
        
        $get['phone'] = $this->get('phone','string','',false);
        $get['ex_number'] = $this->get('ex_number','string','',false);
        $get['real_name'] = $this->get('real_name','string','',false);
        $get['ex_type'] = $this->get('ex_type','int',0);
        $get['pay_status'] = $this->get('pay_status','int',0);
        $get['status'] = $this->get('status','int',0);
        
        $orderby = "order by sm.ex_id desc";
        $fields = "sm.*,su.phone,su.area_name,su.address";
        $rs_data = $matterServ->get_express_mutilist($get, $orderby,$fields, $limit, $page, 1);
        
        //群发列表
        $mass = $massServ->get_mass_list(array('status'=>1),'*' ,'order by mass_id desc');
        
        $this->tvar = array(
            "express"=>$rs_data['list'],
            "count"=>$rs_data['count'],
            "mass" => $mass['list'],
            "exp_status" => SStatic::$exp_status,
            "expf_status" => SStatic::$expf_status,
            "exps_status" => SStatic::$exps_status,
            "pay_status" => SStatic::$pay_status,
            "get"=>$get
        );
        $this->pageBar($rs_data['count'],$limit,$page,$inPath);
        return $this->srender('matter/express.html',$this->tvar);
    }
    function pageBoxeexpress() {
        $this->isCanUse(19);
        $expressModel = new Model_ss_express();
        if ($_POST['handle'] == 'save') {
            $ex_id = $this->ajpost('ex_id', 'int', 0);
            $params['pay_status'] = $this->ajpost('pay_status', 'int');
            $params['status'] = $this->ajpost('status', 'int');
            if($params['pay_status']==2){
                $params['pay_time'] = $this->_time;
            }
            if($params['status']==2){
                $params['comp_time'] = $this->_time;
            }
            $rs = $expressModel->update(array('ex_id'=>$ex_id), $params);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = "修改失败";
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = 'window.parent.location.reload';
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $ex_id = $this->get("ex_id", "int");
        $express = $expressModel->getOne(array('ex_id'=>$ex_id), 'ex_id,ex_type,status,pay_status');
        $this->tvar = array(
            'express' => $express,
            "expf_status" => SStatic::$expf_status,
            "exps_status" => SStatic::$exps_status,
            "pay_status" => SStatic::$pay_status
        );
        return $this->srender('matter/boxeexpress.html', $this->tvar);
    }
    function pageDexpress(){
        $this->isCanUse(19);
        $ex_id = $this->ajpost('ex_id', 'int');
        $expressModel = new Model_ss_express();
        $rs = $expressModel->delete(array('ex_id' => $ex_id));
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '删除失败';
            $this->printJson();
        }
        $this->outData['method'] = 'write';
        $this->outData['runFunction'] = 'window.location.reload';
        $this->printJson();
    }
}
