<?php

/**
 * 通用Serv
 */
class Service_ss_common extends Service_baseservice {

    /**
     * 联动选中
     * 
     */
    public function region($pro_id = 0, $city_id = 0, $area_id = 0, $street_id = 0, $shequ_id = 0) {
        $regionModel = new Model_ss_region();
        $pca = array();
        $pca['pros'] = $regionModel->getList(array("parent_id" => 1));
        if (!empty($pro_id)) {
            $pca['citys'] = $regionModel->getList(array("parent_id" => $pro_id));
        } else {
            $pca['citys'] = array();
        }
        if (!empty($city_id)) {
            $pca['areas'] = $regionModel->getList(array("parent_id" => $city_id));
        } else {
            $pca['areas'] = array();
        }
        if (!empty($area_id)) {
            $pca['streets'] = $regionModel->getList(array("parent_id" => $area_id));
        } else {
            $pca['streets'] = array();
        }
        if (!empty($street_id)) {
            $pca['shequs'] = $regionModel->getList(array("parent_id" => $street_id));
        } else {
            $pca['shequs'] = array();
        }
        if (!empty($shequ_id)) {
            $pca['wangges'] = $regionModel->getList(array("parent_id" => $shequ_id));
        } else {
            $pca['wangges'] = array();
        }
        return $pca;
    }

    //发短信
    function send_msg($phones, $content, $stype) {
        if (empty($phones) || empty($content)) {
            $this->setError(-1, '发送失败，请填写手机号码和发送内容');
            return false;
        }
        $phone_arr = explode(',', $phones);
        $i = 0;
        $tmp_phone = array();
        foreach ($phone_arr as $val) {
            $val = trim($val);
            $rs = SUtil::IsMobile($val);
            if ($rs === false) {
                //$this->setError(-1, '发送失败，请填写正确的手机号码');
                continue;
                //return false;
            }
            $k = floor($i / 10000);
            $tmp_phone[$k][] = $val;
            $i++;
        }

        $content = iconv('UTF-8', 'GBK', $content);

        foreach ($tmp_phone as $val) {
            $tmp_phones = implode(',', $val);
            if ($stype == 1) {//验证码
                //$url = "http://sdk2.028lk.com:9880/sdk2/BatchSend2.aspx?CorpID=MAHF000777&Pwd=8615@abc&Mobile=".$tmp_phones."&Content=".$content;
                $url = "http://sdk.mobilesell.cn:89/ws/BatchSend2.aspx";
                $data = array(
                    'CorpID' => 'CQLKJ0005308',
                    'Pwd' => 'cqlk@9008',
                    'Mobile' => $tmp_phones,
                    'Content' => $content
                );
            }
            if ($stype == 2) {//通知
                //$url = "http://sdk2.028lk.com:9880/sdk2/BatchSend2.aspx?CorpID=MAHF000777&Pwd=8615@abc&Mobile=".$tmp_phones."&Content=".$content;
                $url = "http://sdk.mobilesell.cn:89/ws/BatchSend2.aspx";
                $data = array(
                    'CorpID' => 'CQLKJ0005308',
                    'Pwd' => 'cqlk@9008',
                    'Mobile' => $tmp_phones,
                    'Content' => $content
                );
            }

            $rs = SUtil::http_curl($url, $data, 120, 'post');
            //var_dump($url);
            //var_dump($rs);
            sleep(1);
        }
        //exit;
        return $rs;
    }

}
