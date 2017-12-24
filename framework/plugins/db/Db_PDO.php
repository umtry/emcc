<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

/**
 * @package SlightPHP
 * @subpackage SDb
 */
class Db_PDO extends DbObject{
	//private $mysql;

	/**
	 *
	 */
	public $host;
	/**
	 *
	 */
	public $port=3306;
	/**
	 *
	 */
	public $user;
	/**
	 *
	 */
	public $password;
	/**
	 *
	 */
	public $database;
	/**
	 *
	 */
	public $charset;
	/**
	 *
	 */
	public $orderby;
	/**
	 *
	 */
	public $groupby;
	/**
	 *
	 */
	public $sql;
	/**
	 *
	 */
	public $count=false;
	/**
	 *
	 */
	public $limit=0;
	/**
	 *
	 */
	public $page=1;
	/**
	 *
	 */
    public $dbInfo;
    private $prefix;
	private $countsql;
	private $affectedRows=0;
	/**
	 *
	 */
	public $error=array('code'=>0,'msg'=>"");
	/**
	 * @var array $globals
	 */
	static $globals;
	function __construct($prefix="mysql"){
		$this->prefix=$prefix;
	}
	/**
	 * construct
	 *
	 * @param string host
	 * @param string user
	 * @param string password
	 * @param string database
	 * @param int port=3306
	 */
	function init($params=array(),$table=''){
        $dbInfo = '';
        if(!empty($this->dbInfo)) {
            $dbInfo = $this->dbInfo;
        }else{
            $pary = explode('_',$table);
            $pri = $pary[0];
            switch($pri) {
                case 'ss':
                    $dbInfo = 'sscms';
                    break;
            }
        }
        if(!empty($params['type'])) {
            $type = $params['type'];
        }else{
            $type = 'main';
        }
        $dbConfig = SDb::getConfig($dbInfo,$type);
        if(!empty($dbConfig)) {
            foreach($dbConfig as $key=>$value){
                $this->$key = $value;
            }
            $this->key = $this->prefix.":".$this->host.":".$this->user.":".$type.":".$pary[0];
            if(!isset(Db_PDO::$globals[$this->key])) Db_PDO::$globals[$this->key] = "";
        }
	}
	/**
	 * is count
	 *
	 * @param boolean count
	 */
	function setCount($count){
		if($count==true){
			$this->count=true;
		}else{
			$this->count=false;
		}
	}
    function setDbinfo($dbinfo=''){
        $this->dbInfo = $dbinfo;
    }
	/**
	 * page number
	 *
	 * @param int page
	 */
	function setPage($page){
		if(!is_numeric($page) || $page<1){$page=1;}
		$this->page=$page;
	}
	/**
	 * page size
	 *
	 * @param int limit ,0 is all
	 */
	function setLimit($limit){
		if(!is_numeric($limit) || $limit<0){$limit=0;}
		$this->limit=$limit;
	}
	/**
	 * group by sql
	 *
	 * @param string groupby
	 * eg:	setGroupby("groupby MusicID");
	 *      setGroupby("groupby MusicID,MusicName");
	 */
	function setGroupby($groupby){
		$this->groupby=$groupby;
	}
	/**
	 * order by sql
	 *
	 * @param string orderby
	 * eg:	setOrderby("order by MusicID Desc");
	 */
	function setOrderby($orderby){
		$this->orderby=$orderby;
	}

	/**
	 * select data from db
	 *
	 * @param mixed $table
	 * @param array $condition
	 * @param array $item
	 * @param string $groupby
	 * @param string $orderby
	 * @param string $leftjoin
	 * @return DbData object || Boolean false
	 */
	function select($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query')){
		//{{{$item
		if($item==""){$item="*";}
		if(is_array($table)){
			for($i=0;$i<count($table);$i++)
			{
				$tmp[]=trim($table[$i]);
			}
			$table=@implode(" , ",$tmp);
		}else{
			$table=trim($table);
		}

		if(is_array($item)&&!empty($item)){
			$item =@implode(",",$item);
		}
		//}}}
		//{{{$condition
		$condiStr = $this->__quote($condition,"AND",$bind);

		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}
		//}}}
		//{{{
		$join="";
		if(is_array($leftjoin)){
			foreach ($leftjoin as $key=>$value){
				$join.=" LEFT JOIN $key ON $value ";
			}
		}
		//}}}
		//{{{
		$this->groupby  =$groupby!=""?$groupby:$this->groupby;
		$this->orderby  =$orderby!=""?$orderby:$this->orderby;

		$orderby_sql="";
		$orderby_sql_tmp = array();
		if(is_array($orderby)){
			foreach($orderby as $key=>$value){
				if(!is_numeric($key)){
					$orderby_sql_tmp[]=$key." ".$value;
				}
			}
		}else{
			$orderby_sql=$this->orderby;
		}
		if(count($orderby_sql_tmp)>0){
			$orderby_sql=" ORDER BY ".implode(",",$orderby_sql_tmp);
		}
		//}}}

		$limit="";
		if($this->limit!=0){
			$limit    =($this->page-1)*$this->limit;
			$limit ="LIMIT $limit,$this->limit";
		}
		$this->sql="SELECT $item FROM $table $join $condiStr $groupby $orderby_sql $limit";
		//print $this->sql;
		if($groupby!=''){
			$this->countsql="SELECT count(1) totalSize FROM (SELECT 1 FROM $table $join $condiStr $groupby) ss";
		}else{
			$this->countsql="SELECT count(1) totalSize FROM $table $join $condiStr $groupby";
		}

		$data = new DbData;

		$data->page = $this->page;
		$data->limit = $this->limit;
		$start = microtime(true);


		$data->limit = $this->limit;
		//print_r($this->sql);
		$data->items = $this->query($table,$this->sql,$bind,null,$params);
		if($data->items === false){
			return false;
		}
		$data->pageSize = count($data->items);
		$end = microtime(true);
		$data->totalSecond = $end-$start;

		//}}}


		//{{{
		if($this->limit !=0 and $this->count==true and $this->countsql!=""){
			$result_count = $this->query($table,$this->countsql,$bind,null,$params);
			$data->totalSize = $result_count[0]['totalSize'];
			$data->totalPage = ceil($data->totalSize/$data->limit);
		}
		//}}}

		//���page,limit������
		$this->setCount(false);
		$this->setPage(1);
		$this->setLimit(0);
		$this->setGroupby("");
		$this->setOrderby("");

		return $data;
	}
	/**
	 *
	 *
	 * @param mixed $table
	 * @param array $condition
	 * @param array $item
	 * @param string $groupby
	 * @param string $orderby
	 * @param string $leftjoin
	 * @return array item
	 */
	function selectOne($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query')){
		$this->setLimit(1);
		$this->setCount(false);
		$data=$this->select($table,$condition,$item,$groupby,$orderby,$leftjoin,$params);
		if(isset($data->items[0]))
		return $data->items[0];
		else return false;

	}

	/**
	 * update data
	 *
	 * @param mixed $table
	 * @param string,array $condition
	 * @param array $item
	 * @param int $limit
	 * @return int
	 * update("table",array('name'=>'myName','password'=>'myPass'),array('id'=>1));
	 * update("table",array('name'=>'myName','password'=>'myPass'),array("password=$myPass"));
	 */
	function update($table,$condition="",$item="",$params=array('type'=>'main')){
		$value = $this->__quote($item,",",$bind_v);
		$condiStr = $this->__quote($condition,"AND",$bind_c);
		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}
		$this->sql="UPDATE $table SET $value $condiStr";
		if($this->query($table,$this->sql,$bind_v,$bind_c,$params)!==false){
			return $this->rowCount();
		}else{
			return false;
		}
	}
	/**
	 * delete
	 *
	 * @param mixed table
	 * @param string,array $condition
	 * @param int $limit
	 * @return int
	 * delete("table",array('name'=>'myName','password'=>'myPass'),array('id'=>1));
	 * delete("table",array('name'=>'myName','password'=>'myPass'),array("password=$myPass"));
	 */
	function delete($table,$condition="",$params=array('type'=>'main')){
		$condiStr = $this->__quote($condition,"AND",$bind);
		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}
		$this->sql="DELETE FROM  $table $condiStr";
		if($this->query($table,$this->sql,$bind,null,$params)!==false){
			return $this->rowCount();
		}else{
			return false;
		}
	}
	/**
	 * insert
	 *
	 * @param $table
	 * @param array $item
	 * @param array $update ,egarray("key"=>value,"key2"=>value2")
		 insert into zone_user_online values(2,'','','','',now(),now()) on duplicate key update onlineactivetime=CURRENT_TIMESTAMP;
	 * @return int InsertID
	 */
	function insert($table,$item="",$isreplace=false,$isdelayed=false,$update=array(),$params=array('type'=>'main')){
		if($isreplace==true){
			$command="REPLACE";
		}else{
			$command="INSERT";
		}
		if($isdelayed==true){
			$command.=" DELAYED ";
		}

		$f = $this->__quote($item,",",$bind_f);

		$this->sql="$command INTO $table SET $f ";
		$v = $this->__quote($update,",",$bind_v);
		if(!empty($v)){
			$this->sql.="ON DUPLICATE KEY UPDATE $v";
		}
		$r=$this->query($table,$this->sql,$bind_f,$bind_v,$params);
		if($r!==false){
			if($this->lastInsertId ()>0){
				return $this->lastInsertId ();
			}elseif($this->affectedRows >0){
				return $this->affectedRows;
			}
		}
		return $r;
	}

	/**
	 *excle�
	 */
	function excel_query($table,$sql,$title=array(),$filename='',$params=array('type'=>'main')){

		if(defined("DEBUG")){
			$time=time();
			echo "<br/>\r\n(time:{$time})SQL:$sql\n";
			print_r($bind1);
			print_r($bind2);
		}

		if(empty($filename)){
			$filename = date('Y-m-d');
		}

		$stmt = $this->getPDO($table,$params)->prepare($sql);
		if(!$stmt){
			$this->error['code']=Db_PDO::$globals[$this->key]->errorCode ();
			$this->error['msg']=Db_PDO::$globals[$this->key]->errorInfo ();
			return false;
		}

		if($stmt->execute()){
			$this->affectedRows = $stmt->rowCount();

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			$fp = fopen('php://output', 'a');
			$head = $title;
			foreach ($head as $i => $v) {
				$head[$i] = iconv('utf-8', 'gbk', $v);
			}
			fputcsv($fp, $head);
			$cnt = 0;
			$limit = 10000;
			while($row = $stmt->fetch(PDO::FETCH_NUM)) {
				$cnt ++;
				if ($limit == $cnt) {
					ob_flush();
					flush();
					$cnt = 0;
				}
				foreach ($row as $i => $v) {
					$row[$i] = iconv('utf-8', 'gbk', $v);
				}
				fputcsv($fp, $row);
			}
		}else{
			$this->error['code']=$stmt->errorCode ();
			$this->error['msg']=$stmt->errorInfo ();

			if(defined("DEBUG")||1){
					print_r($this->error['msg']);
			}
		}
		unset($head,$title);
		return;
	}

	function excel_query2($table,$sql,$excel_title,$cell_length,$ctitle_array,$text_key=false,$params=array('type'=>'main')){
		$stmt = $this->getPDO($table,$params)->prepare($sql);
		if($stmt->execute()){
			require_once(PLUGINS_DIR . "/phpexcel/PHPExcel.php");
			require_once(PLUGINS_DIR . "/phpexcel/PHPExcel/Writer/Excel5.php");

			/**
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
			$cacheSettings = array( 'memcacheServer'=> SConstant::MEMCACHEHOST,
									'memcachePort' => SConstant::MEMCACHEPORT,
									'cacheTime' => 600 );

			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			*/

			$obj_excel = new PHPExcel();
			$obj_write = new PHPExcel_Writer_Excel5($obj_excel);

			$ua = $_SERVER["HTTP_USER_AGENT"];
			header("Pragma: public");
			header("Expires: 0");
			header('Content-Transfer-Encoding: utf-8');
			header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
			header("Content-Type:application/force-download");
			header("Content-Type:application/vnd.ms-execl");
			header("Content-Type:application/octet-stream");
			header("Content-Type:application/download");
			if(strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")){
				header('Content-Disposition:attachment;filename="'.(urlencode($excel_title)).'.xls"');
			}else{
				header('Content-Disposition:attachment;filename="'.$excel_title.'.xls"');
			}
			header("Content-Transfer-Encoding:binary");

			$obj_properity = $obj_excel->getProperties();
			$obj_properity->setTitle($excel_title);

			$obj_excel->setActiveSheetIndex(0);
			$obj_sheet = $obj_excel->getActiveSheet();
			$obj_sheet->setTitle($excel_title.'1');

			$cell_array = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			foreach($cell_array as $key=>$val){
				if($key < $cell_length){
					$obj_sheet->getColumnDimension($val)->setWidth(20);
					$obj_sheet->setCellValue($val.'1',$ctitle_array[$key]);
				}
			}

			$i =2;
			$index = 0;
			$num = 1;
			$limit = 20000;
			while($val = $stmt->fetch(PDO::FETCH_NUM)) {
				if($index%$limit==0 && $index!=0){
					$i =2;
					ob_flush();
					flush();

					$obj_excel->createSheet($num);
					$obj_excel->setActiveSheetIndex($num);
					$obj_sheet = $obj_excel->getActiveSheet();
					$obj_sheet->setTitle($excel_title.($num+1));

					foreach($cell_array as $key1=>$val1){
						if($key1 < $cell_length){
							$obj_sheet->getColumnDimension($val1)->setWidth(20);
							$obj_sheet->setCellValue($val1.'1',$ctitle_array[$key1]);
						}
					}
					$num++;
				}

				foreach($cell_array as $k=>$v){
					if($k >= $cell_length){
						break;
					}
					if(false == $text_key){
						$obj_sheet->setCellValue($v.$i,$val[$k]);
						continue;
					}
					if(!in_array($k,$text_key)){
						$obj_sheet->setCellValue($v.$i,$val[$k]);
						continue;
					}
					$obj_sheet->setCellValueExplicit($v.$i,$val[$k]);
				}
				$i++;
				$index++;
				//$obj_excel->Destroy();
			}
		}

		//��������
        //$file = SConstant::IMAGE_URL.'/excel/1.xls';
        //$obj_write->save($file);

	 	$obj_write->save("php://output");
        return false;
	}

	/**
	 * query
	 *
	 * @param string $sql
	 * @return Array $result  || Boolean false
	 */

	function query($table,$sql,$bind1=array(),$bind2=array(),$params=array('type'=>'main')){
		if(defined("DEBUG")){
			$time=time();
			echo "<br/>\r\n(time:{$time})SQL:$sql\n";
			print_r($bind1);
			print_r($bind2);
		}

		//echo $sql;

		$stmt = $this->getPDO($table,$params)->prepare($sql);

		if(!$stmt){

			$this->error['code']=Db_PDO::$globals[$this->key]->errorCode ();
			$this->error['msg']=Db_PDO::$globals[$this->key]->errorInfo ();
			return false;
		}
		if(!empty($bind1)){
			foreach($bind1 as $k=>$v){
				$stmt->bindValue($k,$v);
			}
		}

		if(!empty($bind2)){
			foreach($bind2 as $k=>$v){
				$stmt->bindValue($k + count($bind1),$v);
			}
		}
		if($stmt->execute ()){
			$this->affectedRows = $stmt->rowCount();
			return $stmt->fetchAll (PDO::FETCH_ASSOC );
		}else{
			$this->error['code']=$stmt->errorCode ();
			$this->error['msg']=$stmt->errorInfo ();

			if(defined("DEBUG")||1){
					print_r($this->error['msg']);
				}
		}
		return false;

	}
	function lastInsertId(){
		return Db_PDO::$globals[$this->key]->lastInsertId ();
	}
	function rowCount(){
		return $this->affectedRows;
	}

	/**
	 *
	 * @param string $sql
	 * @return array $data || Boolean false
	 */
	function execute($sql,$table=''){
		return $this->query($table,$sql);
	}

	function __connect($forceReconnect=false){
		if(empty(Db_PDO::$globals[$this->key]) || $forceReconnect){
			if(!empty(Db_PDO::$globals[$this->key])){
				unset(Db_PDO::$globals[$this->key]);
			}
			try{
				Db_PDO::$globals[$this->key] = new PDO($this->prefix.":dbname=".$this->database.";host=".$this->host.";port=".$this->port,$this->user,$this->password);
			}catch(Exception $e){
				//die("connect database error:\n".var_export($this,true));
				die("connect database error :" .$this->database);
			}
		}
		if(!empty($this->charset)){
			$this->execute("SET NAMES ".$this->charset);
		}
	}

	function __quote($condition,$split="AND",&$bind){
		$condiStr = "";
		if(!is_array($bind)){$bind=array();}
		if(is_array($condition)){
			$v1=array();
			$i=1;
			foreach($condition as $k=>$v){
				if(!is_numeric($k)){
					if(strpos($k,".")>0){
						$v1[]="$k = ?";
					}else{
						$v1[]="`$k` = ?";
					}
					$bind[$i++]=$v;
				}else{
					$v1[]=($v);
				}
			}
			if(count($v1)>0){
				$condiStr=implode(" ".$split." ",$v1);

			}
		}else{
			$condiStr=$condition;
		}
		return $condiStr;
	}

	public function getPDO($table,$params=array('type'=>'query')){
		if(empty($params['dbinfo'])) {
			$this->init($params,$table);
			if(empty(Db_PDO::$globals[$this->key])){
				$this->__connect($forceReconnect=true);
			}
		} else {
			$this->init($params,$table);
			$this->__connect($forceReconnect=true);
		}

		return Db_PDO::$globals[$this->key];
	}
	public function beginTransaction($table,$params=array('type'=>'main')){
		return $this->getPDO($table,$params)->beginTransaction();
	}
	public function commit($table,$params=array('type'=>'main')){
		return $this->getPDO($table,$params)->commit();
	}
	public function rollBack($table,$params=array('type'=>'main')){
		return $this->getPDO($table,$params)->rollBack();
	}
}
?>
