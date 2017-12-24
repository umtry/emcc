<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_user extends Controller_basepage {
    private $tModel;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_user();
        $this->tvar = array();
    }
    //
    public function pageList($inPath){
        $this->isCanUse(7);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',SConstant::PAGE_SIZE);
        $orderby = "order by last_time desc,user_id desc";
        $groupModel = new Model_ss_group();
        
        $get['group_id'] = $group_id = $this->get('group_id','int','',false);
        $get['user_name'] = $user_name = $this->get('user_name','string','',false);
        $get['status'] = $status = $this->get('status','int','',false);
        
        $condition = array(1);
        if(!empty($group_id)){
            $condition['group_id'] = $group_id;
        }
        if(!empty($user_name)){
            $condition[] = " user_name like '%$user_name%' ";
        }
        if($status !== ""){
            $condition['status'] = $status;
        }
        
        $user = $this->tModel->getList($condition,"*",$orderby);
        $count = $this->tModel->getCount($condition);
        $group = $groupModel->getList(array('user_type'=>2));
        $this->tvar = array(
            "user"=>$user,
            "group"=>$group
        );
        $this->pageBar($count,$limit,$page,$inPath);
        return $this->srender('user/list.html',$this->tvar);
    }

    //添加新用户
    public function pageAdd(){
        $this->isCanUse(7);
        $groupModel = new Model_ss_group();
        if($_POST){
            $user_name = $this->post('user_name');
            $pwd1 = $this->post('pwd1');
            $pwd2 = $this->post('pwd2');
            $group_id = $this->post('group_id','int');
            $status = $this->post('status','int',1);
            $lock_note = $this->post('lock_note','string','',false);
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
            $user_pass = md5($pwd1.SConstant::SYSUSERKEY.$pass_random);
            
            $gdata = $groupModel->getOne(array('group_id'=>$group_id),"group_name");
            
            $indata = array(
                "user_name"=>$user_name,
                "user_pass"=>$user_pass,
                "group_id"=>$group_id,
                "group_name"=>$gdata['group_name'],
                "add_time"=>$this->_time,
                "add_ip"=>$ip,
                "status"=>$status,
                "lock_note"=>$lock_note,
                "pass_random"=>$pass_random
            );
            $rs = $this->tModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
            $this->showMsg("添加成功",'list.html',3,1);
        }
        $group = $groupModel->getList(array('user_type'=>2));
        $this->tvar = array(
            "group"=>$group
        );
        return $this->srender('user/add.html',$this->tvar);
    }
    
    //编辑用户
    public function pageEdit(){
        $this->isCanUse(7);
        $user_id = $this->get("user_id","int");
        $groupModel = new Model_ss_group();
        if($_POST){
            $pwd1 = $this->post('pwd1','string','',false);
            $pwd2 = $this->post('pwd2','string','',false);
            $group_id = $this->post('group_id','int');
            $status = $this->post('status','int',1);
            $lock_note = $this->post('lock_note','string','',false);
            
            $gdata = $groupModel->getOne(array('group_id'=>$group_id),"group_name");
            $updata = array(
                "group_id"=>$group_id,
                "group_name"=>$gdata['group_name'],
                "status"=>$status,
                "lock_note"=>$lock_note
            );
            //修改密码
            if(!empty($pwd1) && !empty($pwd2)){
                if($pwd1 != $pwd2){
                    $this->showMsg("密码输入错误");
                }
                $updata['pass_random'] = SUtil::random(6,0);
                $updata['user_pass'] = md5($pwd1.SConstant::SYSUSERKEY.$updata['pass_random']);
            }
            
            $rs = $this->tModel->update(array("user_id"=>$user_id),$updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'list.html',3,1);
        }
        $group = $groupModel->getList(array('user_type'=>2));
        $user = $this->tModel->getOne(array("user_id"=>$user_id));
        $this->tvar = array(
            "group"=>$group,
            "user"=>$user
        );
        return $this->srender('user/edit.html',$this->tvar);
    }
}
