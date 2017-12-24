<?php
/**
* @author fenngle
* @time 2014-08-11 
*/
class Controller_ajax extends Controller_basepage {
    public function __construct(){
        parent::__construct();
        $this->tvar = array();
    }
    //图片热点
    function pageHotnotes(){
        //$this->isLogin(true);
        
        $img_url = $this->ajpost('image','string','',false);
        $id = $this->ajpost('id','int',0);
        $author = $this->ajpost('author','string','',false);
        $link = $this->ajpost('link','string','',false);
        $position = $this->ajpost('position','string','',false);
        $note = $this->ajpost('note','string','',false);
        
        $get = $this->ajpost('get','string','',false);
        $add = $this->ajpost('add','string','',false);
        $delete = $this->ajpost('delete','string','',false);
        $edit = $this->ajpost('edit','string','',false);
        
        $hotcommModel = new Model_ss_hotcomm();
        
        if($add){
            $indata = array(
                "user_id"=>1,
                "user_name"=>"fenngle",
                "img_url"=>$img_url,
                "author"=>$author,
                "link"=>$link,
                "position"=>$position,
                "note"=>$note,
                "add_time"=>$this->_time,
                "edit_time"=>$this->_time
            );
            $rs = $hotcommModel->insert($indata);
            $this->printJson(true);
        }elseif($edit){
            
        }elseif($delete){
            
        }else{
            $condition = array();
            if(1){
                $condition[] = " status in(0,1) ";
            }else{
                $condition["status"] = 1;
            }
            
            $hcs = $hotcommModel->getList($condition);
            foreach($hcs as $key=>$val){
                $position = explode(',', $val['position']);
                if (count($position) != 4){
                    continue;
                }
                $newNotes[$key] = array(
                    'ID' => $val['id'],
                    'LEFT' => $position[0],
                    'TOP' => $position[1],
                    'WIDTH' => $position[2],
                    'HEIGHT' => $position[3],
                    'DATE' => date("Y-m-d H:i",$val['edit_time']),
                    'NOTE' => $val['note'],
                    'AUTHOR' => $val['author'],
                    'LINK' => $val['link']
                );
            }
            
            $this->printJson($newNotes);
        }
    }
    
}
