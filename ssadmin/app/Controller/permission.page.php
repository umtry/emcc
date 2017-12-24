<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_permission extends Controller_basepage {
    private $tvar;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }
    /*
     * 后台系统一级菜单
     */
    //列表
    public function pageSysnav(){
        $sysnavModel = new Model_ss_sysnav();
        $condition = array(1);
        $sysnav = $sysnavModel->getList($condition);
        $this->tvar = array(
            "sysnav"=>$sysnav
        );
        return $this->srender('permission/sysnav.html',$this->tvar);
    }
    
    /*
     * 权限资源
     */
    //权限资源列表
    public function pageSource($inPath){
        $this->isCanUse(2);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        
        $get['sysnav_id'] = $sysnav_id = $this->get("sysnav_id","int","",false);
        $get['license_name'] = $license_name = $this->get("license_name","string","",false);
        
        $licenseModel = new Model_ss_license();
        $sysnavModel = new Model_ss_sysnav();
        $condition = array(1);
        $orderby = "order by sort desc,license_id desc";
        if($sysnav_id !== ""){
            $condition['sysnav_id'] = $sysnav_id;
        }
        if($license_name !== ""){
            $condition[] = " license_name like '%$license_name%' ";
        }
        
        $license = $licenseModel->getList($condition,"*",$orderby,$limit,$page);
        $count = $licenseModel->getCount($condition);
        $sysnav = $sysnavModel->getList(array(1));
        foreach($sysnav as $key=>$val){
            $id_tmp = $val['sysnav_id'];
            $name_tmp = $val['sysnav_name'];
            $sysnav_tmp[$id_tmp] = $name_tmp;
        }
        foreach($license as $key=>$val){
            $license[$key]['sysnav_name'] = $sysnav_tmp[$val['sysnav_id']];
        }
        $this->tvar = array(
            "license"=>$license,
            "sysnav"=>$sysnav,
            "get"=>$get
        );
        $this->pageBar($count,$limit,$page,$inPath);
        return $this->srender('permission/source.html',$this->tvar);
    }
    //权限资源添加
    public function pageSourceadd(){
        $this->isCanUse(2);
        $licenseModel = new Model_ss_license();
        $sysnavModel = new Model_ss_sysnav();
        if($_POST){
            $license_name = $this->post('license_name');
            $sysnav_id = $this->post('sysnav_id','int');
            $license_type = $this->post('license_type','int');
            $content = $this->post('content');
            $license_url = $this->post('license_url','string','',false);
            $status = $this->post('status','int');
            $sort = $this->post('sort','int');
            
            $indata = array(
                "sysnav_id"=>$sysnav_id,
                "license_name"=>$license_name,
                "license_type"=>$license_type,
                "content"=>$content,
                "license_url"=>$license_url,
                "status"=>$status,
                "sort"=>$sort
            );
            $rs = $licenseModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
            $this->showMsg("添加成功",'source.html',3,1);
        }
        //获取栏目
        $sysnav = $sysnavModel->getList(array(1),"*","order by sort desc");
        $this->tvar = array(
            "sysnav"=>$sysnav
        );
        return $this->srender('permission/sourceadd.html',$this->tvar);
    }
    //权限资源编辑
    public function pageSourceedit(){
        $this->isCanUse(2);
        $license_id = $this->get("license_id","int");
        $licenseModel = new Model_ss_license();
        $sysnavModel = new Model_ss_sysnav();
        if($_POST){
            $license_name = $this->post('license_name');
            $sysnav_id = $this->post('sysnav_id','int');
            $license_type = $this->post('license_type','int');
            $content = $this->post('content');
            $license_url = $this->post('license_url','string','',false);
            $status = $this->post('status','int');
            $sort = $this->post('sort','int');
            
            $condition = array();
            $condition['license_id'] = $license_id;
            $updata = array(
                "sysnav_id"=>$sysnav_id,
                "license_name"=>$license_name,
                "license_type"=>$license_type,
                "content"=>$content,
                "license_url"=>$license_url,
                "status"=>$status,
                "sort"=>$sort
            );
            $rs = $licenseModel->update($condition, $updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'source.html',3,1);
        }
        $license = $licenseModel->getOne(array("license_id"=>$license_id));
        //获取栏目
        $sysnav = $sysnavModel->getList(array(1),"*","order by sort desc");
        $this->tvar = array(
            "license"=>$license,
            "sysnav"=>$sysnav
        );
        return $this->srender('permission/sourceedit.html',$this->tvar);
    }
    //权限资源删除
    public function pageSourcedel(){
        $this->isCanUse(2);
        $license_id = $this->get("license_id","int");
        $licenseModel = new Model_ss_license();
        $rs = $licenseModel->delete(array("license_id"=>$license_id));
        if($rs === false){
            $this->showMsg("删除失败");
        }
        $this->showMsg("删除成功",'source.html',3,1);
    }
    
    
    /*
     * 权限分配用户组
     */
    //后台管理员权限分配
    public function pageLicgroup(){
        $this->isCanUse(5,true,1);
        $group_id = $this->get("group_id","int",1);
        $sysnavModel = new Model_ss_sysnav();
        $licenseModel = new Model_ss_license();
        $groupModel = new Model_ss_group();
        if($_POST){
            $lic = $_POST['lic'];
            $lic_str = implode(",", $lic);
            $updata = array(
                "license_data"=>$lic_str
            );
            $rs = $groupModel->update(array("group_id"=>$group_id), $updata);
            if($rs === false){
                $this->showMsg("设置失败");
            }
            $this->showMsg("设置成功",'',3,1);
        }
        //取得所有管理员用户组
        $group = $groupModel->getList(array("user_type"=>1));
        //
        $curlic = "";
        foreach($group as $key=>$val){
            if($val['group_id'] == $group_id){
                $curlic = $val['license_data'];
            }
        }
        $curlic = explode(",", $curlic);
        
        $sysnav = $sysnavModel->getList(array(1),"*","order by sort desc");
        foreach($sysnav as $key=>$val){
            $license = $licenseModel->getList(array("sysnav_id"=>$val['sysnav_id'],"status"=>1,"license_type"=>1),"license_id,license_name,content","order by sort desc");
            $sysnav[$key]["license"] = $license;
        }
        $this->tvar = array(
            "group"=>$group,
            "sysnav"=>$sysnav,
            "curlic"=>$curlic,
            "group_id"=>$group_id
        );
        return $this->srender('permission/licgroup.html',$this->tvar);
    }
}
