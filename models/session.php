<?php
class Session{
  var $db;

  // Constructor, linker
  function __construct(){$this->db=null;}
  function linkDB($db){$this->db=$db;}

  // Methods
  function activateUser($user_id,$email){
    $statement=$this->db->getPDO()->prepare(
      "INSERT INTO pizzapp_sessions (user_id, token, email)
      VALUES (:user_id, :token, :email)
      ON DUPLICATE KEY UPDATE token=:token_update"
    );
    $token=unique_random_string(16);
    $x=array(
      'user_id'=>$user_id,
      'token'=>$token,
      'token_update'=>$token,
      'email'=>$email
    );
    $this->db->pdo_multibindParams($statement, $x);
    $statement->execute();
    return $statement;
  }
}

return new Session();