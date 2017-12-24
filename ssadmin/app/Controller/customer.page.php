<?php

/**
 * 客户管理
 * @author fenngle
 * @time 2014-08-11
 */
class Controller_customer extends Controller_basepage {

    public function __construct() {
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }

    //居民信息列表
    public function pageResident($inPath) {
        $this->isCanUse(18);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1, SUtil::getStr($get['page'], 'int'));
        $limit = $this->get('limit', 'int', SConstant::PAGE_SIZE);
        $fields = "su.*,sur.*";
        $customerServ = new Service_ss_customer();
        $massServ = new Service_ss_mass();

        $get['address'] = $this->get("address", 'string', '', false);
        $get['real_name'] = $this->get('real_name', 'string', '', false);
        $get['sex'] = $this->get('sex', 'int', 0);
        $get['phone'] = $this->get('phone', 'string', '', false);
        $get['lrstime'] = $this->get('lrstime', 'time',0);
        $get['lretime'] = $this->get('lretime', 'time',0);
        
        $get['search_type'] = $this->get('search_type', 'int', 1);

        $condition = array();
        $condition[] = "su.status=1";
        if (!empty($get['address'])) {
            $condition[] = "su.address like '%" . $get['address'] . "%'";
        }
        if (!empty($get['real_name'])) {
            $condition[] = "su.real_name='" . $get['real_name'] . "'";
        }
        if (!empty($get['sex'])) {
            $condition[] = "su.sex=" . $get['sex'];
        }
        if (!empty($get['phone'])) {
            $condition[] = "su.phone like '%" . $get['phone'] . "%'";
        }
        if (!empty($get['lrstime'])) {
            $condition[] = "su.add_time>" . $get['lrstime'];
        }
        if (!empty($get['lretime'])) {
            $condition[] = "su.add_time<=" . $get['lretime'];
        }

        //导出Excel
        if ($get['search_type'] == 2) {
            $jobexpModel = new Model_ss_jobexp();
            $sex_arr = array(
                1=>'男',
                2=>'女'
            );
            
            $rs_data = $customerServ->get_customer_list($condition);
            $seobj = new SExcel();
            $seobj->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '姓名')
                ->setCellValue('C1', '性别')
                ->setCellValue('D1', '联系电话')
                ->setCellValue('E1', '所在地')
                ->setCellValue('F1', '备注')
                ->setCellValue('G1', '录入时间');
            $seobj->getActiveSheet()->getStyle('A1:N1')->applyFromArray(
                array(
                    'font' => array (
                        'bold' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            
            $num = 2;
            foreach($rs_data['list'] as $val){
                $seobj->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num, $val['m_id'])
                    ->setCellValue('B'.$num, $val['real_name'])
                    ->setCellValue('C'.$num, $sex_arr[$val['sex']])
                    ->setCellValue('D'.$num, $val['phone']." ")
                    ->setCellValue('E'.$num, $val['pro_name'].$val['city_name'].$val['area_name'].$val['street_name'].$val['shequ_name'].$val['wangge_name'])
                    ->setCellValue('F'.$num, date('Y-m-d H:i', $val['add_time']));
                $num++;
                
                $jobexp_data = $jobexpModel->getList(array('m_id'=>$val['m_id']));
                if(!empty($jobexp_data)){
                    foreach($jobexp_data as $v){
                        $seobj->getActiveSheet()->mergeCells('B'.$num.':N'.$num);
                        $vls = '就职单位：'.$v['cp_name'].' '
                            .'入职时间：'.date('Y-m-d', $v['stime']).' '
                            .'离职时间：'.date('Y-m-d', $v['etime']).' '
                            .'职位：'.$v['ac_name'].' '
                            .'工作内容：'.$v['job_cont'];
                        $seobj->setActiveSheetIndex(0)
                            ->setCellValue('A'.$num, '工作经历|-')
                            ->setCellValue('B'.$num, $vls);
                        $seobj->getActiveSheet()->getStyle('A'.$num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $num++;
                    }
                    $num++;
                }
            }
            $name = "客户信息、工作经历列表";
            $seobj->getActiveSheet()->setTitle($name);
            $seobj->setActiveSheetIndex(0);
             header('Content-Type: application/vnd.ms-excel');
             header('Content-Disposition: attachment;filename="'.$name.'.xls"');
             header('Cache-Control: max-age=0');
             $objWriter = PHPExcel_IOFactory::createWriter($seobj, 'Excel5');
             $objWriter->save('php://output');
             exit;
        }
        $rs_data = $customerServ->get_customer_list($condition, $fields, $limit, $page, 1);
        //群发列表
        $mass = $massServ->get_mass_list(array('status'=>1),'*' ,'order by mass_id desc');
        $this->tvar = array(
            "customer" => $rs_data['list'],
            "count" => $rs_data['count'],
            "mass" => $mass['list'],
            "get" => $get,
            "get_str" => base64_encode(serialize($get))
        );
        $this->pageBar($rs_data['count'], $limit, $page, $inPath);
        return $this->srender('customer/resident.html', $this->tvar);
    }
    
    //检查当前客户信息是否存在
    function pageCheckcustomer(){
        $this->isCanUse(18);
        $phone = $this->get("phone", "string", "", false);
        if(empty($phone)){
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '未获取手机号码';
            $this->printJson();
        }
        $customerServ = new Service_ss_customer();
        $user_id = $customerServ->check_customer($phone);
        if(empty($user_id)){
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = $customerServ->getError();
            $this->printJson();
        }
        if($user_id == 'addcus'){
            $this->outData['method'] = 'location';
            $this->outData['location'] = '/ssadmin/customer/add.html?phone='.$phone;
            $this->printJson();
        }else{
            $this->outData['method'] = 'location';
            $this->outData['location'] = '/ssadmin/customer/edit.html?user_id='.$user_id;
            $this->printJson();
        }
    }
    
    //绑定射频卡
    function pageBindrfid(){
        $this->isCanUse(18);
        $user_id = $this->ajpost("user_id", "int");
        $rfid_id = $this->ajpost('rfid_id', 'string','',false);
        $userrdtModel = new Model_ss_userrdt();
        $rs = $userrdtModel->update(array('user_id'=>$user_id), array('rfid_id'=>$rfid_id));
        if($rs === false){
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = $userrdtModel->getError();
            $this->printJson();
        }
        $this->outData['method'] = 'alert';
        $this->outData['msg'] = '绑定成功|S';
        $this->printJson();
    }
    //编辑客户信息
    function pageEdit() {
        $this->isCanUse(18);
        $user_id = $this->get("user_id", "int", 0);
        $phone = $this->get("phone", "string", "", false);
        $customerServ = new Service_ss_customer();
        $commonServ = new Service_ss_common();
        if ($_POST) {
            $user_id = $this->ajpost("user_id", "int");
            $params['phone'] = $this->ajpost('phone', 'string','',false);
            $params['real_name'] = $this->ajpost('real_name', 'string');
            $params['idcard'] = $this->ajpost('idcard', 'string','',false);
            $params['sex'] = $this->ajpost('sex', 'int');
            $pic_url = $_FILES['pic_url'];
            
            $params['pro_id'] = $this->ajpost("pro_id",'int',0);
            $params['city_id'] = $this->ajpost("city_id",'int',0);
            $params['area_id'] = $this->ajpost("area_id",'int',0);
            $params['street_id'] = $this->ajpost("street_id",'int',0);
            $params['shequ_id'] = $this->ajpost("shequ_id",'int',0);
            $params['address'] = $this->ajpost('address', 'string', '', false);
            $params['qq'] = $this->ajpost('qq', 'string', '', false);
            $params['email'] = $this->ajpost('email', 'string', '', false);
            $params['resume'] = $this->ajpost('resume', 'string', '', false);
            
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $params['avatar'] = $pic_url['url'];
            }

            $rs = $customerServ->edit_customer($params, $user_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $customerServ->getError();
                $this->printJson();
            }
//            $rs = $customerServ->edit_customer_ext($this->_userid, $params_ext, $user_id);
//            if ($rs === false) {
//                $this->outData['method'] = 'alert';
//                $this->outData['msg'] = $customerServ->getError();
//                $this->printJson();
//            }

            $this->outData['method'] = 'write';
            $this->outData['msg'] = '修改成功|S';
            if($params['avatar']){
                $this->outData['html']['profile-picture'] = '<img class="img-responsive" src="'.tpl_modifier_picsize($params['avatar']).'" />';
                $this->outData['runFunction'] = '$("a.remove").click';
            }
            $this->printJson();
        }

        if (empty($user_id) && empty($phone)) {
            $this->showMsg('参数错误');
        }
        $fields = "su.*,sur.rfid_id";
        $customer = $customerServ->get_customer_detail($user_id, $phone, $fields);
        $region = $commonServ->region(32,394,$customer['area_id'],$customer['street_id'],$customer['shequ_id']);
        $this->tvar = array(
            "customer" => $customer,
            "region"=>$region
        );
        return $this->srender('customer/edit.html', $this->tvar);
    }
    //新增客户信息
    public function pageAdd() {
        $this->isCanUse(18);
        $phone = $this->get("phone", "string", "", false);
        $customerServ = new Service_ss_customer();
        if ($_POST) {
            $params['phone'] = $this->ajpost('phone', 'string','',false);
            $params['sex'] = $this->ajpost('sex', 'int');
            $params['real_name'] = $this->ajpost('real_name', 'string');
            $params['add_time'] = $this->_time;
            
            $rs = $customerServ->add_customer($this->_userid, $params);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $customerServ->getError();
                $this->printJson();
            }

            $this->outData['method'] = 'location2';
            $this->outData['location'] = 'edit.html?user_id='.$rs;
            $this->outData['msg'] = '添加成功|S';
            $this->printJson();
        }

        $this->tvar = array(
            'phone'=>$phone
        );
        return $this->srender('customer/add.html', $this->tvar);
    }
    //删除客户信息-软删除
    function pageCusdel(){
        $this->isCanUse(18);
        $user_id = $this->get("user_id", "int", 0);
        $userModel = new Model_ss_user();
        $rs = $userModel->update(array('user_id'=>$user_id), array('status'=>3));
        if($rs === false){
            $this->showMsg("删除失败");
        }
        $this->showMsg("删除成功",'list.html',3,1);
    }
    
    
    
    //标准通话流程内容列表
    function pageLoadservice() {
        $this->isCanUse(18);
        $user_id = $this->ajget('user_id', 'int', 0);
        $systemServ = new Service_ss_system();
        $userrdtModel = new Model_ss_userrdt();
        //保存访问结果
        if ($_POST['handle'] == 'save') {
            $stitle_ids = $_POST['stitle_ids'];
            $user_id = $this->ajpost('user_id', 'int');
            $remark = $this->ajpost('remark', 'string', '', false);
            if (!empty($stitle_ids)) {
                $services = implode(',', $stitle_ids);
                $rs = $userrdtModel->update(array('user_id' => $user_id), array('services' => $services, 'remark' => $remark));
                if ($rs === false) {
                    $this->outData['method'] = 'alert';
                    $this->outData['msg'] = '保存结果失败';
                    $this->printJson();
                }
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '保存结果成功|S';
                $this->printJson();
            }
        }

        $userrdt = $userrdtModel->getOne(array('user_id' => $user_id), 'services,remark');
        $services_arr = array();
        if (!empty($userrdt['services'])) {
            $services_arr = explode(',', $userrdt['services']);
        }

        $cond['status'] = 1;
        $orderby = "order by sort asc";
        $rs_data = $systemServ->get_service_list($cond, "*", $orderby);
        $this->tvar = array(
            'services' => $rs_data['list'],
            'services_arr' => $services_arr,
            'remark' => $userrdt['remark'],
            'user_id' => $user_id
        );
        $html_str = $this->srender('customer/loadservice.html', $this->tvar, true);
        $this->outData['html']['loadservice'] = $html_str;
        $this->printJson();
    }
    
    
    
    /**
     * 新增/编辑事项（弹窗）
     */
    function pageBoxematter() {
        $this->isCanUse(18);
        $matterServ = new Service_ss_matter();
        $staffModel = new Model_ss_staff();
        if ($_POST['handle'] == 'save') {
            $id = $this->ajpost('id', 'int', 0);
            $params['user_id'] = $this->ajpost('user_id', 'int');
            $params['answer_state'] = $this->ajpost('answer_state', 'int',0);
            $params['content'] = $this->ajpost('content', 'string','',false);
            $params['remark'] = $this->ajpost('remark', 'string', '', false);
            $params['status'] = $this->ajpost('status', 'int',1);
            $params['intention'] = $this->ajpost('intention', 'int');
            $params['stf_id'] = $this->ajpost('stf_id', 'int',0);
            $params['price'] = $this->ajpost('price', 'float',0.00);
            $params['service_time'] = $this->ajpost('service_time', 'time',0);

            $rs = $matterServ->edit_matter($this->_userid, $this->_username, $params, $id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $matterServ->getError();
                $this->printJson();
            }
            
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.loadmatter";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $id = $this->get("id", "int", 0);
        $user_id = $this->get("user_id", "int", 0);
        if (empty($user_id) && empty($id)) {
            $box_arr = array(
                'boxmsg' => '请先保存客户基本信息，才可添加事项'
            );
            return $this->srender('common/boxmsg.html', $box_arr);
        }
        $rs_data = array();
        $rs_data['user_id'] = $user_id;
        if ($id > 0) {
            $rs_data = $matterServ->get_matter_detail(array('id' => $id));
        }
        $mcates = $matterServ->get_mcate_list();
        //工作人员
        $staff = $staffModel->get_list(array('status'=>1), 'order by stf_id desc');
        $this->tvar = array(
            "matter" => $rs_data,
            "mcates" => $mcates['list'],
            "staff" => $staff['list'],
            "answer_state" => SStatic::$answer_state,
            "matter_status" => SStatic::$matter_status,
            "pay_status" => SStatic::$pay_status
        );
        return $this->srender('customer/boxmatter.html', $this->tvar);
    }

    //事项列表
    function pageLoadmatter($inPath) {
        $this->isCanUse(18);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        
        $user_id = $this->ajget('user_id', 'int');
        $matterServ = new Service_ss_matter();
        $cond['user_id'] = $user_id;
        $orderby = "order by id desc";
        $rs_data = $matterServ->get_matter_list($cond,$orderby,$limit,$page,1);
        
        $this->tvar = array(
            'matters' => $rs_data['list'],
            "answer_state" => SStatic::$answer_state,
            "matter_status" => SStatic::$matter_status,
            "pay_status" => SStatic::$pay_status
        );
        $this->pageBar($rs_data['count'],$limit,$page,$inPath);
        $html_str = $this->srender('customer/loadmatter.html', $this->tvar, true);
        $this->outData['html']['loadmatter'] = $html_str;
        $this->printJson();
    }
    
    
    /**
     * 新增/编辑快递（弹窗）
     */
    function pageBoxeexpress() {
        $this->isCanUse(18);
        $expressModel = new Model_ss_express();
        if ($_POST['handle'] == 'save') {
            $ex_id = $this->ajpost('ex_id', 'int', 0);
            $params['ex_type'] = $this->ajpost('ex_type', 'int');
            $params['ex_number'] = $this->ajpost('ex_number', 'string', '', false);
            $params['user_id'] = $this->ajpost('user_id', 'int');
            $params['real_name'] = $this->ajpost('real_name', 'string');
            $params['answer_state'] = $this->ajpost('answer_state', 'int',0);
            $params['content'] = $this->ajpost('content', 'string','',false);
            $params['price'] = $this->ajpost('price', 'float',0.00);
            $params['pay_status'] = $this->ajpost('pay_status', 'int',1);
            $params['status'] = $this->ajpost('status', 'int',1);

            $rs = $expressModel->edit($params, $ex_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $expressModel->getError();
                $this->printJson();
            }
            
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.loadexpress";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }

        $ex_id = $this->get("ex_id", "int", 0);
        $user_id = $this->get("user_id", "int", 0);
        if (empty($user_id) && empty($ex_id)) {
            $box_arr = array(
                'boxmsg' => '请先保存客户基本信息，才可添加事项'
            );
            return $this->srender('common/boxmsg.html', $box_arr);
        }
        $rs_data = array();
        $rs_data['user_id'] = $user_id;
        if ($ex_id > 0) {
            $rs_data = $expressModel->getOne(array('ex_id' => $ex_id));
        }
        $this->tvar = array(
            "express" => $rs_data,
            "answer_state" => SStatic::$answer_state,
            "exp_status" => SStatic::$exp_status,
            "expf_status" => SStatic::$expf_status,
            "exps_status" => SStatic::$exps_status,
            "pay_status" => SStatic::$pay_status
        );
        return $this->srender('customer/boxeexpress.html', $this->tvar);
    }

    //收发件列表
    function pageLoadexpress($inPath) {
        $this->isCanUse(18);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        
        $user_id = $this->ajget('user_id', 'int');
        $expressModel = new Model_ss_express();
        $cond['user_id'] = $user_id;
        $orderby = "order by ex_id desc";
        $rs_data = $expressModel->get_list($cond,$orderby,$limit,$page,1);
        
        $this->tvar = array(
            'expresses' => $rs_data['list'],
            "answer_state" => SStatic::$answer_state,
            "expf_status" => SStatic::$expf_status,
            "exps_status" => SStatic::$exps_status,
            "pay_status" => SStatic::$pay_status
        );
        $this->pageBar($rs_data['count'],$limit,$page,$inPath);
        $html_str = $this->srender('customer/loadexpress.html', $this->tvar, true);
        $this->outData['html']['loadexpress'] = $html_str;
        $this->printJson();
    }
    //
    function pageAjstatus(){
        $this->isCanUse(18);
        $ex_id = $this->get("ex_id", "int", 0);
        $expressModel = new Model_ss_express();
        $rs = $expressModel->update(array('ex_id'=>$ex_id), array('status'=>2,'comp_time'=>$this->_time));
        if($rs === false){
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '编辑失败';
            $this->printJson();
        }
        $this->outData['method'] = 'write';
        $this->outData['runFunction'] = "loadexpress";
        $this->printJson();
    }
    
    //用户通话记录列表
    function pageLoadcallrecord() {
        $this->isCanUse(18);
        $user_id = $this->ajrequest('user_id', 'int');
        $callrecordModel = new Model_ss_callrecord();
        $cond['user_id'] = $user_id;
        $orderby = "order by id desc";
        $rs_data = $callrecordModel->get_list($cond, $orderby);

        $this->tvar = array(
            'callrecords' => $rs_data['list'],
        );
        $html_str = $this->srender('customer/loadcallrecord.html', $this->tvar, true);
        $this->outData['html']['loadcallrecord'] = $html_str;
        $this->printJson();
    }
    
    
    /**
     * 发送短信
     */
    function pageSendmsg(){
        $this->isCanUse(18);
        $msg_phone = $this->ajpost('msg_phone', 'string');
        $msg = $this->ajpost('msg', 'string');
        if (empty($msg)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '请填写发送内容';
            $this->printJson();
        }
        $msg = $msg.SConstant::MSG_SUFFIX;
        SCommon::sendPhoneMsg($msg_phone, $msg, 1, 2);
        $this->outData['method'] = 'write';
        $this->outData['val']['msg'] = "";
        $this->outData['msg'] = '发送成功|S';
        $this->outData['runFunction'] = "loadmsglog";
        $this->printJson();
    }
    //发送短信记录列表
    function pageLoadmsglog() {
        $this->isCanUse(18);
        $phone = $this->ajrequest('phone', 'string', '', false);
        
        $sendmsglogModel = new Model_ss_sendmsglog();
        if(empty($phone)){
            $this->tvar = array(
                'msglogs' => array(),
            );
        }else{
            $cond['phone'] = $phone;
            $orderby = "order by sl_id desc";
            $rs_data = $sendmsglogModel->get_list($cond, $orderby);
            $this->tvar = array(
                'msglogs' => $rs_data['list'],
            );
        }
        $html_str = $this->srender('customer/loadmsglog.html', $this->tvar, true);
        $this->outData['html']['loadmsglog'] = $html_str;
        $this->printJson();
    }

}
