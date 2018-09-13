<?php
class SessionManager{
  var $db,$cookie_manager;
  var $data;
  var $cookie_expiration_time=24*60*60;

  // Constructor, linker
  function __construct(){$this->db=null;$this->data=null;}
  function linkDB($db){$this->db=$db;}
  function linkCookieManager($cookie_manager){$this->cookie_manager=$cookie_manager;}

  // Methods
  function activateUser($user_id,$email){
    $statement=$this->db->getPDO()->prepare(
      "INSERT INTO pizzapp_sessions (user_id, token, email)
      VALUES (:user_id, :token, :email)
      ON DUPLICATE KEY UPDATE token=:token_update, time=CURRENT_TIMESTAMP"
    );
    $token=unique_random_string(16);
    $this->db->pdo_multibindParams($statement, array(
      'user_id'=>$user_id,
      'token'=>$token,
      'token_update'=>$token,
      'email'=>$email
    ));
    $statement->execute();
    $this->cookie_send($token);
    return $statement;
  }

  function deactivateUser($user_id,$email){
    $statement=$this->db->getPDO()->prepare(
      "DELETE FROM pizzapp_sessions WHERE user_id=:user_id AND email=:email"
    );
    $statement->execute();
    $this->cookie_remove();
    return true;
  }

  function tryAuthenticate(){
    $status=false;

    $token=$this->cookie_read();
    $user_id=null;

    if($token!=null){
      $statement=$this->db->getPDO()->prepare("SELECT user_id, time FROM pizzapp_sessions WHERE token=:token");
      $this->db->pdo_multibindParams($statement, array('token'=>$token));
      $statement->execute();
      $result=$statement->fetch();
      if((time()-strtotime($result['time']))<($this->cookie_expiration_time)){
        $this->data=array('user_id'=>$result['user_id']);
        $status=true;
      }else{
        $this->cookie_remove();
      }
    }

    return $status;
  }

  function isActivated(){
    return ($this->data!=null);
  }

  function cookie_send($token){
    $this->cookie_manager->set_secure('session_app_login', $token, time()+($this->cookie_expiration_time));
  }
  function cookie_read(){return $this->cookie_manager->get('session_app_login');}
  function cookie_remove(){$this->cookie_manager->remove('session_app_login');}
}

return new SessionManager();