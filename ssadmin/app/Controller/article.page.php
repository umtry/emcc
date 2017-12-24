<?php
/**
* @author fenngle
* @time 2014-08-11
*/
class Controller_article extends Controller_basepage {
    private $tvar;
    private $tModel;
    public function __construct(){
        parent::__construct();
        $this->isLogin(true);
        $this->tModel = new Model_ss_article();
        $this->tvar = array();
    }
    //
    public function pageList($inPath){
        $this->isCanUse(10);
        $get = SUtil::getUrlParams($inPath);
        $page = max(1,SUtil::getStr($get['page'],'int'));
        $limit = $this->get('limit','int',15);
        $condition = array(1);
        $orderby = "order by sort desc,a_id desc";
        
        $cateModel = new Model_ss_articlecate();
        $get['ac_id'] = $ac_id = $this->get('ac_id','int','',false);
        $get['stime'] = $stime = $this->get('stime','string','',false);
        $get['etime'] = $etime = $this->get('etime','string','',false);
        
        if(!empty($ac_id)){
            $condition['ac_id'] = $ac_id;
        }
        if(!empty($stime)){
            $condition[] = "create_time>".strtotime($stime." 00:00:00");
        }
        if(!empty($etime)){
            $condition[] = "create_time<=".strtotime($etime." 23:59:59");
        }
        
        $article = $this->tModel->getList($condition,"*",$orderby,$limit,$page);
        $count = $this->tModel->getCount($condition);
        $this->pageBar($count,$limit,$page,$inPath);
        //文章分类
        $cates = $cateModel->getList(array(1), "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,seq", "order by abs asc");
        $this->tvar = array(
            "article"=>$article,
            "cates"=>$cates,
            "get"=>$get
        );
        return $this->srender('article/list.html',$this->tvar);
    }
    //添加新文章
    public function pageAdd(){
        $this->isCanUse(10);
        $cateModel = new Model_ss_articlecate();
        if($_POST){
            $title = $this->post('title');
            $ac_id = $this->post('ac_id','int');
            $summary = $this->post('summary');
            $content = $this->post('content','html');
            $keywords = $this->post('keywords','string','',false);
            $status = $this->post('status','int');
            $sort = $this->post('sort','int');
            $pic_url = $_FILES['pic_url'];
            
            $ac = $cateModel->getOne(array('ac_id'=>$ac_id), 'ac_name,type');
            if($ac['type'] == 1){
                $this->showMsg("栏目禁止添加文章");
            }
            
            $indata = array(
                "ac_id"=>$ac_id,
                "ac_name"=>$ac['ac_name'],
                "title"=>$title,
                "summary"=>$summary,
                "content"=>$content,
                "keywords"=>$keywords,
                "status"=>$status,
                "sort"=>$sort,
                "create_time"=>$this->_time,
                "edit_time"=>$this->_time,
                "admin_id"=>$this->_userid,
                "admin_name"=>$this->_username
            );
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $indata['top_pic'] = $pic_url['url'];
            }
            $rs = $this->tModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
            $this->showMsg("添加成功",'list.html',3,1);
        }
        $articlecate = $cateModel->getList(array(1), "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,seq", "order by abs asc");
        $this->tvar = array(
            'articlecate'=>$articlecate
        );
        return $this->srender('article/add.html',$this->tvar);
    }
    //编辑文章
    public function pageEdit(){
        $this->isCanUse(10);
        $a_id = $this->get("a_id","int");
        $cateModel = new Model_ss_articlecate();
        if($_POST){
            $title = $this->post('title');
            $ac_id = $this->post('ac_id','int');
            $summary = $this->post('summary');
            $content = $this->post('content','html');
            $keywords = $this->post('keywords','string','',false);
            $status = $this->post('status','int');
            $sort = $this->post('sort','int');
            $pic_url = $_FILES['pic_url'];
            
            $condition = array();
            $condition['a_id'] = $a_id;
            $ac = $cateModel->getOne(array('ac_id'=>$ac_id), 'ac_name,type');
            if($ac['type'] == 1){
                $this->showMsg("栏目禁止添加文章");
            }
            $updata = array(
                "ac_id"=>$ac_id,
                "ac_name"=>$ac['ac_name'],
                "title"=>$title,
                "summary"=>$summary,
                "content"=>$content,
                "keywords"=>$keywords,
                "edit_time"=>$this->_time,
                "status"=>$status,
                "sort"=>$sort
            );
            //处理上传
            if(!empty($pic_url['name'])){
                $pic_url = $this->getUploadFilePath($pic_url);
                $updata['top_pic'] = $pic_url['url'];
            }
            $rs = $this->tModel->update($condition, $updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'list.html',3,1);
        }
        $articlecate = $cateModel->getList(array(1), "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,seq", "order by abs asc");
        
        $article = $this->tModel->getOne(array("a_id"=>$a_id));
        
        $this->tvar = array(
            'article'=>$article,
            'articlecate'=>$articlecate
        );
        return $this->srender('article/edit.html',$this->tvar);
    }
    public function pageDel(){
        $this->isCanUse(10);
        $a_id = $this->get("a_id","int");
        $rs = $this->tModel->delete(array("a_id"=>$a_id));
        if($rs === false){
            $this->showMsg("删除失败");
        }
        $this->showMsg("删除成功",'list.html',3,1);
    }

    //分类
    public function pageCate(){
        $this->isCanUse(9);
        $cateModel = new Model_ss_articlecate();
        $condition = array(1);
        $articlecate = $cateModel->getList($condition, "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,sort,seq", "order by abs asc");
        $this->tvar = array(
            "articlecate"=>$articlecate
        );
        return $this->srender('article/cate.html',$this->tvar);
    }
    public function pageCateadd(){
        $this->isCanUse(9);
        $cateModel = new Model_ss_articlecate();
        if($_POST){
            $parent_id = $this->post('parent_id','int',0);
            $ac_name = $this->post('ac_name');
            $type = $this->post('type','int');
            $sort = $this->post('sort','int');
            $seq = "0";
            if($parent_id){
                $pdata = $cateModel->getOne(array('ac_id'=>$parent_id), "ac_id,seq,type");
                if($pdata['type'] != 1){
                    $this->showMsg("只有栏目才可新增下级");
                }
                $seq = $pdata['seq'].",".$pdata['ac_id'];
            }
            $indata = array(
                "parent_id"=>$parent_id,
                "ac_name"=>$ac_name,
                "type"=>$type,
                "sort"=>$sort,
                "seq"=>$seq
            );
            $rs = $cateModel->insert($indata);
            if($rs === false){
                $this->showMsg("添加失败");
            }
            $this->showMsg("添加成功",'cate.html',3,1);
        }
        
        $cates = $cateModel->getList(array(1), "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,seq", "order by abs asc");
        $this->tvar = array(
            "cates"=>$cates
        );
        return $this->srender('article/cateadd.html',$this->tvar);
    }
    public function pageCateedit(){
        $this->isCanUse(9);
        $ac_id = $this->get("ac_id","int");
        $cateModel = new Model_ss_articlecate();
        if($_POST){
            $parent_id = $this->post('parent_id','int',0);
            $ac_name = $this->post('ac_name');
            $type = $this->post('type','int');
            $sort = $this->post('sort','int');
            $seq = "0";
            if($parent_id){
                $pdata = $cateModel->getOne(array('ac_id'=>$parent_id), "ac_id,seq,type");
                if($pdata['type'] != 1){
                    $this->showMsg("只有栏目才可新增下级");
                }
                $seq = $pdata['seq'].",".$pdata['ac_id'];
            }
            $condition = array();
            $condition['ac_id'] = $ac_id;
            $updata = array(
                "parent_id"=>$parent_id,
                "ac_name"=>$ac_name,
                "type"=>$type,
                "sort"=>$sort,
                "seq"=>$seq
            );
            $rs = $cateModel->update($condition, $updata);
            if($rs === false){
                $this->showMsg("编辑失败");
            }
            $this->showMsg("编辑成功",'cate.html',3,1);
        }
        $cates = $cateModel->getList(array(1), "concat(seq,',',ac_id) as abs,ac_id,ac_name,type,parent_id,sort,seq", "order by abs asc");
        $cate = array();
        foreach($cates as $val){
            if($val['ac_id'] == $ac_id){
                $cate = $val;
            }
        }
        $this->tvar = array(
            'cates'=>$cates,
            'cate'=>$cate
        );
        return $this->srender('article/cateedit.html',$this->tvar);
    }
    public function pageCatedel(){
        $this->isCanUse(9);
        $ac_id = $this->get("ac_id","int");
        $cateModel = new Model_ss_articlecate();
        //检查当前分类下是否有文章
        $data = $this->tModel->getOne(array("ac_id"=>$ac_id));
        if(!empty($data) && $data['a_id']>0){
            $this->showMsg("当前分类下有文章，不能删除");
        }
        $rs = $cateModel->delete(array("ac_id"=>$ac_id));
        if($rs === false){
            $this->showMsg("删除失败");
        }
        $this->showMsg("删除成功",'cate.html',3,1);
    }
}
