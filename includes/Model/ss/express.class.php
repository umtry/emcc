<?php
class Model_ss_express extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'ex_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_express';
	}
    
    //列表
    function get_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['user_id'])){
            $condition['user_id'] = $cond['user_id'];
        }
        if(!empty($cond['real_name'])){
            $condition['real_name'] = $cond['real_name'];
        }
        if(!empty($cond['ex_type'])){
            $condition['ex_type'] = $cond['ex_type'];
        }
        if(!empty($cond['ex_number'])){
            $condition['ex_number'] = $cond['ex_number'];
        }
        if(!empty($cond['answer_state'])){
            $condition['answer_state'] = $cond['answer_state'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
        }
        if(!empty($cond['stime'])){
            $condition[] = "add_time>".$cond['stime'];
        }
        if(!empty($cond['etime'])){
            $condition[] = "add_time<=".$cond['etime'];
        }
        $rs['list'] = $this->getList($condition, '*', $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
    
    //新增、编辑(后台操作)
    function edit($params, $ex_id=0){
        $updata = array(
            'ex_type' => $params['ex_type'],
            'ex_number' => $params['ex_number'],
            'user_id' => $params['user_id'],
            'real_name' => $params['real_name'],
            'answer_state' => $params['answer_state'],
            'content' => $params['content'],
            'price' => $params['price'],
            'pay_status' => $params['pay_status']
        );
        
        if($ex_id>0){
            $rs = $this->update(array('ex_id'=>$ex_id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            if($params['pay_status']==2){
                $updata['pay_time'] = $this->_time;
            }
            $updata['status'] = $params['status'];
            if($params['status']==2){
                $updata['comp_time'] = $this->_time;
            }
            $rs = $this->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }
    
}

/**
	End file,Don't add ?> after this.
*/