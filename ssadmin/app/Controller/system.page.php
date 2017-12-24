<?php

/**
 * @author fenngle
 * @time 2016-03-17
 */
class Controller_system extends Controller_basepage {

    public function __construct() {
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }

    //服务流程列表
    function pageSflow($inPath) {
        $this->isCanUse(15);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1, SUtil::getStr($get['page'], 'int'));
        $limit = $this->get('limit', 'int', SConstant::PAGE_SIZE);
        $orderby = "order by sort asc,id desc";
        $systemServ = new Service_ss_system();

        $get['status'] = $this->get('status', 'int', 1);
        $rs_data = $systemServ->get_service_list($get,'*',$orderby, $limit, $page, 1);
        $this->tvar = array(
            "sflow" => $rs_data['list'],
            "get" => $get
        );
        $this->pageBar($rs_data['count'], $limit, $page, $inPath);
        return $this->srender('system/sflow.html', $this->tvar);
    }

    //添加/编辑通话流程
    function pageBoxsflow() {
        $this->isCanUse(15);
        $systemServ = new Service_ss_system();
        if ($_POST['handle'] == 'save') {
            $id = $this->ajpost('id', 'int', 0);
            $params['content'] = $this->ajpost('content', 'string');
            $params['status'] = $this->ajpost('status', 'int', 1);
            $params['sort'] = $this->ajpost('sort', 'int', 10);
            $params['stype'] = $this->ajpost('stype', 'int', 1);

            $rs = $systemServ->edit_service($this->_userid, $this->_username, $params, $id);
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

        $id = $this->get("id", "int", 0);
        $rs_data = array();
        if ($id > 0) {
            $rs_data = $systemServ->get_service_detail($id);
        }
        $this->tvar = array(
            "rs_data" => $rs_data
        );
        return $this->srender('system/boxsflow.html', $this->tvar);
    }

    //添加/编辑通话流程回复
    function pageBoxsflowrs() {
        $this->isCanUse(15);
        $systemServ = new Service_ss_system();
        if ($_POST['handle'] == 'save') {
            $id = $this->ajpost('id', 'int', 0);
            $params['rs_title'] = $this->ajpost('rs_title', 'string');
            $params['sid'] = $this->ajpost('sid', 'int', 0);

            $rs = $systemServ->edit_service_rs($this->_userid, $this->_username, $params, $id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $systemServ->getError();
                $this->printJson();
            }

            //序列化存入流程表
            $s_data = $systemServ->get_service_rs($params['sid']);
            if (!empty($s_data) && !empty($s_data['list'])) {
                $serviceModel = new Model_ss_service();
                $str_data = serialize($s_data['list']);
                $serviceModel->update(array('id' => $params['sid']), array('stitles' => $str_data));
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = 'window.location.reload';
            $this->printJson();
        }

        $sid = $this->get("id", "int", 0);
        $rs_data = array();
        if ($sid > 0) {
            $rs_data = $systemServ->get_service_rs($sid);
        }
        $this->tvar = array(
            "rs_data" => $rs_data['list'],
            "sid" => $sid
        );
        return $this->srender('system/boxsflowrs.html', $this->tvar);
    }

    //删除服务流程回复
    function pageDelsrs() {
        $id = $this->ajpost('id', 'int', 0);
        $sid = $this->ajpost('sid', 'int', 0);
        if (empty($id)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '删除失败';
            $this->printJson();
        }
        $servicersModel = new Model_ss_servicers();
        $systemServ = new Service_ss_system();
        $rs = $servicersModel->delete(array('id' => $id));
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = $servicersModel->getError();
            $this->printJson();
        }
        //序列化存入流程表
        $s_data = $systemServ->get_service_rs($sid);
        $serviceModel = new Model_ss_service();
        if (!empty($s_data) && !empty($s_data['list'])) {
            $str_data = serialize($s_data['list']);
            $serviceModel->update(array('id' => $sid), array('stitles' => $str_data));
        } else {
            $serviceModel->update(array('id' => $sid), array('stitles' => ''));
        }
        $this->outData['method'] = 'write';
        $this->outData['runFunction'] = 'window.location.reload';
        $this->printJson();
    }

    //呼叫参数设置
    function pageCallipset() {
        $this->isCanUse(17);
        $settingsModel = new Model_ss_settings();
        if ($_POST) {
            $data = $_POST['data'];
            if (!empty($data['call_server_ip'])) {
                $data['call_server_ip'] = trim($data['call_server_ip'], "http://");
            }
            $settingsModel->setting($data, 'callcenter');
            $this->showMsg("操作成功", "callipset.html", 3, 1);
        }
        $this->tvar['setting'] = $settingsModel->getSettings('callcenter');
        $this->srender('system/callipset.html', $this->tvar);
    }

}
