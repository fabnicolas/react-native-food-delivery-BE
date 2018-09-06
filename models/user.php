<?php
class UserModel{
  var $db;

  function __construct(){$db=null;}
  function linkDB($db){$this->db=$db;}

  function register($email, $password){
    $error=0; // 0 means "success"
    $error_extra=null;

    if(!($email!=null && filter_var($email, FILTER_VALIDATE_EMAIL))){
      // Email is NOT valid
      $error=1;
    }else if(!($password!=null && strlen($password)>=8)){
      // Password is NOT valid
      $error=2;
    }else{
      // Generate password using a random salt (That will be stored in database)
      $password_salt = unique_random_string(3);
      $password_hashed = hash('sha256', $password.$password_salt);
      
      try{
        // Insert user into DB
        $statement = $this->db->getPDO()->prepare(
          "INSERT INTO pizzapp_users (email, password, password_salt) VALUES (:email, :password, :password_salt)"
        );
    
        $this->db->pdo_multibindParams($statement, array(
          'email'=>$email,
          'password'=>$password_hashed,
          'password_salt'=>$password_salt
        ));
        if($statement->execute()){
          // Insert OK
          $error=0;
        }else{
          // User already existing
          $error=3;
        }
      }catch(PDOException $e){
        if($e->getCode()==23000){
          // Duplicate value
          $error=3;
        }else{
          // DB error (And/Or PDOException)
          $error=-1;
          $error_extra=$e->getMessage();
        }
      }
    }

    return array($error, $error_extra);
  }
}

return new UserModel();