<?php
class Service_baseservice{
    protected $_error;
	protected $_time;

    function __construct() {
        $this->_time = time();
		$this->_error = array();
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
}