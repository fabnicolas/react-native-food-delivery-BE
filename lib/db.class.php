<?php
// Extending PDO
class DB{
  var $pdo;
  var $connection_params;
  var $admin_params;

  private function pdo_connectionparams_composer($arr_values){
    return $this->pdo_composer($arr_values,'',';',true);
  }

  function pdo_in_composer($arr_values){
    $str_params=$this->pdo_composer($arr_values,'?',',',false);
    if($str_params=="") $str_params="-1";
    return "(".$str_params.")";
  }

  function pdo_insertinto_composer($arr_values, $structure){
    return $this->pdo_composer($arr_values,$structure,',',false);
  }

  function pdo_composer($arr_values,$structure,$separator,$use_key_value=false){
    $str_params="";
    foreach($arr_values as $key=>$value){
      if($use_key_value) $structure=$key."=".$value;
      $str_params.=$structure.$separator.' ';
    }
    $str_params=substr($str_params,0,strlen($str_params)-strlen($separator.' '));
    return $str_params;
  }

  function pdo_insertinto_executeparams($arr_values){
    $arr = array();
    foreach($arr_values as $subarray){
      foreach($subarray as $key=>$value){array_push($arr,$value);}
    }
    return $arr;
  }

  function pdo_multibindParams($statement,$params){
    foreach($params as $key=>&$value){
      $statement->bindParam(":".$key, $value);
    }
  }

  function pdo_interpolateQuery($query, $params) {
    $keys = array();
    foreach($params as $key => $value) {
      if(is_string($key)) $keys[] = '/:'.$key.'/';
      else $keys[] = '/[?]/';
    }
    return preg_replace($keys, $params, $query, 1, $count);
  }

  function __construct($connection_params, $admin_params, $options=null, $connect=true){
    $this->connection_params=$connection_params;
    $this->admin_params=$admin_params;
    if(!$options) $options = [
      PDO::ATTR_EMULATE_PREPARES   => false,
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    if($connect) $this->pdo = new PDO("mysql:".($this->pdo_connectionparams_composer($this->connection_params)), $this->admin_params['username'], $this->admin_params['password'], $options);
  }

  function getPDO(){return $this->pdo;}
}
?>