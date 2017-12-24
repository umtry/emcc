<?php
/**
 * 群发信息Serv
 */
class Service_ss_mass extends Service_baseservice{
    //列表
    function get_mass_list($cond,$fields,$orderby,$limit=0,$page=1,$ispage=0){
        $massModel = new Model_ss_mass();
        $rs_data = $massModel->get_list($cond,$fields, $orderby, $limit, $page, $ispage);
        return $rs_data;
    }
    //获取单个详情
    function get_mass_detail($cond){
        $massModel = new Model_ss_mass();
        $rs_data = $massModel->getOne($cond);
        return $rs_data;
    }
    //新增、编辑
    function edit_mass($user_id, $user_name,$mass_params, $mass_id=0){
        if (empty($user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        $massModel = new Model_ss_mass();
        $updata = array(
            'type' => $mass_params['type'],
            'content' => $mass_params['content'],
            'send_time' => $mass_params['send_time'],
            'status' => $mass_params['status']
        );
        
        if($mass_id>0){
            $rs = $massModel->update(array('mass_id'=>$mass_id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            $rs = $massModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }
    
    
    /**
     * 群发用户
     */
    //列表
    function get_massuser_list($cond,$fields,$orderby,$limit=0,$page=1,$ispage=0){
        $massuserModel = new Model_ss_massuser();
        $rs_data = $massuserModel->get_list($cond, $fields,$orderby, $limit, $page, $ispage);
        return $rs_data;
    }
    //新增编辑临时群发用户
    function edit_massuser($user_id, $muser_params, $id=0){
        if (empty($user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        $massuserModel = new Model_ss_massuser();
        //先判断是否有重复的电话号码
        $muser = $massuserModel->getOne(array('mass_id'=>$muser_params['mass_id'],'phone'=>$muser_params['phone']), 'id');
        if(!empty($muser) && $muser['id']>0){
            $this->setError(-1, '此电话号码已经被添加');
            return false;
        }
        $updata = array(
            'mass_id' => $muser_params['mass_id'],
            'user_id' => 0,
            'real_name' => $muser_params['real_name'],
            'phone' => $muser_params['phone'],
            'status' => $muser_params['status']
        );
        
        if($id>0){
            $rs = $massuserModel->update(array('id'=>$id), $updata);
        }else{
            $rs = $massuserModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }
}
