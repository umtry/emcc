<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_ajax extends Controller_basepage {
    public function __construct(){
        parent::__construct();
        //$this->isLogin(true);
        $this->tvar = array();
    }
    
    /**
     * 地区联动数据
     * 
     * @param int $type_id 地区父类id
     * @return json 数据列表
     */
    public function pageRegion() {
        $regionModel = new Model_ss_region();
        $type_id = $this->ajget('type_id', 'int');
        $lists = $regionModel->getList(array("parent_id" => $type_id));
        if ($lists === false) {
            $this->printJson(array("status" => 0, "msg" => "参数错误"));
        }
        $this->printJson(array("status" => 2, "msg" => $lists));
    }
    
    //挂断电话后创建通话记录
    function pageRephnu() {
        $phone = $this->ajget('phone', 'string');
        $call_type = $this->ajget('call_type', 'int');
        $answer_status = $this->ajget('answer_status', 'int');
        $call_time = $this->ajget('call_time', 'int', 0);
        $call_start = $this->ajget('call_start', 'int', 0);
        $call_end = $this->ajget('call_end', 'int', 0);
        $recording_address = $this->ajget('recording_address', 'string', '', false);
        $userModel = new Model_ss_user();
        $callrecordModel = new Model_ss_callrecord();
        $customer = $userModel->getOne(array('phone' => $phone), 'user_id,real_name');
        if (empty($customer) || empty($customer['user_id'])) {
            $customer['user_id'] = 0;
            $customer['real_name'] = "";
        }

        $indata = array(
            'user_id' => $customer['user_id'],
            'real_name' => $customer['real_name'],
            'admin_id' => $this->_userid,
            'admin_name' => $this->_username,
            'phone' => $phone,
            'call_type' => $call_type,
            'call_time' => $call_time,
            'call_start' => $call_start,
            'call_end' => $call_end,
            'answer_status' => $answer_status,
            'recording_address' => $recording_address
        );
        $rs = $callrecordModel->insert($indata);
        if ($rs === false) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '自动添加通话记录失败';
            $this->printJson();
        }
        $this->outData['method'] = 'alert';
        $this->outData['msg'] = '通话记录已自动添加|S';
        $this->printJson();
    }
    
    
    //导入群发用户
    function pageImassuser() {
        $mass = $this->ajpost('mass', 'int', 0);
        $send_type = $this->ajpost('send_type', 'int', 1);
        $mids = $_POST['mids'];
        $massuerModel = new Model_ss_massuser();
        if (empty($mass)) {
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '请选择群发项目';
            $this->printJson();
        }
        $tmp_msg = "";
        if ($send_type == 1) {
            if (empty($mids)) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '请勾选用户';
                $this->printJson();
            }
            foreach ($mids as $val) {
                list($m_id, $real_name, $phone) = explode('##', $val);
                if(empty($phone) || !SUtil::IsMobile($phone)){
                    $tmp_msg = $tmp_msg.$real_name."(ID:".$m_id."); ";
                    continue;
                }
                $indata = array(
                    'mass_id' => $mass,
                    'user_id' => $m_id,
                    'real_name' => $real_name,
                    'phone' => $phone
                );
                $massuerModel->insert($indata, true);
            }
            if(!empty($tmp_msg)){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $tmp_msg."以上用户未导入群发号码";
                $this->printJson();
            }
        } else {
            $matterServ = new Service_ss_matter();
            $get_str = $this->ajpost('get_str', 'string', '', false);
            $get = unserialize(base64_decode($get_str));
            
            $orderby = "order by sm.ex_id desc";
            $fields = "sm.*,su.phone,su.area_name,su.address";
            $rs_data = $matterServ->get_express_mutilist($get, $orderby,$fields);
            
            foreach($rs_data['list'] as $val){
                $val['phone'] = trim($val['phone']);
                if(empty($val['phone']) || !SUtil::IsMobile($val['phone'])){
                    $tmp_msg = $tmp_msg.$val['real_name']."(ID:".$val['m_id']."); ";
                    continue;
                }
                $indata = array(
                    'mass_id' => $mass,
                    'user_id' => $val['user_id'],
                    'real_name' => $val['real_name'],
                    'phone' => $val['phone']
                );
                $massuerModel->insert($indata, true);
            }
            if(!empty($tmp_msg)){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $tmp_msg."<br>以上用户未导入群发号码,需手动更正其手机号|F|900000";
                $this->printJson();
            }
        }

        $this->outData['method'] = 'alert';
        $this->outData['msg'] = '电话号码导入群发成功|S';
        $this->printJson();
    }
}
