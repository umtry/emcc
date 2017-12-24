<?php
class Model_ss_license extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'license_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_license';
	}
    
}

/**
	End file,Don't add ?> after this.
*/