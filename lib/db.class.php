<?php
// Extending PDO
class DB{
    var $pdo;
    var $connection_params;
    var $admin_params;

    private function pdo_params_composer($array_params){
        $str_params="";
        foreach($array_params as $key=>$value){
            $str_params.=$key."=".$value.";";
        }
        return (substr($str_params,0,strlen($str_params)-1));
    }

    function in_composer($arr_values){
        $str_params="";
        foreach($arr_values as $key=>$value){
            $str_params.=$value.",";
        }
        return (substr($str_params,0,strlen($str_params)-1));
    }

    function pdo_multibindParams($statement,$params){
        foreach($params as $key=>&$value){
            $statement->bindParam(":".$key, $value);
        }
    }

    function __construct($connection_params, $admin_params, $options=null, $connect=true){
        $this->connection_params=$connection_params;
        $this->admin_params=$admin_params;
        if(!$options) $options = [
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        if($connect) $this->pdo = new PDO("mysql:".($this->pdo_params_composer($this->connection_params)), $this->admin_params['username'], $this->admin_params['password'], $options);
    }

    function getPDO(){return $this->pdo;}
}
?>