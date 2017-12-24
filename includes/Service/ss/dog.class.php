<?php

/**
 * 加密狗Serv
 * get server data information
 */
class Service_ss_dog extends Service_baseservice {

    private static $s_vendorID = '';
    private static $s_authcode = '';
    private static $s_authFactor = '';
    private static $s_authfile = '';
    private static $s_fctrfile = '';

    public function __construct($rootpath) {
        self::$s_authfile = "$rootpath/config/auth_code.xml";
        self::$s_fctrfile = "$rootpath/config/auth_factor.xml";
    }

    public function __destruct() {
        
    }

    public function getVendorID() {
        if ('' != self::$s_vendorID) {
            return self::$s_vendorID;
        }

        If (!file_exists(self::$s_authfile)) {
            echo 'Authentication file not exist';
            return 'Authentication file not exist';
        }

        $doc = new DOMDocument();
        $doc->load(self::$s_authfile);
        $authdata = $doc->getElementsByTagName("dogauth");
        foreach ($authdata as $authnode) {
            $vendorTag = $authnode->getElementsByTagName("vendor");
            self::$s_vendorID = $vendorTag->item(0)->getAttribute("id");
            return self::$s_vendorID;
        }
    }

    public function getVendorCode() {
        if ('' != self::$s_authcode) {
            return self::$s_authcode;
        }
        If (!file_exists(self::$s_authfile)) {
            echo 'Authentication file not exist';
            return 'Authentication file not exist';
        }

        $doc = new DOMDocument();
        $doc->load(self::$s_authfile);
        $authdata = $doc->getElementsByTagName("dogauth");
        foreach ($authdata as $authnode) {
            $acodeTag = $authnode->getElementsByTagName('authcode');
            self::$s_authcode = $acodeTag->item(0)->nodeValue;
            return self::$s_authcode;
        }
    }

    public function getAuthFactor() {
        if ('' != self::$s_authFactor) {
            return self::$s_authFactor;
        }
        If (!file_exists(self::$s_fctrfile)) {
            echo 'Authentication factor file not exist';
            return 'Authentication factor file not exist';
        }

        $doc = new DOMDocument();
        $doc->load(self::$s_fctrfile);
        $authdata = $doc->getElementsByTagName("dogauth");
        foreach ($authdata as $authnode) {
            $acodeTag = $authnode->getElementsByTagName('factor');
            self::$s_authFactor = $acodeTag->item(0)->nodeValue;
            if ('' == self::$s_authFactor) {
                self::$s_authFactor = "00000000";
            }
            return self::$s_authFactor;
        }
    }

}
