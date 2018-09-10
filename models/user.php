<?php
class UserModel{
  var $db,$session;

  // Constructor, linkers
  function __construct(){$db=null;$session=null;}
  function linkDB($db){
    $this->db=$db;
    if($this->session!=null) $this->session->linkDB($db);
  }
  function linkSession($session){
    if($this->db!=null) $session->linkDB($this->db);
    $this->session=$session;
  }

  // Methods
  function isEmailValid($email){
    return ($email!=null && filter_var($email, FILTER_VALIDATE_EMAIL));
  }

  function isPasswordValid($password){
    return ($password!=null && strlen($password)>=8);
  }

  function register($email, $password){
    $error=0; // 0 means "success"
    $error_extra=null;

    if(!$this->isEmailValid($email)){
      // Email is NOT valid
      $error=1;
    }else if(!$this->isPasswordValid($password)){
      // Password is NOT valid
      $error=2;
    }else{
      // Generate password hashed using bcrypt (To store it in database)
      $password_hashed = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
      
      try{
        // Insert user into DB
        $statement = $this->db->getPDO()->prepare(
          "INSERT INTO pizzapp_users (email, password) VALUES (:email, :password)"
        );
    
        $this->db->pdo_multibindParams($statement, array(
          'email'=>$email,
          'password'=>$password_hashed
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

  function login($email, $password){
    $error=0; // 0 means "success"
    $error_extra=null;

    if(!$this->isEmailValid($email)){
      $error=1; // Email is NOT valid
    }else if(!$this->isPasswordValid($password)){
      $error=2; // Password is NOT valid
    }else{
      try{
        $statement = $this->db->getPDO()->prepare(
          "SELECT id, password FROM pizzapp_users WHERE email = :email LIMIT 1"
        );

        $this->db->pdo_multibindParams($statement, array('email'=>$email));
        if($statement->execute()){
          $userdata = $statement->fetchAll()[0];
          $password_db = $userdata['password']; // Password inside database of relative user

          if(password_verify($password, $password_db)){
            $user_id=$userdata['id'];
            $this->session->activateUser($user_id, $email);
            $error=0;
          }else{$error=4;}
        }else{$error=3;}
      }catch(PDOException $e){
        $error=-1;
        $error_extra=$e->getMessage();
      }
    }

    return array($error, $error_extra);
  }
}

return new UserModel();