<?php
class Model_ss_settings extends Model_basemodel{

	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'key';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'ss_settings';
	}
    
    // 设置
    public function setting($data,$setg){
        foreach($data as $key=>$val){
            $data1['key'] = $key;
            $data1['value'] = is_array($val) ? serialize($val) : $val;
            $data1['setg'] = $setg;
            $this->insert($data1,true);    
        }  
        $tdata = $this->getSettings($setg,true);
        SUtil::fileCacheSet($setg,$tdata); 
        return true;
    }
    // 取值
    public function getSettings($setg,$reload=false){
        $tdata = SUtil::fileCacheGet($setg);
        if(!SUtil::isEmptyArr($tdata) && $reload==false){
            return $tdata;
        }
        
        $data = $this->getList("setg='{$setg}'",'*');
        $rarray = array();
        foreach($data as $key=>$val){
            $t = @unserialize($val['value']);
            $t = is_array($t) ? $t : $val['value'];
            $rarray[$val['setg']][$val['key']] = $t;    
        }
        
        return $rarray;
    }

}

/**
	End file,Don't add ?> after this.
*/