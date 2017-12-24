<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_group extends Controller_basepage {
    private $tvar;
    private $tModel;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_group();
        $this->tvar = array();
    }
    /*
     * 管理员组
     */
    //管理员组列表
    public function pageAgroup(){
        $this->isCanUse(3);
        if($_POST){
            $group_id = $this->post('group_id','int',0);
            $group_name = $this->post('group_name');
            if(empty($group_id)){
                $indata = array(
                    "group_name"=>$group_name,
                    "user_type"=>1
                );
                $rs = $this->tModel->insert($indata);
            }else{
                $updata = array(
                    "group_name"=>$group_name,
                );
                $rs = $this->tModel->update(array("group_id"=>$group_id,"user_type"=>1),$updata);
            }
            
            if($rs === false){
                $this->showMsg("操作失败");
            }
            $this->showMsg("操作成功",'agroup.html',3,1);
        }
        
        $group = $this->tModel->getList(array("user_type"=>1));
        $this->tvar = array(
            "group"=>$group
        );
        return $this->srender('group/agroup.html',$this->tvar);
    }
    //删除
    public function pageAgroupdel(){
        $this->isCanUse(3);
        $group_id = $this->get('group_id','int');
        $condition = array();
        $condition['group_id'] = $group_id;
        //先判断此用户组下是否还有用户
        $adminModel = new Model_ss_admin();
        $admin = $adminModel->getOne($condition);
        if(!empty($admin)){
            $this->showMsg("操作失败，此用户组下还有用户");
            exit;
        }
        $condition['user_type'] = 1;
        $rs = $this->tModel->delete($condition);
        if($rs === false){
            $this->showMsg("操作失败");
        }
        $this->showMsg("操作成功",'agroup.html',3,1);
    }
    
    /*
     * 前台用户组
     */
    //用户组列表
    public function pageGroup(){
        $this->isCanUse(4);
        if($_POST){
            $group_id = $this->post('group_id','int',0);
            $group_name = $this->post('group_name');
            if(empty($group_id)){
                $indata = array(
                    "group_name"=>$group_name,
                    "user_type"=>2
                );
                $rs = $this->tModel->insert($indata);
            }else{
                $updata = array(
                    "group_name"=>$group_name,
                );
                $rs = $this->tModel->update(array("group_id"=>$group_id,"user_type"=>2),$updata);
            }
            
            if($rs === false){
                $this->showMsg("操作失败");
            }
            $this->showMsg("操作成功",'group.html',3,1);
        }
        
        $group = $this->tModel->getList(array("user_type"=>2));
        $this->tvar = array(
            "group"=>$group
        );
        return $this->srender('group/group.html',$this->tvar);
    }
    //删除
    public function pageGroupdel(){
        $this->isCanUse(4);
        $group_id = $this->get('group_id','int');
        $condition = array();
        $condition['group_id'] = $group_id;
        //先判断此用户组下是否还有用户
        $adminModel = new Model_ss_admin();
        $admin = $adminModel->getOne($condition);
        if(!empty($admin)){
            $this->showMsg("操作失败，此用户组下还有用户");
            exit;
        }
        $condition['user_type'] = 2;
        $rs = $this->tModel->delete($condition);
        if($rs === false){
            $this->showMsg("操作失败");
        }
        $this->showMsg("操作成功",'group.html',3,1);
    }
}
