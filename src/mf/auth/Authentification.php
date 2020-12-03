<?php

namespace mf\auth;

class Authentification extends AbstractAuthentification {

  public function __construct(){
    /*
    *  tableau super global $_SESSION
    * qui contient user_login et access_level
    */
    if(isset($_SESSION['user_login'])){
      /* récupère les valeurs */
      $this->user_login = $_SESSION['user_login'];
      $this->access_level = $_SESSION['access_level'];
      $this->logged_in = true;
    }
    else{
      /* sinon, pas d'accès, pas de connexion */
      $this->user_login = null;
      $this->access_level = self::ACCESS_LEVEL_NONE;
      $this->logged_in = false;
    }
  }

  /* mise à jour des informations de la session */
  protected function updateSession($username, $level){
    $this->user_login = $username;
    $this->access_level = $level;
    $_SESSION['user_login'] = $username;
    $_SESSION['access_level'] = $level;

    /* l'utilisateur est connecté */
    $this->logged_in = true;
  }

  /* déconnexion */
  public function logout(){
    /* unset pour supprimer les valeurs */
    unset($_SESSION['user_login']);
    unset($_SESSION['access_level']);
    $this->user_login = null;

    /* plus d'accès, utilisateur pas connecté */
    $this->access_level = self::ACCESS_LEVEL_NONE;
    $this->logged_in = false;
  }

  /* vérifier le droit d'accès */
  public function checkAccessRight($requested){
    if($requested > $this->access_level)
      return false;
    else
      return true;
  }

  public function login($username, $db_pass, $given_pass, $level){

    /* vérification du mot de passe */
    if(!$this->verifyPassword($given_pass, $db_pass)){

      /* génération d'une nouvelle exception */
      throw new \mf\auth\exception\AuthentificationException('Le mot de passe ne correspond pas.');
    }else {
      $this->updateSession($username, $level);
    }
  }

  /* hachage du mot de passe */
  protected function hashPassword($password){
    return password_hash($password, PASSWORD_DEFAULT);
  }

  /* vérification du mot de passe */
  protected function verifyPassword($password, $hash){
    return password_verify($password, $hash);
  }
}
