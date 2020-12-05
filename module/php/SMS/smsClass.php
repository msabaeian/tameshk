<?php
include 'connection.php';
class sms{
    private $username = "";
    private $password = "";
    private $number = "";
    function __construct()
    {
        echo "hi";
    }

    public function SendMessage($msg,$phone){
        $url = 'http://www.afe.ir/WebService/V4/BoxService.asmx?wsdl';
        $method = 'SendMessage';
        $param = array('Username' => $this->username,'Password' => $this->password,'Number' => $this->number,'Mobile' => array("$phone"),'Message' => "$msg",'Type' => "3");
        define($security,1);
        include_once("connection.php");
        $request = new connection($url,$method,$param);
        $message = $request->connect();
        echo $message;
        $request->__destruct();
        unset($url,$method,$param,$request);
    }

    public function getCount(){
        /*$url = 'http://www.afe.ir/WebService/Inboxservice.asmx?wsdl';
        $method = 'GetCount';
        $param = array('Username' => "$value[0]",'Password' => "$value[1]",'To' => "$value[2]",'ID' => $value[3]);
        define($security,1);
        include_once("include/connection.php");
        $request = new connection($url,$method,$param);
        $message = $request->connect();
        $request->__destruct();
        unset($value,$url,$method,$param,$request);*/
    }
}

?>