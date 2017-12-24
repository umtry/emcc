<?php

/**
 * 群发管理
 * @author fenngle
 * @time 2014-08-11
 */
class Controller_mass extends Controller_basepage {
    public function __construct() {
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }
    
    /**
     * 信息群发
     */
    function pageMassmassage($inPath) {
        $this->isCanUse(20);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1, SUtil::getStr($get['page'], 'int'));
        $limit = $this->get('limit', 'int', SConstant::PAGE_SIZE);
        $orderby = "order by status asc,mass_id desc";
        $massServ = new Service_ss_mass();

        $get['type'] = $this->get('type', 'int', 0);
        $get['status'] = $this->get('status', 'int', 0);

        $rs_data = $massServ->get_mass_list($get,'*', $orderby, $limit, $page, 1);
        $this->tvar = array(
            "massmassage" => $rs_data['list'],
            "get" => $get
        );
        $this->pageBar($rs_data['count'], $limit, $page, $inPath);
        return $this->srender('mass/massmassage.html', $this->tvar);
    }

    //发送
    function pageSend() {
        $this->isCanUse(20);
        $mass_id = $this->ajpost("mass_id", "int", 0);
        $status = $this->ajpost("status", "int", 2);
        $type = $this->ajpost("type", "int", 1);
        if (empty($mass_id) || empty($status)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '发送失败';
            $this->printJson();
        }
        $massModel = new Model_ss_mass();
        $massuserModel = new Model_ss_massuser();
        //检测是否有待发用户
        $massuser = $massuserModel->getList(array('mass_id'=>$mass_id,'status'=>1), 'id,user_id,real_name,phone');
        if(empty($massuser)){
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '发送失败,请添加发送的客户';
            $this->printJson();
        }
        
        
        if ($type == 1) {//语音群发,改状态并通知通讯服务器
            $rs = $massModel->update(array('mass_id' => $mass_id), array('status' => $status));
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '发送失败';
                $this->printJson();
            }
            $this->outData['runFunction'] = 'ws.send';
            $this->outData['data'] = '4';
            $this->outData['runFunction2'] = 'window.reload';
            $this->printJson();
        }
        if ($type == 2) {//短信群发，循环发送
            $mass = $massModel->getOne(array('mass_id'=>$mass_id), 'content,status');
            if($mass['status']>2){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '已发送';
                $this->printJson();
            }
            
            $content = $mass['content'].SConstant::MSG_SUFFIX;
            $mobile_arr = array();
            foreach($massuser as $val){
                $mobile_arr[] = $val['phone'];
            }
            $mobile = implode(',', $mobile_arr);
            
            $commonServ = new Service_ss_common();
            $rs = $commonServ->send_msg($mobile, $content, 2);
            if($rs['status']==200 && $rs['str'] > 0){
                $massModel->update(array('mass_id' => $mass_id), array('status' => 3));
                $massuserModel->update(array('mass_id' => $mass_id),  array('status' => 2,'send_time'=>$this->_time));
            }else{
                if($rs['str'] == "-5"){
                    $this->outData['method'] = 'alert';
                    $this->outData['msg'] = '短信余额不足';
                    $this->printJson();
                }else{
                    $this->outData['method'] = 'alert';
                    $this->outData['msg'] = '发送失败';
                    $this->printJson();
                }
            }
            
        }

        $this->outData['method'] = 'reload';
        $this->printJson();
    }

    function pageBoxmassmassage() {
        $this->isCanUse(20);
        $massServ = new Service_ss_mass();
        if ($_POST['handle'] == 'save') {
            $mass_id = $this->ajpost('mass_id', 'int', 0);
            $params['type'] = $this->ajpost('type', 'int', 1);
            $params['content'] = $this->ajpost('content', 'string');
            $params['status'] = $this->ajpost('status', 'int', 1);
            $params['send_time'] = $this->ajpost('send_time', 'int', 0);

            $rs = $massServ->edit_mass($this->_userid, $this->_username, $params, $mass_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $massServ->getError();
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.wlreload";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $mass_id = $this->get("mass_id", "int", 0);
        $rs_data = array();
        if ($mass_id > 0) {
            $rs_data = $massServ->get_mass_detail(array("mass_id" => $mass_id));
        }
        $this->tvar = array(
            "rs_data" => $rs_data,
            "mass_id" => $mass_id
        );

        return $this->srender('mass/boxmassmassage.html', $this->tvar);
    }

    function pageBoxmassuser($inPath) {
        $this->isCanUse(20);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1, SUtil::getStr($get['page'], 'int'));
        $limit = $this->get('limit', 'int', SConstant::PAGE_SIZE);
        $orderby = "order by status asc,id desc";
        $massServ = new Service_ss_mass();

        $get['mass_id'] = $this->get("mass_id", "int");
        $get['real_name'] = $this->get("real_name", "string", "", false);
        $get['phone'] = $this->get("phone", "string", "", false);
        $get['status'] = $this->get('status', 'int', 0);

        $massuser = $massServ->get_massuser_list($get, '*',$orderby, $limit, $page, 1);
        $this->tvar = array(
            "massuser" => $massuser['list'],
            "get" => $get
        );
        $this->pageBar($massuser['count'], $limit, $page, $inPath);
        return $this->srender('mass/boxmassuser.html', $this->tvar);
    }
    
    function pageBoxmassuseradd() {
        $this->isCanUse(20);
        $massServ = new Service_ss_mass();
        if ($_POST['handle'] == 'save') {
            $params['mass_id'] = $this->ajpost('mass_id', 'int');
            $params['real_name'] = $this->ajpost('real_name', 'string');
            $params['status'] = $this->ajpost('status', 'int', 1);
            $params['phone'] = $this->ajpost('phone', 'string');

            $rs = $massServ->edit_massuser($this->_userid, $params);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $massServ->getError();
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.document.getElementById('_DialogFrame_0').contentWindow.location.reload";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $mass_id = $this->get("mass_id", "int", 0);
        $this->tvar = array(
            "mass_id" => $mass_id
        );
        return $this->srender('mass/boxmassuseradd.html', $this->tvar);
    }
    
    //删除群发信息
    function pageDelmassmsg() {
        $this->isCanUse(20);
        $mass_id = $this->ajpost("mass_id", "int", 0);
        if (empty($mass_id)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '参数错误';
            $this->printJson();
        }
        $massModel = new Model_ss_mass();
        $rs = $massModel->delete("mass_id=".$mass_id." and (status=1 or status=2)");
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '删除失败';
            $this->printJson();
        }
        $this->outData['method'] = 'reload';
        $this->printJson();
    }

    //删除群发用户
    function pageDeluser() {
        $this->isCanUse(20);
        $id = $this->ajpost("id", "int", 0);
        if (empty($id)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '参数错误';
            $this->printJson();
        }
        $massuserModel = new Model_ss_massuser();
        $rs = $massuserModel->delete(array('id' => $id));
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '删除失败';
            $this->printJson();
        }
        $this->outData['method'] = 'reload';
        $this->printJson();
    }

}
