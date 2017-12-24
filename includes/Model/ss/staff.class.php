<?php
class Model_ss_staff extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'stf_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_staff';
	}
    
    //列表
    function get_list($cond,$orderby,$fields='*',$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['stf_id'])){
            $condition['stf_id'] = $cond['stf_id'];
        }
        if(!empty($cond['stf_name'])){
            $condition['stf_name'] = $cond['stf_name'];
        }
        if(!empty($cond['real_name'])){
            $condition['real_name'] = $cond['real_name'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
        }
        
        $rs['list'] = $this->getList($condition, $fields, $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
    //新增、编辑(后台操作)
    function edit($params, $stf_id=0){
        $updata = array(
            'real_name' => $params['real_name'],
            'service' => $params['service'],
            'phone' => $params['phone'],
            'address' => $params['address'],
            'status' => $params['status']
        );
        
        if($stf_id>0){
            $rs = $this->update(array('stf_id'=>$stf_id), $updata);
        }else{
            $updata['stf_name'] = $params['stf_name'];
            $updata['add_time'] = $this->_time;
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