<?php

/**
 * 客户Serv
 */
class Service_ss_customer extends Service_baseservice {

    //检查客户是否存在
    function check_customer($phone) {
        if (empty($phone)) {
            $this->setError(-1, '未获取手机号码');
            return false;
        }
        $userModel = new Model_ss_user();
        $cus_data = $userModel->getOne(array('phone' => $phone), 'user_id');
        if (!empty($cus_data) || $cus_data['user_id'] > 0) {
            return $cus_data['user_id'];
        } else {
            return 'addcus';
        }
    }

    //客户列表
    function get_customer_list($cond, $fields = "*", $limit = 0, $page = 1, $ispage = 0) {
        $userModel = new Model_ss_user();
        $start = ($page - 1) * $limit;
        $where = $userModel->quote($cond);
        if (!empty($where)) {
            $where = "where " . $where;
        }
        $limit_str = "";
        if ($limit > 0) {
            $limit_str = "limit {$start},{$limit} ";
        }
        $sql = "select {$fields} "
            . "from ss_user as su "
            . "inner join ss_user_rdt as sur on(su.user_id=sur.user_id) "
            . $where . " "
            . "order by su.user_id desc "
            . $limit_str;
        $rs_data['list'] = $userModel->query('ss_user', $sql);
        if ($ispage > 0) {
            $sql_c = "select count(*) as total "
                . "from ss_user as su "
                . "inner join ss_user_rdt as sur on(su.user_id=sur.user_id) "
                . $where;
            $rs_count = $userModel->query('ss_user', $sql_c);
            $rs_data['count'] = $rs_count[0]['total'];
        }

        return $rs_data;
    }

    //客户详情信息
    function get_customer_detail($user_id, $phone, $fields = "*") {
        if (empty($user_id) && empty($phone)) {
            $this->setError(-1, '参数错误');
            return false;
        }
        if ($user_id) {
            $where = "where su.user_id=" . $user_id . " ";
        } elseif ($phone) {
            $where = "where su.phone=" . $phone . " ";
        }
        $userModel = new Model_ss_user();
        $sql = "select $fields from ss_user as su "
            . "inner join ss_user_rdt as sur on(su.user_id=sur.user_id) "
            . $where
            . "limit 1";
        $rs_data = $userModel->query('ss_user', $sql);
        return $rs_data[0];
    }

    //编辑客户信息
    function edit_customer($params, $user_id = 0) {
        $regionModel = new Model_ss_region();
        $userModel = new Model_ss_user();
        $updata_cus = $params;
        if (!empty($params['pro_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['pro_id']), "region_name");
            $updata_cus['pro_name'] = $region['region_name'];
        }
        if (!empty($params['city_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['city_id']), "region_name");
            $updata_cus['city_name'] = $region['region_name'];
        }
        if (!empty($params['area_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['area_id']), "region_name");
            $updata_cus['area_name'] = $region['region_name'];
        }
        if (!empty($params['street_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['street_id']), "region_name");
            $updata_cus['street_name'] = $region['region_name'];
        }
        if (!empty($params['shequ_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['shequ_id']), "region_name");
            $updata_cus['shequ_name'] = $region['region_name'];
        }
        if (!empty($params['wangge_id'])) {
            $region = $regionModel->getOne(array('region_id' => $params['wangge_id']), "region_name");
            $updata_cus['wangge_name'] = $region['region_name'];
        }
        //判断联系电话是否重复
        if (!empty($params['phone'])) {
            $rs_cus = $userModel->getOne("user_id<>$user_id and phone='" . $params['phone'] . "'", 'user_id');
            if (!empty($rs_cus) && $rs_cus['user_id'] > 0) {
                $this->setError(-1, '电话号码已被占用，请核实');
                return false;
            }
        }
        //判断身份证号码是否重复
        if (!empty($params['idcard'])) {
            $rs_cus = $userModel->getOne("user_id<>$user_id and idcard='" . $params['idcard'] . "'", 'user_id,real_name');
            if (!empty($rs_cus) && $rs_cus['user_id'] > 0) {
                $this->setError(-1, '身份证号码与居民：' . $rs_cus['real_name'] . ' 的有重复，请核实');
                return false;
            }
        }
        $rs = $userModel->update(array('user_id' => $user_id), $updata_cus);
        if ($rs === false) {
            $this->setError(-1, '操作失败');
            return false;
        }
        return $rs;
    }

    //编辑客户扩展信息
    function edit_customer_ext($_user_id, $params, $user_id = 0) {
        if (empty($_user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        $userrdtModel = new Model_ss_userrdt();
        $updata_cus = $params;
        $rs = $userrdtModel->update(array('user_id' => $user_id), $updata_cus);
        if ($rs === false) {
            $this->setError(-1, '编辑居民扩展信息失败');
            return false;
        }
        return $rs;
    }

    //新增客户信息
    function add_customer($_user_id, $params) {
        if (empty($_user_id)) {
            $this->setError(-1, '请先登录');
            return false;
        }
        $userModel = new Model_ss_user();
        $userrdtModel = new Model_ss_userrdt();

        //判断联系电话是否重复
        if (!empty($params['phone'])) {
            $rs_cus = $userModel->getOne("phone='" . $params['phone'] . "'", 'user_id');
            if (!empty($rs_cus) && $rs_cus['user_id'] > 0) {
                $this->setError(-1, '电话号码已被占用，请核实');
                return false;
            }
        }

        try {
            $userModel->_db->beginTransaction("ss");
            $user_id = $userModel->insert($params);
            if ($user_id === false) {
                throw new Exception(-1, '操作失败');
            }
            $rs = $userrdtModel->insert(array('user_id' => $user_id));
            if ($rs === false) {
                throw new Exception(-1, '操作失败');
            }
            $userModel->_db->commit("ss");
            return $user_id;
        } catch (Exception $e) {
            $userModel->_db->rollBack("ss");
            $this->setError(-1, $e->getMessage());
            return false;
        }
    }

}
