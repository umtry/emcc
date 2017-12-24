<?php
class Model_ss_links extends Model_basemodel{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_links';
	}
}

/**
	End file,Don't add ?> after this.
*/