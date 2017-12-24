<?php
class Model_ss_group extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'group_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_group';
	}
    
}

/**
	End file,Don't add ?> after this.
*/