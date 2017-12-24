<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_admin extends Controller_basepage {
    private $tvar;
    private $tModel;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_admin();
        $this->tvar = array();
    }
    //修改当前登录用户密码
    public function pageRepwd() {
        $opwd = $this->post('opwd');
        $npwd = $this->post('npwd');
        $admin = $this->tModel->getOne(array("user_id"=>$this->_userid), "user_pass,pass_random");
        if($admin['user_pass'] != md5($opwd.SConstant::ADMINUSERKEY.$admin['pass_random'])){
            $this->showMsg("旧密码不正确");
        }
        $rs = $this->tModel->chage_pwd($this->_userid, $npwd);
        if($rs === false){
            $this->showMsg("操作失败");
        }
        $this->showMsg("修改成功",'',3,1);
    }
    
    //列表
    public function pageList($inPath){
        $this->isCanUse(6);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        $groupModel = new Model_ss_group();
        $condition = array(1);
        $admin = $this->tModel->getList($condition,"*","order by last_time desc");
        $count = $this->tModel->getCount($condition);
        foreach($admin as $key=>$val){
            $rsdata = $groupModel->getOne(array("group_id"=>$val['group_id']),'group_name');
            $admin[$key]['group_name'] = $rsdata['group_name'];
        }
        $this->tvar = array(
            "admin"=>$admin
        );
        $this->pageBar($count,$limit,$page,$inPath);
        return $this->srender('admin/list.html',$this->tvar);
    }

    //添加管理员
    public function pageAdd(){
        $this->isCanUse(6);
        $groupModel = new Model_ss_group();
        if($_POST){
            $user_name = $this->post('user_name');
            $pwd1 = $this->post('pwd1');
            $pwd2 = $this->post('pwd2');
            $group_id = $this->post('group_id','int');
            $status = $this->post('status','int',1);
            $is_system = $this->post('is_system','int',0);
            $lock_note = $this->post('lock_note','string','',false);
            $remark = $this->post('remark','string','',false);
            $port = $this->post('port','int',0);
            $ip = SUtil::getIP();
            
            if($pwd1 != $pwd2){
                $this->showMsg("密码输入错误");
            }
            //判断账号重复
            $c = $this->tModel->check_name_used($user_name);
            if(!empty($c)){
                $this->showMsg("此账号已被占用");
            }
            
            $pass_random = SUtil::random(6,0);
            $user_pass = md5($pwd1.SConstant::ADMINUSERKEY.$pass_random);
            
            $indata = array(
                "user_name"=>$user_name,
                "user_pass"=>$user_pass,
                "group_id"=>$group_id,
                "add_time"=>$this->_time,
                "add_ip"=>$ip,
                "last_time"=>$this->_time,
                "last_ip"=>$ip,
                "status"=>$status,
                "is_system"=>$is_system,
                "lock_note"=>$lock_note,
                "remark"=>$remark,
                "pass_random"=>$pass_random,
                "port"=>$port
            );
            $rs = $this->tModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
        }
        $group = $groupModel->getList(array('user_type'=>1));
        $this->tvar = array(
            "group"=>$group
        );
        return $this->srender('admin/add.html',$this->tvar);
    }
    //编辑管理员
    public function pageEdit(){
        $this->isCanUse(6);
        $user_id = $this->get("user_id","int");
        $groupModel = new Model_ss_group();
        if($_POST){
            $pwd1 = $this->post('pwd1','string','',false);
            $pwd2 = $this->post('pwd2','string','',false);
            $group_id = $this->post('group_id','int');
            $status = $this->post('status','int',1);
            $is_system = $this->post('is_system','int',0);
            $lock_note = $this->post('lock_note','string','',false);
            $remark = $this->post('remark','string','',false);
            $port = $this->post('port','int',0);
            
            $updata = array(
                "group_id"=>$group_id,
                "status"=>$status,
                "is_system"=>$is_system,
                "lock_note"=>$lock_note,
                "remark"=>$remark,
                "port"=>$port
            );
            //修改密码
            if(!empty($pwd1) && !empty($pwd2)){
                if($pwd1 != $pwd2){
                    $this->showMsg("密码输入错误");
                }
                $updata['pass_random'] = SUtil::random(6,0);
                $updata['user_pass'] = md5($pwd1.SConstant::ADMINUSERKEY.$updata['pass_random']);
            }
            
            $rs = $this->tModel->update(array("user_id"=>$user_id),$updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'list.html',3,1);
        }
        $group = $groupModel->getList(array('user_type'=>1));
        $admin = $this->tModel->getOne(array("user_id"=>$user_id));
        $this->tvar = array(
            "group"=>$group,
            "admin"=>$admin
        );
        return $this->srender('admin/edit.html',$this->tvar);
    }
}
