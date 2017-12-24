<?php
class Model_ss_userrdt extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'user_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_user_rdt';
	}
    
}

/**
	End file,Don't add ?> after this.
*/