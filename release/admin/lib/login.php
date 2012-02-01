<?php
/**
 *  验证用户是否登陆
 */
if (!defined("INCLUDE_CORE")){die("Permission denied.");}
require_once("user.php");

class Login extends User{
    var $userData;
    function Login($conf=array()){
        $this->conf=$conf;
        parent::__construct($this->conf);
        $this->msg="";
        $this->isSaveSession=false;
        $this->isSaveCookie=false;
    }

    public function check($username,$password){
        $checkResult = parent::check($username,$password);
        if ($checkResult){
            if ($this->isSaveSession){
                $this->saveSession();
            }
            if ($this->isSaveCookie){
                $this->saveCookie();
            }
            return True;
        }else{
            $this->msg='用户名或密码不正确';
            return False;
        }
    }

    function checkBySession(){
        if (isset($_SESSION['username']) && $_SESSION['username'] && $_SESSION['password']){
            $username=$_SESSION['username'];
            $password=$_SESSION['password'];
            return $this->check($username,$password);
        }else{
            return false;
        }
    }

    function saveCookie(){
        setcookie("username",$this->userData['username'],time()+60*60*24*365);//save cookie for one year
        setcookie("password",$this->userData['password'],time()+60*60*24*365);//save password for one year
    }

    function saveSession(){
        $_SESSION["username"]=$this->userData['username'];
        $_SESSION["password"]=$this->userData['password'];
        $_SESSION["nickname"]=$this->userData['nickname'];
    }
}
