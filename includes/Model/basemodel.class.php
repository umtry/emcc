<?php
/**
 参数备忘	select($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query'))
			selectOne($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query'))
			update($table,$condition="",$item="",$params=array('type'=>'main'))
			delete($table,$condition="",$params=array('type'=>'main'))
			insert($table,$item="",$isreplace=false,$isdelayed=false,$update=array(),$params=array('type'=>'main'))
			query($table,$sql,$bind1=array(),$bind2=array(),$params=array())
 */
abstract class Model_basemodel {
    protected $_time;
    protected $_primaryKey;
    protected $_tableName;
    protected $_error = array();
	/**
     * 设置主健名称,该方法必需被子类覆盖
     */
    protected abstract function setPrimaryKey();
    /**
     * 设置当前表名,该方法必需被子类覆盖
     */
    protected abstract function setTableName();
    function __construct() {
        SDb::setConfigFile(ROOT_CONFIG."/db.ini.php");
        $this->_db = SDb::getDbEngine("pdo_mysql");   
        $this->_time = time();
        $this->setPrimaryKey();
        $this->setTableName();
    }
    public function disconnect() {
    	$this->_db = null;
        unset($this->_db);
    }
    public function __destruct() {
    	$this->_db = null;
        unset($this->_db);
    }
    /**
     * 打印sql错误 调试时使用
     *
     */
    public function debug() {
        echo $this->_db->sql;
        echo "<br/>";
        print_r($this->_db->error);
    }
    /**
     * 写入错误信息
     * @param int $code
     * @param string $msg
     */
    protected function setError($code=0, $msg='') {
        $this->_error["code"] = $code;
        $this->_error["msg"] = $msg;
    }
    /**
     * 获取错误信息
     * @param string $type
     */
    public function getError($type="msg") {
        return $this->_error[$type];
    }
    /**
     * 获取一行数据
     * @param array|string $condition 条件
     * @param array|string $item 查询字段
	 * @return array|false 数据 或失败
     */
    public function getOne($condition='', $item='*') {
        return $this->_db->selectOne($this->_tableName, $condition, $item);
    }
	/**
     * 获取列表
     * @param array|string $condition 条件
     * @param array|string $item 查询字段
	 * @param array|string $order 排序
	 * @param int $limit 限制查询条数
	 * @param int $page 页数
	 * @return array|false 数据 或失败
     */
    public function getList($condition, $item='*', $order = '', $limit = 0, $page = 1, $leftjoin="") {
		if(!$condition){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$this->_db->setPage($page);
		$this->_db->setLimit($limit);
        return $this->_db->select($this->_tableName, $condition, $item, $groupby='', $order, $leftjoin)->items;
    }
	/**
	* 获取行数
	* @param int $page 页数
	* @return int|false 行数 或 失败
	*/
	public function getCount($condition='',$groupby = ''){
		$data = $this->_db->selectOne($this->_tableName, $condition, 'count(*) as tablesize',$groupby);
        return $data['tablesize'];
	}
	 /**
     * update
     * @param array|string $condition 更新条件
	 * @param array $data 更新内容
	 * @return int|false 更新行数 或 失败
     */
    public function update($condition,$data) {
		if(empty($condition) || empty($data)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
        return $this->_db->update($this->_tableName, $condition, $data);
    }
	 /**
     * insert
     * @param array $data	欲插入的数据
	 * @return int|false	新插入行的id 或 失败
     */
    public function insert($data,$isreplace=false) {
		if(empty($data)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
        return $this->_db->insert($this->_tableName, $data,$isreplace);
    }
    /**
     * delete
     * @param array|string $condition 更新条件
	 * @return int|false 删除行数 或 失败
     */
    public function delete($condition) {
		if(empty($condition)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
        return $this->_db->delete($this->_tableName, $condition);
    }
	/**
	 * 执行sql语句
	 * @param <string> $table 表名
	 * @param <string> $sql  sql语句
	 */
	public function query($table,$sql,$params=array('type'=>'main')){
		return $this->_db->query($table,$sql,array(),array(),$params);
	}
    
    /**
     * 解析where条件
     * @param array|string $condition 条件
     * @param string $split 分隔符
     * @return string 分割后的条件语句
     */
    public function quote($condition,$split="AND"){
        return $this->_db->__quote($condition,$split,$bind);
    }
}

/**
	End file,Don't add ?> after this.
*/
