<?php
class Model_ss_article extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'a_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_article';
	}
    
}

/**
	End file,Don't add ?> after this.
*/