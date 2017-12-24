<?php
/**
* @author fenngle 区域管理
* @time 2012-2-11 11:28:13 
*/
class Controller_region extends Controller_basepage {
    private $tvar;
    private $tModel;
    function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_region();
        $this->tvar = array();
    }

    function pageList(){
        $this->isCanUse(13);
        $region_id = $this->get('region_id','int',1);
        if(empty($region_id)){
            $condition1['region_id'] = 1;
            $condition['parent_id'] = 0;
        }else{
            $condition1['region_id'] = $region_id;
            $condition['parent_id'] = $region_id;
        }
        //
        $region = $this->tModel->getOne($condition1);
        $regions = $this->tModel->getList($condition,'*', 'order by region_id asc');
        $this->tvar = array(
            'region'=>$region,
            'regions'=>$regions,
            'parent_id'=>$region_id
        );
        return $this->srender('region/list.html',$this->tvar);
    }

    //添加区域信息
    function pageAjadd(){
        $this->isCanUse(13);
        $region_type = $this->ajpost('region_type','int');
        $parent_id = $this->ajpost('parent_id','int');
        $region_name = $this->ajpost('region_name','string');

        $indata = array(
            "region_type"=>$region_type,
            "parent_id"=>$parent_id,
            "region_name"=>$region_name
        );
        $rs =$this->tModel->insert($indata);
        if($rs>0){
            if(empty($parent_id)){
                $condition['parent_id'] = 0;
            }else{
                $condition['parent_id'] = $parent_id;
            }
            $this->tvar['regions'] = $this->tModel->getList($condition,'*', 'order by region_id asc');
            $tmpdata = $this->srender('region/tblist.html',$this->tvar,true);
            
            $this->printJson(array("status"=>1,"msg"=>$tmpdata));
        }else{
            $this->printJson(array("status"=>0,"msg"=>"操作失败"));
        }
    }

    //删除区域信息
    function pageAjdel(){
        $this->isCanUse(13);
        $region_id = $this->ajpost("region_id","int");
        //判断是否有下级内容
        $region = $this->tModel->getOne(array("parent_id"=>$region_id));
        if(!SUtil::isEmptyArr($region)){
            $this->printJson(array("status"=>0,"msg"=>"请先删除下级地区"));
        }
        
        $rs = $this->tModel->delete(array('region_id'=>$region_id));
        if($rs === false){
            $this->printJson(array("status"=>0,"msg"=>"操作失败"));
        }else{
            $this->printJson(array("status"=>1,"msg"=>"操作成功"));
        }
    }

    //更新区域信息
    function pageAjedit(){
        $this->isCanUse(13);
        $region_id = $this->ajpost('region_id','int');
        $region_name = $this->ajpost('region_name','string');

        $updata = array(
            'region_name' => $region_name
        );
        $rs=$this->tModel->update(array('region_id'=>$region_id),$updata);
        //判断是否更新成功
        if ($rs === false) {
            $this->printJson(array("status"=>0,"msg"=>"操作失败"));
        }
        $this->printJson(array("status"=>1,"msg"=>"操作成功"));
    }

}

