<?php

class Model_ss_pay extends Model_basemodel {

    //@override
    public function setPrimaryKey() {
        $this->_primaryKey = 'pay_id';
    }

    //@override
    public function setTableName() {
        $this->_tableName = 'ss_pay';
    }

}

/**
	End file,Don't add ?> after this.
*/