<?php

/**
 * @author fenngle
 * @time 2014-08-11
 */
class Controller_dog extends Controller_basepage {

    public function __construct() {
        parent::__construct();
        $this->isLogin(true);
        $this->tvar = array();
    }

    //
    function pageConfiginfo() {
        //get the q parameter from URL
        $qrInfo = $_GET["data"];

        $dogServ = new Service_ss_dog($_SERVER["DOCUMENT_ROOT"]);
        if (0 < strlen($qrInfo)) {
            if (strtolower("VendorID") == strtolower($qrInfo)) {
                echo $dogServ->getVendorID();
            } elseif (strtolower("AuthCode") == strtolower($qrInfo)) {
                echo $dogServ->getVendorCode();
            } elseif (strtolower("Factor") == strtolower($qrInfo)) {
                echo $dogServ->getAuthFactor();
            }
        }
    }

    //get the challenge parameter from client 
    function pageGetchallge() {
        $strchag = dog_auth_get_challenge();
        if ("" != $strchag) {
            $_SESSION['ChallengeData'] = $strchag;
            echo $strchag;
        } else {
            unset($_SESSION['ChallengeData']);
            echo '';
        }
    }

    //Do authenticate
    function pageAuthchk() {
        $dogServ = new Service_ss_dog($_SERVER["DOCUMENT_ROOT"]);
        $vendorID = $dogServ->getVendorID();
        $factor = $dogServ->getAuthFactor();

        //get the auth parameters from client
        $dogID = $_GET["dogid"];
        $strDigest = $_GET["digest"];

        $res = -1;

        if (0 < strlen($dogID) && 0 < strlen($strDigest) && 0 < strlen($vendorID) && 0 < strlen($factor)) {
            try {
                $res = dog_auth_verify_digest($vendorID, $dogID, $_SESSION['ChallengeData'], $strDigest, $factor);
                echo $res;
                if (0 == $res) {
                    $_SESSION['authState'] = 'pass';
                    echo 0;
                } else {
                    unset($_SESSION['authState']);
                    echo $res;
                }
            } catch (Exception $e) {
                echo 'error:' . $e->getMessage();
            }
        }
    }

}
