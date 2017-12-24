<?php

/**
 * 支付Serv
 * 
 */
class Service_ss_pay extends Service_baseservice {
    //新增、编辑支付配置
    function edit_payconf($user_id, $params, $pay_id=0){
        if (empty($user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        $payModel = new Model_ss_pay();
        $updata = array(
            'pay_name' => $params['pay_name'],
            'pay_code' => $params['pay_code'],
            'pay_type' => $params['pay_type'],
            'pay_status' => $params['pay_status'],
            'pay_des' => $params['pay_des'],
            'pay_key' => $params['pay_key']
        );
        if($pay_id>0){
            $rs = $payModel->update(array('pay_id'=>$pay_id), $updata);
        }else{
            $rs = $payModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }

}
