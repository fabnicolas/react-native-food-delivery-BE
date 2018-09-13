<?php
class CookieManager{
  function __construct(){}
  function remove($cookie_name){
    unset($_COOKIE[$cookie_name]);
    setcookie($cookie_name, '');
  }

  function get($cookie_name){
    if(isset($_COOKIE[$cookie_name])){
      return $_COOKIE[$cookie_name];
    }else{
      return null;
    }
  }

  function set_secure($cookie_name, $content, $expire=0){
    setcookie($cookie_name, $content, $expire, '', isset($_SERVER["HTTPS"]), true);
  }
}

return new CookieManager();