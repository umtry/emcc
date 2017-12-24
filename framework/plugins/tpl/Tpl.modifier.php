<?php
/*
 * 通用
 */
function tpl_modifier_tostring($mixed){
	return var_export($mixed,true);
}
function tpl_modifier_tr($string,$zone="template"){
	return SLanguage::tr($string,$zone);
}
function tpl_modifier_default($input,$default=""){
	return empty($input)?$default:$input;
}
function tpl_modifier_version($string,$version="1.0") {
    return $string."?".$version;
}
function tpl_modifier_urlencode($string){
    return urlencode($string);
}
function tpl_modifier_urldecode($string){
    return urldecode($string);
}
function tpl_modifier_base64decode($string){
    return base64_decode($string);
}
function tpl_modifier_round($string){
    return round($string);
}
function tpl_modifier_replace($string,$search,$replace){
    return str_replace($search, $replace, $string);
}
function tpl_modifier_substrcount($string,$search){
    return substr_count($string, $search);
}
//循环空格
function tpl_modifier_forwhat($string,$replace="&nbsp;&nbsp;&nbsp;"){
    $c = intval($string);
    if(empty($c)){
        return "";
    }
    $re = "";
    for($i=0;$i<=$c;$i++){
        $re .= $replace;
    }
    return $re;
}

function tpl_modifier_substr($string,$length=80,$etc='',$code='UTF-8'){
    if($length == 0)
        return '';
    if($code == 'UTF-8'){
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
    }else{
        $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
    }
    preg_match_all($pa, $string, $t_string);
    if (count($t_string[0]) > $length){
        $retstr = join('', array_slice($t_string[0], 0, $length)) . $etc;
        $retstr = str_replace ( chr(10), "<BR>", $retstr );
        $retstr = str_replace ( chr(13), "<BR>", $retstr );
        return $retstr;
    }
    $retstr = join('', array_slice($t_string[0], 0, $length));
    $retstr = str_replace ( chr(10), "<BR>", $retstr );
    $retstr = str_replace ( chr(13), "<BR>", $retstr );
    return $retstr;
}

//格式化时间戳
function smarty_make_timestamp($string){
    if(empty($string)) {
        // use "now":
        return time();
    } elseif ($string instanceof DateTime) {
        return $string->getTimestamp();
    } elseif (preg_match('/^\d{14}$/', $string)) {
        // it is mysql timestamp format of YYYYMMDDHHMMSS?            
        return mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
                       substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
    } elseif (is_numeric($string)) {
        // it is a numeric string, we handle it as timestamp
        return (int)$string;
    } else {
        // strtotime should handle it
        $time = strtotime($string);
        if ($time == -1 || $time === false) {
            // strtotime() was not able to parse $string, use "now":
            return time();
        }
        return $time;
    }
}
function tpl_modifier_dateformat($string, $format = 'Y-m-d H:i:s', $default_date = '',$formatter='auto') {
    if ($string != '' && $string != 0) {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    } 
    if($formatter=='strftime'||($formatter=='auto'&&strpos($format,'%')!==false)) {
        if (DS == '\\') {
            $_win_from = array('%D', '%h', '%n', '%r', '%R', '%t', '%T');
            $_win_to = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
            if (strpos($format, '%e') !== false) {
                $_win_from[] = '%e';
                $_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
            } 
            if (strpos($format, '%l') !== false) {
                $_win_from[] = '%l';
                $_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
            } 
            $format = str_replace($_win_from, $_win_to, $format);
        } 
        return strftime($format, $timestamp);
    } else {
        return date($format, $timestamp);
    }
}

//格式化时间 秒转分
function tpl_modifier_sec2his($string){
    if(empty($string))
        return '';
    $time = intval($string);
    if(is_numeric($time)){
        $value = array(
            "hours"=>"00","minutes"=>"00", "seconds"=>"00"
        );
	if($time >= 3600){
        $value["hours"] = floor($time/3600);
        $time = ($time%3600);
	}
	if($time >= 60){
        $value["minutes"] = floor($time/60);
        $time = ($time%60);
	}
	$value["seconds"] = floor($time);
        if($value["seconds"]<10){
            $value["seconds"] = "0".$value["seconds"];
        }
        if($value["hours"]>"00"){
            $rs = $value["hours"].":".$value["minutes"].":".$value["seconds"];
        }else{
            $rs = $value["minutes"].":".$value["seconds"];
        }
        return $rs;
    }else{
        return false;
    }
}


/*
 * 业务
 */
//后台url补全
function tpl_modifier_compurl($string){
    return SConstant::SSADMIN.$string;
}
//状态id转名称
function tpl_modifier_status($string){
    if($string === ""){
        return '';
    }
    switch ($string) {
        case 0:
            $status = "<span class='label label-sm label-warning'>禁止</span>";
            break;
        case 1:
            $status = "<span class='label label-sm label-success'>正常</span>";
            break;
        default:
            $status = "";
            break;
    }
    return $status;
}
//性别id转名称
function tpl_modifier_sex($string){
    if($string === ""){
        return '未知';
    }
    switch ($string) {
        case 1:
            $sex = "男";
            break;
        case 2:
            $sex = "女";
            break;
        default:
            $sex = "未知";
            break;
    }
    return $sex;
}
//性别id转名称
function tpl_modifier_catetype($string){
    if($string === ""){
        return '未知';
    }
    switch ($string) {
        case 1:
            $type = "<span class='badge badge-info'>栏目</span>";
            break;
        case 2:
            $type = "分类";
            break;
        case 3:
            $type = "单页";
            break;
        default:
            $type = "未知";
            break;
    }
    return $type;
}

//得到图片完整路径(待完善)
/**
* 生成缩略图片
* 
* @param mixed $string  图片地址
* @param mixed $size    min 50X50 mid 200X200 mid2 400x400 max 800X800 source 原图
* @param mixed $cut     
*/
function tpl_modifier_picsize($string,$size='',$cut=''){
    $size_str = '';
    switch($size){
        case 'min':
            $size_str = '50x50';
            break;
        case 'mid':
            $size_str = '200x200';
            break;
        case 'mid2':
            $size_str = '400x400';
            break;
        case 'max':
            $size_str = '800x800';
            break;
        case 'source':
            $size_str = '';
            break;
        default:
            $size_str = $size;
            break;
    }
    if($size_str){
        $size_arr = explode('X',strtoupper($size_str));
        $cut = $cut ? '-'.$cut : '';
        $new_url = SConstant::UPLOAD_URL.'/thumb'.$string.'-'.$size_arr[0].'-'.$size_arr[1].$cut.'.jpg';
    }else{
	    $new_url = SConstant::UPLOAD_URL.$string;
    }
    return $new_url;
}


/*
 * 广告
 */
//通过广告位id取得广告内容
function tpl_modifier_ads($string){
    if(empty($string)){
        return "";
    }
    $adServ = new Service_ad();
    $ads = $adServ->getad($string);
    return $ads;
}

