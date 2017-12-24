<?php
class Model_ss_massuser extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_mass_user';
	}
    
    //列表
    function get_list($cond,$fields,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(!empty($cond['mass_id'])){
            $condition['mass_id'] = $cond['mass_id'];
        }
        if(!empty($cond['user_id'])){
            $condition['user_id'] = $cond['user_id'];
        }
        if(!empty($cond['real_name'])){
            $condition['real_name'] = $cond['real_name'];
        }
        if(!empty($cond['status'])){
            $condition['status'] = $cond['status'];
        }
        if(!empty($cond['phone'])){
            $condition['phone'] = $cond['phone'];
        }
        $rs['list'] = $this->getList($condition, $fields, $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
    
    //删除
    function del($id){
        if(empty($id)){
            $this->setError(-1, '参数错误');
            return false;
        }
        $rs = $this->delete(array('id'=>$id));
        if($rs === false){
            $this->setError(-1, '删除失败');
            return false;
        }
        return true;
    }
}

/**
	End file,Don't add ?> after this.
*/