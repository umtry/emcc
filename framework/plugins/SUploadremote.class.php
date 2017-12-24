<?php

/**
 *将本webserver接收到得文件上传到远端服务器
 * */
class SUploadremote {

    public static $host = SConstant::IMAGE_SERVER_IP;
    public static $port = 80;
    public static $method = 'POST';
    public static $path = '/upfiles/upfile.php';
	

	/**
	 * 上传头像
	 *
	 * @param array $_file 文件流
	 * @param array $uid 用户ID
	 * @return array 上传成功的路径 或错误信息
	 */
	public function uploadAvatar($_file, $uid) {
		$uid = abs(intval($uid));
		if(empty($_file['name'])) return array('error' => 1, 'msg' => "请选择要上传的文件!");
		if(empty($uid)) return array('error' => 1, 'msg' => "非法的uid!");
		
		$_file['name'] = strtolower($_file['name']);
		$ext = strtolower(end(explode(".", $_file['name'])));
		$avatarType = 'jpg,png,jpeg';
		$ary = explode(',', $avatarType);
		if (!in_array($ext, $ary))
			return array('status' => 0, 'error' => 1, 'msg' => "不允许上传的文件类型,上传文件类型只能为：" . $avatarType . "!");

		
		$sizelimit = 2097152;//2mb
		if ($_file['size'] > $sizelimit) {
			$fallowsize = round($sizelimit / 1024, 2) . 'kb' . ' 实际上传文件大小为：' . round($_file['size'] / 1024, 2) . 'kb';
			return array('status' => 0, 'error' => 1, 'msg' => "文件大小超出规定限制：{$fallowsize},请重新上传!");
		}
		if ($_file['error'] != UPLOAD_ERR_OK){
			switch ($_file['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$msg = '上传文件大小超出服务器最大限制。';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$msg = '上传文件大小超出Form指定的最大限制。';
					break;
				case UPLOAD_ERR_PARTIAL:
					$msg = '上传文件部分失败。';
					break;
				case UPLOAD_ERR_NO_FILE:
					$msg = '文件未上传成功。';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$msg = '服务器未指定临时文件夹。';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$msg = '上传文件写入磁盘失败。';
					break;
				case UPLOAD_ERR_EXTENSION:
					die('File upload stopped by extension.');
					break;
			}
			return array('status' => 0, 'error' => 1, 'msg' => $msg);
		} 
		
		//图片尺寸
		$query['size[0]'] = '80x80';
		$query['size[1]'] = '120x120';
		$query['size[2]'] = '150x150';
		//$query['size[3]'] = '400x400';
		//存储路径和文件名
		$pathArr = SUtil::get_avatar_filepath($uid, '', $retArr=1);
		$query['path']= $pathArr[0];
		$query['type'] = 1;  //为图片时填1,非图片文件填
		//唯一文件名由自己构造,不能使用上传文件的名字，有可能重复,可以使用自增id、时间截或随机等为文件名,后缀要准确0
		$query['name'] = $pathArr[1];
		$query['nomask'] = 1; //是否不加水印
		$result = self::postfile($query, $_file['name'], $_file['tmp_name'], $_file['type']);
		//$result = json_decode($result);
		if($result['state']!=0) return array('error' => 1 ,'status'=>0, 'msg' => $result['msg']);
		$imgurl = self::getfileurl($query['path'], $query['name']);
		return array('error' => 0, 'url' => $imgurl, 'size' => $_file['size'], 'type' => $ext);
	
	}

    public static function postfile($postvar, $file_name="", $tmp_name, $content_type) {
        error_reporting(0);
        
        $host = $host ? $host : self::$host;
        $method = self::$method;
        $path = '/upfiles/upfile.php';
        srand((double) microtime() * 1000000);
        $boundary = "---------------------" . substr(md5(rand(0, 32000)), 0, 10);

        // Build the header
        $header = "POST $path HTTP/1.0\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Content-type: multipart/form-data, boundary=$boundary\r\n";
        // attach post vars
        foreach ($postvar AS $index => $value) {
            $data .="--$boundary\r\n";
            $data .= "Content-Disposition: form-data; name=\"" . $index . "\"\r\n";
            $data .= "\r\n" . $value . "\r\n";
            $data .="--$boundary\r\n";
        }
//                echo $data;
//                exit;

        if ($tmp_name) {
            $data .= "--$boundary\r\n";
            $content_file = join("", file($tmp_name));
            $data .="Content-Disposition: form-data; name=\"userfile\"; filename=\"$file_name\"\r\n";
            $data .= "Content-Type: $content_type\r\n\r\n";
            $data .= "" . $content_file . "\r\n";
            $data .="--$boundary--\r\n";
        }
        //$data .="$postvar\r\n";
        $header .= "Content-length: " . strlen($data) . "\r\n\r\n";
        // Open the connection
        $fp = fsockopen($host, self::$port);
        // then just
        fputs($fp, $header . $data);
		//header
		$line = fgets($fp,1024);
		if (!@eregi("^HTTP/1\.. 200", $line)) return substr($line,9,3);
		$results = "";
		$inheader = 1;
		while(!feof($fp)){
			$line = fgets($fp,1024);
			if($inheader && ($line == "\r\n" || $line == "\r\r\n")){
				$inheader = 0;
			}elseif(!$inheader){
				$results .= $line;
			}
		}
        fclose($fp);
		//var_dump($results);exit();
		return $results;
    }
    protected static function set($type, $key, $name, $value) {
        return null;
    }

    protected static function get($type, $key, $name) {
        return null;
    }

    protected static function del($type, $key, $name) {
        return null;
    }

    public static function getfileurl($path, $name) {
        $url = "/{$path}$name";
        return $url;
    }

    protected static function getFilename($group, $id) {
        return null;
    }

    public static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function setStore($store) {
        self::$store = $store;
    }

}

/**
	End file,Don't add ?> after this.
*/
