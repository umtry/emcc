<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_links extends Controller_basepage {
    private $tvar;
    private $tModel;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_links();
        $this->tvar = array();
    }
    
    public function pageList($inPath){
        $this->isCanUse(13);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',SConstant::PAGE_SIZE);
        
        $get['type'] = $type = $this->get('type','int','',false);
        $get['status'] = $status = $this->get('status','int','',false);
        
        $condition = array(1);
        
        if($type !== ""){
            $condition['type'] = $type;
        }
        if($status !== ""){
            $condition['status'] = $status;
        }
        
        $orderby = "order by sort desc";
        $links = $this->tModel->getList($condition,"*",$orderby,$limit,$page);
        $count = $this->tModel->getCount($condition);
        
        $this->tvar = array(
            "links"=>$links,
            "get"=>$get
        );
        $this->pageBar($count,$limit,$page,$inPath);
        return $this->srender('links/list.html',$this->tvar);
    }

    public function pageAdd(){
        $this->isCanUse(13);
        if($_POST){
            $type = $this->post('type','int');
            $link_name = $this->post('link_name');
            $to_url = $this->post('to_url','string','',false);
            $sort = $this->post('sort','int',10);
            $status = $this->post('status');
            $pic_url = $_FILES['pic_url'];
            if(substr($to_url, 0,7) != "http://"){
                $to_url = "http://".$to_url;
            }
            $indata = array(
                "type"=>$type,
                "link_name"=>$link_name,
                "to_url"=>$to_url,
                "status"=>$status,
                "sort"=>$sort
            );
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $indata['pic_url'] = $pic_url['url'];
            }
            $rs = $this->tModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
            $this->showMsg("添加成功",'list.html',3,1);
        }
        
        $this->tvar = array(
            
        );
        return $this->srender('links/add.html',$this->tvar);
    }

    public function pageEdit(){
        $this->isCanUse(13);
        $id = $this->get("id","int");
        if($_POST){
            $type = $this->post('type','int');
            $link_name = $this->post('link_name');
            $to_url = $this->post('to_url','string','',false);
            $sort = $this->post('sort','int',10);
            $status = $this->post('status');
            $pic_url = $_FILES['pic_url'];
            if(substr($to_url, 0,7) != "http://"){
                $to_url = "http://".$to_url;
            }
            $condition = array();
            $condition['id'] = $id;
            $updata = array(
                "type"=>$type,
                "link_name"=>$link_name,
                "to_url"=>$to_url,
                "status"=>$status,
                "sort"=>$sort
            );
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $updata['pic_url'] = $pic_url['url'];
            }
            $rs = $this->tModel->update($condition, $updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'list.html',3,1);
        }
        $links = $this->tModel->getOne(array("id"=>$id));
        $this->tvar = array(
            "links"=>$links
        );
        return $this->srender('links/edit.html',$this->tvar);
    }

    public function pageDel(){
        $this->isCanUse(13);
        $id = $this->get("id","int");
        $rs = $this->tModel->delete(array("id"=>$id));
        if($rs === false){
            $this->showMsg("删除失败");
        }
        $this->showMsg("删除成功",'list.html',3,1);
    }
    
}
