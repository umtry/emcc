<?php
class Model_ss_sysnav extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'sysnav_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_sysnav';
	}
    
}

/**
	End file,Don't add ?> after this.
*/