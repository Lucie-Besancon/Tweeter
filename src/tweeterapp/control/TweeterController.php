<?php

namespace tweeterapp\control;

/* Classe TweeterController :
*
* RÃ©alise les algorithmes des fonctionnalitÃ©s suivantes:
*
*  - afficher la liste des Tweets
*  - afficher un Tweet
*  - afficher les tweet d'un utilisateur
*  - afficher la le formulaire pour poster un Tweet
*  - afficher la liste des utilisateurs suivis
*  - Ã©valuer un Tweet
*  - suivre un utilisateur
*
*/

class TweeterController extends \mf\control\AbstractController {


  /* Constructeur :
  *
  * Appelle le constructeur parent
  *
  * c.f. la classe \mf\control\AbstractController
  *
  */

  public function __construct(){
    parent::__construct();
  }


  /* MÃ©thode viewHome :
  *
  * RÃ©alise la fonctionnalitÃ© : afficher la liste de Tweet
  *
  */

  public function viewHome(){

    /* Algorithme :
    *
    *  1 RÃ©cupÃ©rer tout les tweet en utilisant le modÃ¨le Tweet
    *  2 Parcourir le rÃ©sultat
    *      afficher le text du tweet, l'auteur et la date de crÃ©ation
    *  3 Retourner un block HTML qui met en forme la liste
    *
    */

    $tweets = \tweeterapp\model\Tweet::select()->orderBy('created_at','DESC')->offset(0)->limit(10);
    $lignes = $tweets->get();

    /*
    * Créer une nouvelle vue qui fait un appel de render()
    * TweeterView attend un paramètre $data
    * $lignes devient $data
    */
    
    $view = new \tweeterapp\view\TweeterView($lignes);
    echo $view->render("viewHome");
  }


  /* MÃ©thode viewTweet :
  *
  * RÃ©alise la fonctionnalitÃ© afficher un Tweet
  *
  */

  public function viewTweet(){

    /* Algorithme :
    *
    *  1 L'identifiant du Tweet en question est passÃ© en paramÃ¨tre (id)
    *      d'une requÃªte GET
    *  2 RÃ©cupÃ©rer le Tweet depuis le modÃ¨le Tweet
    *  3 Afficher toutes les informations du tweet
    *      (text, auteur, date, score)
    *  4 Retourner un block HTML qui met en forme le Tweet
    *
    *  Erreurs possibles : (*** Ã  implanter ultÃ©rieurement ***)
    *    - pas de paramÃ¨tre dans la requÃªte
    *    - le paramÃ¨tre passÃ© ne correspond pas a un identifiant existant
    *    - le paramÃ¨tre passÃ© n'est pas un entier
    *
    */

    /*
    *   $data[] 
    *   0 : ok
    *   1 : erreur
    */
    $data = [];

    /* récupération de l'ID */
    if(isset($this->request->get['id'])){

      /* si l'ID est valide */
      if(filter_var($this->request->get['id'], FILTER_VALIDATE_INT)){

        /* requête */
        $requete = \tweeterapp\model\Tweet::select()->where('id', 'like', $this->request->get['id']);
        $lignes = $requete->first();
        
        /* si l'ID n'existe pas */
        if(!$lignes)
        $data[1] = "Aucun tweet avec cet ID.";

        /* si l'ID existe */
        else
        $data[0] = $lignes;
      }

      /* si l'ID n'est pas valide */
      else{
        $data[1] = "L'ID doit être un nombre.";
      }
    }

    /* si aucun ID */
    else{
      $data[1] = "Aucun ID en paramètre.";
    }

    $view = new \tweeterapp\view\TweeterView($data);

    /* nouvelle vue pour afficher le tweet
       appel de render() */
    echo $view->render("viewTweet");
  }


  /* MÃ©thode viewUserTweets :
  *
  * RÃ©alise la fonctionnalitÃ© afficher les tweet d'un utilisateur
  *
  */

  public function viewUserTweets(){

    /*
    *
    *  1 L'identifiant de l'utilisateur en question est passÃ© en
    *      paramÃ¨tre (id) d'une requÃªte GET
    *  2 RÃ©cupÃ©rer l'utilisateur et ses Tweets depuis le modÃ¨le
    *      Tweet et User
    *  3 Afficher les informations de l'utilisateur
    *      (non, login, nombre de suiveurs)
    *  4 Afficher ses Tweets (text, auteur, date)
    *  5 Retourner un block HTML qui met en forme la liste
    *
    *  Erreurs possibles : (*** Ã  implanter ultÃ©rieurement ***)
    *    - pas de paramÃ¨tre dans la requÃªte
    *    - le paramÃ¨tre passÃ© ne correspond pas a un identifiant existant
    *    - le paramÃ¨tre passÃ© n'est pas un entier
    *
    */

    /*
    *   $data[]
    *   0 : user informations
    *   1 : user Tweets
    *   2 : erreur
    */
    $data = [];

    /* récupération de l'ID */
    if(isset($this->request->get['id'])){

      /* si l'ID est valide */
      if(filter_var($this->request->get['id'], FILTER_VALIDATE_INT)){

        /* requête */
        $requeteUser = \tweeterapp\model\User::select()->where('id', 'like', $this->request->get['id']);
        $user = $requeteUser->first();

        /* si l'ID n'existe pas */
        if(!$user)
        $data[2] = "Aucun utilisateur avec cet ID.";

        /* si l'ID existe */
        else{
          $data[0] = $user;
          $data[1] = $user->tweets();
        }
      }

      /* si l'ID n'est pas valide */
      else{
        $data[2] = "L'ID doit être un nombre.";
      }
    }

    /* si aucun ID */
    else{
      $data[2] = "Aucun ID en paramètre.";
    }

    $view = new \tweeterapp\view\TweeterView($data);
    echo $view->render("viewUserTweets");
  }

  public function viewPostTweet(){
    $view = new \tweeterapp\view\TweeterView([]);

    /* nouvelle vue pour afficher le tweet
       appel de render() */
    echo $view->render("viewPostTweet");
  }

  public function postTweet(){
    $user = \tweeterapp\model\User::select('id')->where('username', 'like', $_SESSION['user_login'])->first();
    $id = $user->id;
    $router = new \mf\router\router();

    if(isset($_POST['tweet'])){
      $text = filter_var($_POST['tweet'], FILTER_SANITIZE_SPECIAL_CHARS);
      $tweet = new \tweeterapp\model\Tweet();
      $tweet->text = $text;
      $tweet->author = $id;
      $tweet->save();
      header("Location: {$router->urlFor("home")}");
      exit();
    }
    else{
      echo "Une erreur a été rencontrée, réessayez.<a href=\"{$router->urlFor("createtweet")}\">Retour</a>";
    }
  }

  public function viewFollowing(){
    /*
    *   $data[]
    *   0 : list follow
    *   1 : nombre de follows
    */
    $data = [];

    $user = \tweeterapp\model\User::select()->where('username', 'like', $_SESSION['user_login'])->first();

    $following = $user->following(10, 0);
    $nbFollow = \tweeterapp\model\Follow::getNbFollow($user->id);
    $data[0] = $following;
    $data[1] = $nbFollow;

    $view = new \tweeterapp\view\TweeterView($data);

    /* nouvelle vue pour afficher le tweet
       appel de render() */
    echo $view->render("viewFollowing");
  }
}
