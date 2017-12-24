<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_settings extends Controller_basepage {
    private $tModel;
    function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->isCanUse(1);
        $this->tModel = new Model_ss_settings();
        $this->tvar = array();
    }
    //SEO设置
    function pageSeoconf(){
        if($_POST){
            $data = $_POST['data'];
            $pic_url = $_FILES['pic_url'];
            if(empty($data['title']) || empty($data['siteurl'])){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '请填写必填项';
                $this->printJson();
            }
            if(substr($data['siteurl'], 0,7) != "http://"){
                $data['siteurl'] = "http://".$data['siteurl'];
            }
            $data['siteurl'] = rtrim($data['siteurl'], "/");
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $data['sitelogo'] = $pic_url['url'];
            }
            $this->tModel->setting($data,'site');
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '操作成功|S';
            $this->printJson();
        }
        $this->tvar['setting'] = $this->tModel->getSettings('site');
        $this->srender('settings/seoconf.html',$this->tvar);
    }
    //系统设置
    function pageSiteconf(){
        if($_POST){
            $data = $_POST['data'];
            if(empty($data['admin_url'])){
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = '请填写必填项';
                $this->printJson();
            }
            $this->tModel->setting($data,'site');
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '操作成功|S';
            $this->printJson();
        }
        $this->tvar['setting'] = $this->tModel->getSettings('site');
        //网站模板列表
        $this->tvar['templates'] = SUtil::getTemplatelists();
        $this->srender('settings/siteconf.html',$this->tvar);
    }
    //上传配置
    function pageUploadconf(){
        if($_POST){
            $data = $_POST['data'];
            $this->tModel->setting($data,'upload');
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '操作成功|S';
            $this->printJson();
        }
        $this->tvar['setting'] = $this->tModel->getSettings('upload');
        $this->srender('settings/uploadconf.html',$this->tvar);
    }
    //水印配置
    function pageWatermarkconf(){
        if($_POST){
            $data = $_POST['data'];
            $pic_url = $_FILES['pic_url'];
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $data['watermark_image'] = $pic_url['url'];
            }
            $this->tModel->setting($data,'watermark');
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '操作成功|S';
            $this->printJson();
        }
        $this->tvar['setting'] = $this->tModel->getSettings('watermark');
        $this->srender('settings/watermarkconf.html',$this->tvar);
    }
    
    //邮箱设置
    function pageMailconf(){
        if($_POST){
            $data = $_POST['data'];
            $this->tModel->setting($data,'mail');
            $this->outData['method'] = 'alert';
            $this->outData['msg'] = '操作成功|S';
            $this->printJson();
        }
        $this->tvar['setting'] = $this->tModel->getSettings('mail');
        $this->srender('settings/mailconf.html',$this->tvar);
    }
    
    //支付设置
    function pagePayconf(){
        $payModel = new Model_ss_pay();
        $pay = $payModel->getList(array(1));
        $this->tvar['pay'] = $pay;
        $this->srender('settings/payconf.html',$this->tvar);
    }
    //编辑
    function pageBoxpayconf(){
        $payModel = new Model_ss_pay();
        $payServ = new Service_ss_pay();
        if ($_POST['handle'] == 'save') {
            $pay_id = $this->ajpost('pay_id', 'int');
            $params['pay_name'] = $this->ajpost('pay_name', 'string');
            $params['pay_code'] = $this->ajpost('pay_code', 'string');
            $params['pay_type'] = $this->ajpost('pay_type', 'int',1);
            $params['pay_status'] = $this->ajpost('pay_status', 'int',1);
            $params['pay_des'] = $this->ajpost('pay_des', 'string','',false);
            //$params['pay_key'] = $this->ajpost('pay_key', 'string', '', false);
            
            $pay = $payModel->getOne(array('pay_id'=>$pay_id));
            $pay['pay_key'] = @unserialize($pay['pay_key']);
            
            $pay_key = $_POST['pay_key'];
            foreach($pay_key as $key=>$val){
				$pay_key[$key]=array("name"=>$pay['pay_key'][$key]['name'],"val"=>$pay_key[$key]);
			}
			$params['pay_key'] = serialize($pay_key);

            $rs = $payServ->edit_payconf($this->_userid, $params, $pay_id);
            if ($rs === false) {
                $this->outData['method'] = 'alert';
                $this->outData['msg'] = $payServ->getError();
                $this->printJson();
            }
            $this->outData['method'] = 'write';
            $this->outData['runFunction'] = "window.parent.location.reload";
            $this->outData['runFunction2'] = 'parentDialog.close';
            $this->printJson();
        }
        
        $pay_id = $this->get("pay_id", "int");
        $pay_data = $payModel->getOne(array('pay_id'=>$pay_id));
        $pay_key = @unserialize($pay_data['pay_key']);
        if(!is_array($pay_key)){
			$pay_key = array("id"=>array("name"=>"商户号","val"=>""),"key"=>array("name"=>"密匙","val"=>""));
		}
        $this->tvar = array(
            "pay_data"=>$pay_data,
            "pay_id"=>$pay_id,
            "pay_key"=>$pay_key
        );
        return $this->srender('settings/boxpayconf.html', $this->tvar);
    }

}
