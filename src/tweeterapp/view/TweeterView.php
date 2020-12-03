<?php

namespace tweeterapp\view;

class TweeterView extends \mf\view\AbstractView {

  /* Constructeur
  *
  * Appelle le constructeur de la classe parent
  */
  protected $router;

  public function __construct( $data ){
    parent::__construct($data);
    $this->router = new \mf\router\router();
  }

  /* MÃ©thode renderHeader
  *
  *  Retourne le fragment HTML de l'entÃªte (unique pour toutes les vues)
  */
  private function renderHeader(){
    return '<h1>MiniTweeTR</h1>';
  }

  /* MÃ©thode renderFooter
  *
  * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
  */
  private function renderFooter(){
    return 'La super app créée en Licence Pro 2020';
  }

  /* MÃ©thode renderHome
  *
  * Vue de la fonctionalitÃ© afficher tous les Tweets.
  *
  */

  private function renderHome(){

    /*
    * Retourne le fragment HTML qui affiche tous les Tweets.
    *
    * L'attribut $this->data contient un tableau d'objets tweet.
    *
    */
    if(sizeof($this->data) > 0){
      $code_html = '<article class="theme-backcolor2">  <h2>Latest Tweets on TweeterApp</h2>';
      foreach ($this->data as $v) {
        $code_html .= <<<EOT
        <div class="tweet">
        <a href="{$this->router->urlFor('viewtweet', [array('id', $v->id)])}">
        <div class="tweet-text">{$v->text}</div>
        </a>
        <div class="tweet-footer">
        <span class="tweet-timestamp">{$v->created_at}</span>
        <span class="tweet-author">
        <a href="{$this->router->urlFor('viewusertweets', [array('id', $v->author)])}">{$v->author()->fullname}</a>
        </span>
        </div>
        </div>
EOT;
      }
      $code_html .= "</article>";
      return $code_html;
    }
    else{
      return '<div class="noElement">Aucun tweet disponible</div>';
    }
  }

  /* MÃ©thode renderUeserTweets
  *
  * Vue de la fonctionalitÃ© afficher tout les Tweets d'un utilisateur donnÃ©.
  *
  */

  private function renderUserTweets(){

    /*
    * Retourne le fragment HTML pour afficher
    * tous les Tweets d'un utilisateur donnÃ©.
    *
    * L'attribut $this->data contient un objet User.
    *
    */
      $code_html = "<article class=\"theme-backcolor2\">  <h2>Tweets from  {$this->data[0]->fullname}</h2><h3>{$this->data[0]->followers} followers</h3>";
      if(!sizeof($this->data[1]) < 1){
        foreach ($this->data[1] as $v) {
          $code_html .= <<<EOT
          <div class="tweet">
          <a href="{$this->router->urlFor('viewtweet', [array('id', $v->id)])}">
          <div class="tweet-text">{$v->text}</div>
          </a>
          <div class="tweet-footer">
          <span class="tweet-timestamp">{$v->created_at}</span>
          <span class="tweet-author">
          <a href="{$this->router->urlFor('viewusertweets', [array('id', $v->author)])}">{$this->data[0]->fullname}</a>
          </span>
          </div>
          </div>
EOT;
        }
      }else{
        $code_html .= "<div class=\"noElement\">{$this->data[0]->fullname} n'a pas encore tweeté !</div>";
      }

      $code_html .= "</article>";
      return $code_html;
  }

  /* MÃ©thode renderViewTweet
  *
  * RrÃ©alise la vue de la fonctionnalitÃ© affichage d'un tweet
  *
  */

  private function renderViewTweet(){

    /*
    * Retourne le fragment HTML qui rÃ©alise l'affichage d'un tweet
    * en particuliÃ©
    *
    * L'attribut $this->data contient un objet Tweet
    *
    */

    //"Identifiant = $ligne->id, Text = $ligne->text, <span style=\"color: green\">Créé par : ". $ligne->author()->fullname." le : ".$ligne->created_at."</span>, <span style=\"color: blue\">Dernière modification le : ".$ligne->updated_at."</span>\n<br>";
    if(!isset($this->data[1])){
      $tweet = $this->data[0];
      $code_html = "<article class=\"theme-backcolor2\">";
      $code_html .= <<<EOT
      <div class="tweet">
      <a href="{$this->router->urlFor('viewtweet', [array('id', $tweet->id)])}">
      <div class="tweet-text">
      {$tweet->text}
      </div>
      </a>
      <div class="tweet-footer">
      <span class="tweet-timestamp">
      {$tweet->created_at}
      </span>
      <span class="tweet-author">
      <a href="{$this->router->urlFor('viewusertweets', [array('id', $tweet->author()->id)])}">
      {$tweet->author()->fullname}
      </a>
      </span>
      </div>
      <div class="tweet-footer">
      <hr>
      <span class="tweet-score tweet-control">
      {$tweet->score}
      </span>
      </div>
      </div>
EOT;

      $code_html .= "</article>";
      return $code_html;
    }
    else{
      return "<div class=\"noElement\">{$this->data[1]}</div>";
    }
  }



  /* MÃ©thode renderPostTweet
  *
  * Realise la vue de rÃ©gider un Tweet
  *
  */
  protected function renderPostTweet(){

    /* MÃ©thode renderPostTweet
    *
    * Retourne la framgment HTML qui dessine un formulaire pour la rÃ©daction
    * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
    *
    */
    $code_html = <<<EOT
    <form class="" action="{$this->router->urlFor('sendtweet')}" method="post">
      <textarea id="story" name="tweet" placeholder="Saisir un nouveau tweet"
          style="width: 70%;" rows="5" cols="33"></textarea>
      <input style="width: 70%;" type="submit" value="Envoyer le tweet">
    </form>
EOT;
    return $code_html;
  }

  protected function renderLogin(){
    $code_html = <<<EOT
    <form class="" action="{$this->router->urlFor('checklogin')}" method="post">
      <label for="username" style="display:block; width: 100%;margin-bottom: 10px;">Votre nom d'utilisateur :</label>
      <input type="text" name="username" placeholder="Nom d'utilisateur" style="margin-bottom: 10px;"/>
      <label for="password" style="display:block; width: 100%;margin-bottom: 10px;">Votre mot de passe :</label>
      <input type="password" name="password" placeholder="Mot de passe" style="margin-bottom: 10px;"/>
      <input type="submit" value="Se connecter" style="display:block; width: 50%; margin: 0 auto;margin-bottom: 10px;"/>
    </form>
EOT;
    return $code_html;
  }

  protected function renderFollowers(){
      $code_html = "<article class=\"theme-backcolor2\">  <h2>Liste des utilisateurs qui vous suivent :</h2><h3>{$this->data[1]} followers</h3>";
      if(!sizeof($this->data[0]) < 1){
        foreach ($this->data[0] as $v) {
          $code_html .= <<<EOT
          <a href="{$this->router->urlFor('viewusertweets', [array('id', $v->id)])}">{$v->fullname}</a>
          </span>
          </div>
          </div>
EOT;
        }
      }else{
        $code_html .= "<div class=\"noElement\">Vous n'avez aucun abonné !</div>";
      }

      $code_html .= "</article>";
      return $code_html;
  }

  protected function renderFollowing(){
      $code_html = "<article class=\"theme-backcolor2\">  <h2>Liste des utilisateurs que vous suivez :</h2><h3>{$this->data[1]} follows</h3>";
      if(!sizeof($this->data[0]) < 1){
        foreach ($this->data[0] as $v) {
          $code_html .= <<<EOT
          <a href="{$this->router->urlFor('viewusertweets', [array('id', $v->id)])}">{$v->fullname}</a>
          </span>
          </div>
          </div>
EOT;
        }
      }
      else{
        $code_html .= "<div class=\"noElement\">Vous n'avez aucun abonnement !</div>";
      }

      $code_html .= "</article>";
      return $code_html;
  }

  protected function renderSignup(){
    $code_html = <<<EOT
    <form class="" action="{$this->router->urlFor('checksignup')}" method="post">
      <h1 style="text-align: center;">Créer votre compte :</h1>
      <label for="username" style="display:block; width: 100%;margin-bottom: 10px;">Votre nom d'utilisateur :</label>
      <input type="text" name="username" placeholder="Nom d'utilisateur" style="margin-bottom: 10px;"/>
      <label for="fullname" style="display:block; width: 100%;margin-bottom: 10px;">Votre nom complet :</label>
      <input type="text" name="fullname" placeholder="Nom complet" style="margin-bottom: 10px;"/>
      <label for="password" style="display:block; width: 100%;margin-bottom: 10px;">Votre mot de passe :</label>
      <input type="password" name="password" placeholder="Mot de passe" style="margin-bottom: 10px;"/>
      <input type="submit" value="Valider l'inscription" style="display:block; width: 50%; margin: 0 auto;margin-bottom: 10px;">
    </form>
EOT;
    return $code_html;
  }

  protected function renderTopMenu(){
    $app_root = (new \mf\utils\HttpRequest())->root;
    $auth = new \tweeterapp\auth\TweeterAuthentification();

    if($auth->logged_in){
    $code_html = <<<EOT
    <nav id="header-nav">
      <a href="{$this->router->urlFor('home')}">
        <img src="{$app_root}/html/img/home.png" alt="Accueil"/>
      </a>
      <a href="{$this->router->urlFor('following')}">
        <img src="{$app_root}/html/img/followees.png" alt="Abonnement"/>
      </a>
      <a href="{$this->router->urlFor('logout')}">
        <img src="{$app_root}/html/img/logout.png" alt="Se déconnecter"/>
      </a>
    </nav>
EOT;
    }else{
      $code_html = <<<EOT
      <nav id="header-nav">
        <a href="{$this->router->urlFor('home')}">
          <img src="{$app_root}/html/img/home.png" alt="Accueil"/>
        </a>
        <a href="{$this->router->urlFor('login')}">
          <img src="{$app_root}/html/img/login.png" alt="Se connecter"/>
        </a>
        <a href="{$this->router->urlFor('signup')}">
          <img src="{$app_root}/html/img/signup.png" alt="S'inscrire"/>
        </a>
      </nav>
EOT;
    }

    return $code_html;
  }

  protected function renderBottomMenu(){
    $code_html = '';
    $auth = new \tweeterapp\auth\TweeterAuthentification();

    if($auth->logged_in && !$this->router->checkActualRoute('createtweet')){
    $code_html = <<<EOT
    <nav id="footer-nav">
      <a href="{$this->router->urlFor('createtweet')}">
        <button>Créer un tweet</button>
      </a>
    </nav>
EOT;
    }

    return $code_html;
  }


  /* MÃ©thode renderBody
  *
  * Retourne la framgment HTML de la balise <body> elle est appelÃ©e
  * par la mÃ©thode hÃ©ritÃ©e render.
  *
  */

  protected function renderBody($selector){

    /*
    * voire la classe AbstractView
    *
    */
    $body = "";

    $body .= '<header class="theme-backcolor1">'.$this->renderHeader().$this->renderTopMenu()."</header>";

    if(isset($_SESSION["info"])){
      $body .= <<<EOT
        <div class="info-block" style="color: {$_SESSION["info"][1]}">
          <p>{$_SESSION["info"][0]}</p>
        </div>
EOT;
      unset($_SESSION["info"]);
    }

    if($selector == "viewHome"){
      $body .= '<section>'.$this->renderHome()."</section>";
    }elseif($selector == "viewTweet"){
      $body .= '<section>'.$this->renderViewTweet()."</section>";
    }elseif ($selector == "viewUserTweets") {
      $body .= '<section>'.$this->renderUserTweets()."</section>";
    }elseif ($selector == "viewPostTweet") {
      $body .= '<section>'.$this->renderPostTweet()."</section>";
    }elseif ($selector == "viewLogin") {
      $body .= '<section>'.$this->renderLogin()."</section>";
    }elseif ($selector == "viewFollowers") {
      $body .= '<section>'.$this->renderFollowers()."</section>";
    }
    elseif ($selector == "viewFollowing") {
      $body .= '<section>'.$this->renderFollowing()."</section>";
    }
    elseif ($selector == "viewSignup") {
      $body .= '<section>'.$this->renderSignup()."</section>";
    }

    $body .= $this->renderBottomMenu();
    $body .= '<footer class="theme-backcolor1">'.$this->renderFooter()."</footer>";
    return $body;
  }
}
