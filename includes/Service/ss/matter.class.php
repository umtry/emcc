<?php
/**
 * 客户事项Serv
 */
class Service_ss_matter extends Service_baseservice{
    //事项列表
    function get_matter_list($cond,$orderby,$limit=0,$page=1,$ispage=0){
        $matterModel = new Model_ss_matter();
        $rs_data = $matterModel->get_list($cond, $orderby, $limit, $page, $ispage);
        if($rs_data['list']){
            foreach($rs_data['list'] as $key=>$val){
                $rs_data['list'][$key]['process'] = unserialize($val['process']);
            }
        }
        return $rs_data;
    }
    //获取单个事项信息
    function get_matter_detail($cond){
        $matterModel = new Model_ss_matter();
        $rs_data = $matterModel->getOne($cond);
        return $rs_data;
    }
    //编辑事项信息
    function edit_matter($_user_id, $_user_name,$matter_params, $id=0){
        if (empty($_user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        if (empty($matter_params['user_id'])) {
            $this->setError(-1, '请先保存居民信息');
            return false;
        }
        $matterModel = new Model_ss_matter();
        $mattercateModel = new Model_ss_mattercate();
        $userModel = new Model_ss_user();
        $staffModel = new Model_ss_staff();
        
        $updata = array(
            'user_id' => $matter_params['user_id'],
            'answer_state' => $matter_params['answer_state'],
            'content' => $matter_params['content'],
            'remark' => $matter_params['remark'],
            'status' => $matter_params['status'],
            'intention' => $matter_params['intention'],
            'stf_id' => $matter_params['stf_id'],
            'price' => $matter_params['price'],
            'service_time' => $matter_params['service_time']
        );
        
        //
        if(!empty($matter_params['intention'])){
            $mstatus = $mattercateModel->getOne(array('st_id'=>$matter_params['intention']), 'st_name');
            $updata['intention_name'] = $mstatus['st_name'];
        }
        //
        if(!empty($matter_params['user_id'])){
            $customer = $userModel->getOne(array('user_id'=>$matter_params['user_id']), 'real_name');
            $updata['real_name'] = $customer['real_name'];
        }
        //
        $updata['stf_name'] = "";
        if(!empty($matter_params['stf_id'])){
            $staff = $staffModel->getOne(array('stf_id'=>$matter_params['stf_id']), 'stf_name');
            $updata['stf_name'] = $staff['stf_name'];
        }
        
        if($id>0){
            $rs = $matterModel->update(array('id'=>$id), $updata);
        }else{
            $updata['add_time'] = $this->_time;
            $updata['admin_id'] = $_user_id;
            $updata['admin_name'] = $_user_name;
            $rs = $matterModel->insert($updata);
            if($rs){
                return $updata;
            }
        }
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }
    
    
    /**
     * 事项类型
     */
    function get_mcate_list(){
        $mattercateModel = new Model_ss_mattercate();
        $cond['status'] = 1;
        $rs_data = $mattercateModel->get_list($cond, 'order by st_id asc');
        return $rs_data;
    }
    
    //事项列表-事项表，用户表联查
    function get_matter_mutilist($cond,$orderby,$fields="*",$limit=0,$page=1,$ispage=0){
        $matterModel = new Model_ss_matter();
        $start = ($page-1)*$limit;
        
        if($cond['intention']){
            $condition[] = "sm.intention=".$cond['intention'];
        }
        if($cond['real_name']){
            $condition[] = "sm.real_name=".$cond['real_name'];
        }
        if($cond['status']){
            $condition[] = "sm.status=".$cond['status'];
        }
        if($cond['pay_status']){
            $condition[] = "sm.pay_status=".$cond['pay_status'];
        }
        if($cond['phone']){
            $condition[] = "su.phone=".$cond['phone'];
        }
        
        $where = "";
        $condition = $matterModel->quote($condition);
        if(!empty($condition)){
            $where = "where ".$condition;
        }
        $sql = "select $fields "
            . "from ss_matter as sm "
            . "inner join ss_user as su on(sm.user_id=su.user_id) "
            . $where." "
            . $orderby." "
            . "limit $start,$limit";
        
        $rs['list'] = $matterModel->query('ss_matter', $sql);
        if($ispage){
            $sql_c = "select count(*) as total "
                . "from ss_matter as sm "
                . "inner join ss_user as su on(sm.user_id=su.user_id) "
                . $where." ";
            $c = $matterModel->query('ss_matter', $sql_c);
            $rs['count'] = $c[0]['total'];
        }
        return $rs;
    }
    
    
    //收发件列表-快递表，用户表联查
    function get_express_mutilist($cond,$orderby,$fields="*",$limit=0,$page=1,$ispage=0){
        $expressModel = new Model_ss_express();
        $start = ($page-1)*$limit;
        
        if($cond['ex_type']){
            $condition[] = "sm.ex_type=".$cond['ex_type'];
        }
        if($cond['real_name']){
            $condition[] = "sm.real_name='".$cond['real_name']."'";
        }
        if($cond['ex_number']){
            $condition[] = "sm.ex_number='".$cond['ex_number']."'";
        }
        if($cond['status']){
            $condition[] = "sm.status=".$cond['status'];
        }
        if($cond['pay_status']){
            $condition[] = "sm.pay_status=".$cond['pay_status'];
        }
        if($cond['phone']){
            $condition[] = "su.phone='".$cond['phone']."'";
        }
        
        $where = "";
        $condition = $expressModel->quote($condition);
        if(!empty($condition)){
            $where = "where ".$condition;
        }
        $sql = "select $fields "
            . "from ss_express as sm "
            . "inner join ss_user as su on(sm.user_id=su.user_id) "
            . $where." "
            . $orderby." "
            . "limit $start,$limit";
        
        $rs['list'] = $expressModel->query('ss_express', $sql);
        if($ispage){
            $sql_c = "select count(*) as total "
                . "from ss_express as sm "
                . "inner join ss_user as su on(sm.user_id=su.user_id) "
                . $where." ";
            $c = $expressModel->query('ss_express', $sql_c);
            $rs['count'] = $c[0]['total'];
        }
        return $rs;
    }
}
