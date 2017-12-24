<?php
class Model_ss_articlecate extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'ac_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_articlecate';
	}
    
}

/**
	End file,Don't add ?> after this.
*/