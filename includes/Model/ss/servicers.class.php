<?php
class Model_ss_servicers extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_service_rs';
	}
    //列表
    function get_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $condition = array(1);
        if(isset($cond['sid'])){
            $condition['sid'] = $cond['sid'];
        }
        $rs['list'] = $this->getList($condition, '*', $orderby, $limit, $page);
        if($ispage){
            $rs['count'] = $this->getCount($condition);
        }
        return $rs;
    }
}

/**
	End file,Don't add ?> after this.
*/