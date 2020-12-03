<?php

namespace mf\router;

/* classe Router hérite de AbstractRouter */
class Router extends AbstractRouter {

  public function __construct(){
      /* pour initialiser le constructeur de AbstractRouter */
      parent::__construct();
  }

  public function addRoute($name, $url, $ctrl, $mth, $access_level){
    /* self:: static */

    /**
     * à chaque route, un contrôleur qui exécute des méthodes,
     * affiche une nouvelle vue
     */
    self::$routes[$url] = array($ctrl, $mth, $access_level);
    self::$aliases[$name] = $url;
  }

  /* définir une route par défaut */
  public function setDefaultRoute($url){
    self::$aliases['default'] = $url;
  }

  /* vérifier la route active */
  public function checkActualRoute($route_name){
    if(self::$aliases[$route_name] == $this->http_req->path_info)
      return true;
    else
      return false;
  }

  public function run(){
    $user = new \tweeterapp\auth\TweeterAuthentification();
    
    /* si l'url existe dans le tableau routes */
    if($this->http_req->path_info && isset(self::$routes[$this->http_req->path_info])){
      
      /* si le user a les droits d'accès */
      if($user->checkAccessRight(self::$routes[$this->http_req->path_info][2])){
        
        /* récupère les données */
        $ctrl_name = self::$routes[$this->http_req->path_info][0];
        $method_name = self::$routes[$this->http_req->path_info][1];

        /* instancie contrôleur */
        $Controller = new $ctrl_name();
        /* exécute la méthode */
        $Controller->$method_name();
      }
      else{
        /* sinon pas les droits, url par défaut */
        $ctrl_name = self::$routes[self::$aliases['default']][0];// /!\/!\ REPETITION DE CODE /!\/!\
        $method_name = self::$routes[self::$aliases['default']][1];

        $Controller = new $ctrl_name();
        $Controller->$method_name();
      }
    }
    else{
      /* sinon url n'existe pas, url par défaut */
      $ctrl_name = self::$routes[self::$aliases['default']][0];// /!\/!\ REPETITION DE CODE /!\/!\
      $method_name = self::$routes[self::$aliases['default']][1];

      $Controller = new $ctrl_name();
      $Controller->$method_name();
    }
  }

  /*
   * MÃ©thode urlFor : retourne l'URL d'une route depuis son alias
   *
   * ParamÃ¨tres :
   *
   * - $route_name (String) : alias de la route
   * - $param_list (Array) optionnel : la liste des paramÃ¨tres si l'URL prend
   *          de paramÃ¨tre GET. Chaque paramÃ¨tre est reprÃ©sentÃ© sous la forme
   *          d'un tableau avec 2 entrÃ©es : le nom du paramÃ¨tre et sa valeur
   *
   * Algorthme:
   *
   * - Depuis le nom du scripte et l'URL stockÃ© dans self::$routes construire
   *   l'URL complÃ¨te
   * - Si $param_list n'est pas vide
   *      - Ajouter les paramÃ¨tres GET a l'URL complÃ¨te
   * - retourner l'URL
   *
   */

  public function urlFor($route_name, $param_list=[]){
    $url = self::$aliases[$route_name];

    /* construction des urls */    
    if(sizeof($param_list) > 0){
      foreach ($param_list as $v) {
        $url .= "?".$v[0]."=".$v[1];
      }
    }

    return $this->http_req->script_name.$url;
  }

  static function executeRoute($route_name){
    /* prend en paramètre une chaîne de caractères
       et exécute la route */

    /* tableau statique, existe tout le temps */

    $ctrl_name = self::$routes[self::$aliases[$route_name]][0];
    $method_name = self::$routes[self::$aliases[$route_name]][1];

    $Controller = new $ctrl_name();
    $Controller->$method_name();
  }

}
