<?php
/**
 * 类成员常量定义
 */
class SConstant {
    /*******************系统配置文件*************/
    const SYSUSERKEY = 'ktrujcvqd,ofgvcgDcv%F&gfF@##$%f';//前台用户登陆key
    const MONEYKEY = 'fa#*^$(_+(*!@@#"dmcl.gp;chiqndc;lj';//用户金额验证key
    const ADMINUSERKEY = 'qydfxq.i-.##FDFAF%D#^DFSfhF@qc4rx';//后台登陆验证key
    const PAGE_SIZE = 13;   //分页展示个数
    const PAGE_NUM = 5; //分页组件长度

    const LOGOUTLIMIT = 3600; //多长时间未操作自动退出
    const UPLOADIMGSIZE = 10485760;  //10MB 上传图片文件大小 2mb 1mb=1048576  2mb=2097152
    const UPLOADPICTYPE = 'jpg,gif,jpeg,png';  //上传图片格式限制
    const UPLOADDOCTYPE = 'zip,rar,doc,docx,xls,xlsx,ppt,ppts,pdf';  //上传文档格式限制
    
    const COOKIE_DOMAIN = '.xnshehui.com';//cookie作用域
    
    const MSG_SUFFIX = '【便民坊】';//短信后缀
    //后台path
    const SSADMIN = '/ssadmin';
    //upload目录
    const UPLOAD_URL = '/upload';
    
}

/**
	End file,Don't add ?> after this.
*/