<?php

// 公共处理类
class SCommon {

    /**
     *  短信公共发送通道
     *
     * @param mixed $mobile     手机号码
     * @param mixed $content    内容
     * @param mixed $type       类型 默认 1 凌凯 2 凌凯
     * @param mixed $stype      1短信验证码 2 通知
     * @param string $send_time 短信发送时间 为空表示随时可发； 不为空：格式为“6-18”，表示在6点到18点之间可发
     */
    public static function sendPhoneMsg($mobile, $content, $type = 1, $stype = 1, $send_time = "") {
        if (!SUtil::IsMobile($mobile)) {
            return false;
        }
        if (!empty($send_time)) {//短信限时发送
            list($shour, $ehour) = explode("-", $send_time);
            $ntime = date("H");
            if ($ntime < $shour || $ntime >= $ehour) {
                return false;
            }
        }
        $sendlogModel = new Model_ss_sendmsglog();
        $daytime = strtotime(date("Ymd"));
        //验证码短信每天最多发5次
        if($stype == 1){
            $sendNum = $sendlogModel->getCount("phone = '{$mobile}' AND add_time >{$daytime} AND stype=1");
            if($sendNum >4){
                 return 'outflag';
            }
        }
        // 检查如果1发送不通 第二次发送 则发送第二个通道
        $sendlog = $sendlogModel->getOne("phone = '{$mobile}' AND add_time >{$daytime} AND stype=1 ORDER BY sl_id DESC", 'channel,msg');
        if ($sendlog) {
            $type = 1;
            if (strcmp($sendlog['channel'], 'lk') == 0) {
                $type = 2;
            }
            // 检查如果是短信验证码 当天则发送一致 =》重发
            if (strcmp($stype, 1) == 0) {
                $content = $sendlog['msg'];
            }
        }
        // 特殊处理
        if (strcmp($stype, 1) == 0) {
            $key = 'yzm_' . $mobile;
            preg_match("/[^0-9]*([0-9]+)/", $content, $matches);
            $rand = $matches[1];
            SUtil::ssetcookie(array($key => $rand, 'yzmsendtime' => time()), 300, '/', SConstant::COOKIE_DOMAIN);
        }
        if (strcmp($stype, 2) == 0) {
            $sendCount = $sendlogModel->getCount("phone='{$mobile}' AND add_time > {$daytime} AND stype =2");
            if ($sendCount > 19 && $sendCount < 118) {
                $type = 1;
            }
        }
        switch ($type) {
            case 1:
                $commonServ = new Service_ss_common();
                $commonServ->send_msg($mobile, $content, $stype);
                break;
            case 2:
                $commonServ = new Service_ss_common();
                $commonServ->send_msg($mobile, $content, $stype);
                break;
            default:
                return false;
                break;
        }
        
        $indata = array(
            'stype'=>$stype,
            'phone'=>$mobile,
            'msg'=>$content,
            'status'=>1,
            'add_time'=>time(),
            'channel'=>'lk',
        );
        $sendlogModel->insert($indata);
        return true;
    }

    /**
     *  6dmap短信公共发送通道
     *
     * @param mixed $mobile     手机号码
     * @param mixed $content    内容
     * @param mixed $type       类型 默认 1 凌凯 2 凌凯
     * @param mixed $stype      1短信验证码 2 通知
     * @param string $send_time 短信发送时间 为空表示随时可发； 不为空：格式为“6-18”，表示在6点到18点之间可发
     */
    public static function sendPhoneMsg6Dmap($mobile, $content, $type = 2, $stype = 1, $send_time = "") {
        if (!SValidate::IsMobile($mobile)) {
            return false;
        }
        if (!empty($send_time)) {//短信限时发送
            list($shour, $ehour) = explode("-", $send_time);
            $ntime = date("H");
            if ($ntime < $shour || $ntime >= $ehour) {
                return false;
            }
        }

        switch ($type) {
            case 1:
                $content = iconv('UTF-8', 'GBK', $content);
                $url = SConstant::$mobile_message_api['url'];
                $CorpID = SConstant::$mobile_message_api['CorpID'];
                $Pwd = SConstant::$mobile_message_api['Pwd'];
                $params = array(
                    "CorpID" => $CorpID,
                    "Pwd" => $Pwd,
                    "Mobile" => $mobile,
                    "Content" => $content
                );
                $rs = SHttpPost::postRequest($url, $params);
                break;
            case 2:
                $content = iconv('UTF-8', 'GBK', $content);
                $url = SConstant::$mobile_message_api['url'];
                $CorpID = SConstant::$mobile_message_api['CorpID'];
                $Pwd = SConstant::$mobile_message_api['Pwd'];
                $params = array(
                    "CorpID" => $CorpID,
                    "Pwd" => $Pwd,
                    "Mobile" => $mobile,
                    "Content" => $content
                );
                $rs = SHttpPost::postRequest($url, $params);
                break;
            default:
                return false;
                break;
        }
        return true;
    }
    
}
