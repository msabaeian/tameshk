<?php
if(!defined($security)){
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    die();
}
class connection{
    public $value;
    public $method;
    public $url;
    public $type;
    function __construct($url,$method,$value,$type=''){
        $this->value = $value;
        $this->method = $method;
        $this->url = $url;
        $this->type = $type;
    }
    function connect(){
        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient($this->url);
        $result = $client->__SoapCall($this->method, array($this->value));
        if($this->type == 'object')
            return get_object_vars($result);
        $merge = $this->method.'Result';
        if($result->$merge->string != '')
            return $result->$merge->string;
        else
            return $result->$merge;
    }
    function __destruct(){
        unset($this->value,$this->url,$this->method,$this->type);
    }
}
?>