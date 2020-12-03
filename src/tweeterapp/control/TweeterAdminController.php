<?php

namespace tweeterapp\control;

class TweeterAdminController extends \mf\control\AbstractController {

  public function __construct(){
    parent::__construct();
  }

  public function login(){
    $auth = new \tweeterapp\auth\TweeterAuthentification();

    if($auth->logged_in){
      /* 
      *  appel de header() et pas de executeRoute()
      *  pour que l'url change
      *  autrement, erreur si rafraîchissement de la page
      */

      $router = new \mf\router\router();
      header("Location: ".$router->urlFor("home"));
      exit();

    /* vue pour se connecter */
    }else{
      $view = new \tweeterapp\view\TweeterView([]);

      /* nouvelle vue, appel de render() */
      echo $view->render("viewLogin");
    }
  }

  /* vérification du mot de passe */
  public function checkLogin(){
    $auth = new \tweeterapp\auth\TweeterAuthentification();

    if($auth->logged_in){
      $router = new \mf\router\router();
      header("Location: ".$router->urlFor("home"));
      exit();
    }

    else{
      /*
      *   $data[]
      *   0 : followers
      *   1 : nombre de followers
      */
      $data = [];

      /* si username et mot de passe sont renseignés */
      if(isset($this->request->post['username']) && isset($this->request->post['password'])){
        try{

          /* vérifier qu'aucun caractère ne soit mal interprété
             avec FILTER_SANITIZE_SPECIAL_CHARS */
          $username = filter_var( $this->request->post['username'], FILTER_SANITIZE_SPECIAL_CHARS );
          $password = filter_var( $this->request->post['password'], FILTER_SANITIZE_SPECIAL_CHARS );

          $auth->loginUser($username, $password);
          $user = \tweeterapp\model\User::select()->where('username', 'like', $_SESSION['user_login'])->first();

          $followers = $user->followers(10, 0);
          $data[0] = $followers;          /* tableau des followers */
          $data[1] = $user->followers;    /*nombre de followers */

          $view = new \tweeterapp\view\TweeterView($data);

          /* nouvelle vue, appel de render() */
          echo $view->render("viewFollowers");

        }catch(\mf\auth\exception\AuthentificationException $e){
          /* prend un tableau en valeur 
          *  0 : message de l'erreur avec getMessage()
          *  1 : couleur
          */
          $_SESSION["info"] = array($e->getMessage(), "red");
          \mf\router\router::executeRoute('login');
        }

      }else{
        $_SESSION["info"] = array("Veuillez renseigner tous les champs.", "red");
        \mf\router\router::executeRoute('login');
      }
    }
  }

  public function logout(){
    $auth = new \tweeterapp\auth\TweeterAuthentification();
    $auth->logout();
    $router = new \mf\router\router();
    header("Location: ".$router->urlFor("home"));
    exit();
  }

  public function signup(){
    $auth = new \tweeterapp\auth\TweeterAuthentification();
    if($auth->logged_in){
      $router = new \mf\router\router();
      header("Location: ".$router->urlFor("home"));
      exit();
    }else{
      $view = new \tweeterapp\view\TweeterView([]);
      echo $view->render("viewSignup");
    }
  }

  public function checkSignup(){
    $auth = new \tweeterapp\auth\TweeterAuthentification();
    if($auth->logged_in){
      $router = new \mf\router\router();
      header("Location: ".$router->urlFor("home"));
      exit();
    }else{
      if(isset($this->request->post["username"]) && isset($this->request->post["fullname"]) && isset($this->request->post["password"])){
        $auth = new \tweeterapp\auth\TweeterAuthentification();
        try{
          $username = filter_var( $this->request->post['username'], FILTER_SANITIZE_SPECIAL_CHARS );
          $password = filter_var( $this->request->post['password'], FILTER_SANITIZE_SPECIAL_CHARS );
          $fullname = filter_var( $this->request->post['fullname'], FILTER_SANITIZE_SPECIAL_CHARS );
          $auth->createUser($username, $password, $fullname);
          $router = new \mf\router\router();
          header("Location: ".$router->urlFor("home"));
          exit();
        }
        catch(\mf\auth\exception\AuthentificationException $e){
          $_SESSION["info"] = array($e->getMessage(), "red");
          \mf\router\router::executeRoute('signup');
        }
      }
    }
  }

}
