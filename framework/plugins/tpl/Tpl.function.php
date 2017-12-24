<?php
function tpl_function_tostring($mixed){
	return var_export($mixed,true);
}
function tpl_function_part($path){
	return !empty($path)?SlightPHP::run($path):"";
}
function tpl_function_include($tpl,$k1='',$v1='',$k2='',$v2='',$k3='',$v3=''){
    if (!empty($k1)) {
        Tpl::assign($k1,$v1);
    }
    if (!empty($k2)) {
        Tpl::assign($k2,$v2);
    }
    if (!empty($k3)) {
        Tpl::assign($k3,$v3);
    }
	return Tpl::fetch($tpl);
}
