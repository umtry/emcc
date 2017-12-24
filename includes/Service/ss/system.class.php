<?php
/**
 * 平台设置Serv
 */
class Service_ss_system extends Service_baseservice{
    /**
     * 服务流程
     */
    //服务流程列表
    function get_service_list($cond,$fields,$orderby,$limit=0,$page=1,$ispage=0){
        $serviceModel = new Model_ss_service();
        $rs_data = $serviceModel->get_list($cond, $fields,$orderby, $limit, $page, $ispage);
        foreach($rs_data['list'] as $key=>$val){
            if(!empty($val['stitles'])){
                $rs_data['list'][$key]['stitles'] = unserialize($val['stitles']);
            }
        }
        return $rs_data;
    }
    
    //添加/编辑通话流程
    function edit_service($user_id, $user_name,$params, $id=0){
        if (empty($user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        if (empty($params['content'])) {
            $this->setError(-1, '请填写通话内容');
            return false;
        }
        $serviceModel = new Model_ss_service();
        $updata = array(
            'content' => $params['content'],
            'status' => $params['status'],
            'stype' => $params['stype'],
            'sort' => $params['sort'],
            'admin_id' => $user_id,
            'admin_name' => $user_name
        );
        
        if($id>0){
            $rs = $serviceModel->update(array('id'=>$id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            $rs = $serviceModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }
    //服务流程详情
    function get_service_detail($id){
        $serviceModel = new Model_ss_service();
        $rs_data = $serviceModel->getOne(array('id'=>$id));
        return $rs_data;
    }
    
    //添加/编辑通话流程选项
    function edit_service_rs($user_id, $user_name,$params, $id=0){
        if (empty($user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        if (empty($params['rs_title'])) {
            $this->setError(-1, '请填写回复选项');
            return false;
        }
        $servicersModel = new Model_ss_servicers();
        $updata = array(
            'sid' => $params['sid'],
            'rs_title' => $params['rs_title']
        );
        
        if($id>0){
            $rs = $servicersModel->update(array('id'=>$id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            $rs = $servicersModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1001, '操作失败');
            return false;
        }
        return $rs;
    }
    //通话流程选项
    function get_service_rs($sid){
        $servicersModel = new Model_ss_servicers();
        $rs_data = $servicersModel->get_list(array('sid'=>$sid), 'order by id asc');
        return $rs_data;
    }
    
    
    
    /**
     * 事项类型
     */
    //事项类型
    function get_mcate_list($condition=array(1)){
        $mattercateModel = new Model_ss_mattercate();
        $rs_data = $mattercateModel->getList($condition, "concat(seq,',',st_id) as abs,st_id,st_name,parent_id,sort,seq,status", "order by abs asc");
        return $rs_data;
    }
    //事项类型重新组合
    function get_mcate_list2($condition=array(1)){
        $mattercateModel = new Model_ss_mattercate();
        $rs_data = $mattercateModel->getList($condition, "concat(seq,',',st_id) as abs,st_id,st_name,parent_id,sort,seq,status", "order by abs asc");
        $tmp_data = array();
        foreach($rs_data as $val){
            $tmp_data[$val['st_id']] = $val['st_name'];
        }
        return $tmp_data;
    }
    //编辑
    function edit_mcate($user_id, $user_name,$params, $st_id=0){
        if (empty($user_id)) {
            $this->setError(-1001, '请先登录');
            return false;
        }
        if (empty($params['st_name'])) {
            $this->setError(-1001, '请填写状态名');
            return false;
        }
        $mattercateModel = new Model_ss_mattercate();
        $updata = array(
            'parent_id' => $params['parent_id'],
            'st_name' => $params['st_name'],
            'sort' => $params['sort'],
            'seq' => $params['seq'],
            'status' => $params['status'],
            'admin_id' => $user_id,
            'admin_name' => $user_name
        );
        
        if($st_id>0){
            $rs = $mattercateModel->update(array('st_id'=>$st_id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            $rs = $mattercateModel->insert($updata);
        }
        if ($rs === false) {
            $this->setError(-1001, '操作失败');
            return false;
        }
        return $rs;
    }
    
    /**
     * 民族代码
     */
    function get_mzdm_list(){
        $mzdmModel = new Model_ab_mzdm();
        $mzdm = $mzdmModel->getList(array(1));
        return $mzdm;
    }
    
}
