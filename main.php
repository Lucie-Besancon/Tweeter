<?php

session_start();

/* chargement automatique des classes d'Eloquent
    dans le répertoire vendor */
require_once 'vendor/autoload.php';
require_once "src/mf/utils/AbstractClassLoader.php";
require_once "src/mf/utils/ClassLoader.php";

/* instancier la classe Loader */
$loader = new \mf\utils\ClassLoader('src');
$loader->register();

use tweeterapp\model\Tweet;
use tweeterapp\model\User;

/* retourne les config sous forme 
    d'un tableau associatif */
$config = parse_ini_file("conf/config.ini");

/* instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection($config);    /* ajout d'une connexion avec nos paramètres */
$db->setAsGlobal();             /* la généraliser à tout le projet */
$db->bootEloquent();            /* établir la connexion */

/* routeur */
$router = new \mf\router\Router();

/* contrôleur */
$ctrl = new tweeterapp\control\TweeterController();

/* ajout des différentes routes 
   dans la table de routage */
$router->addRoute('home', '/home', '\tweeterapp\control\TweeterController', 'viewHome', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('viewtweet', '/view', '\tweeterapp\control\TweeterController', 'viewTweet', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('viewusertweets', '/user', '\tweeterapp\control\TweeterController', 'viewUserTweets', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('createtweet', '/post', '\tweeterapp\control\TweeterController', 'viewPostTweet', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('sendtweet', '/send', '\tweeterapp\control\TweeterController', 'postTweet', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
$router->addRoute('login', '/login', '\tweeterapp\control\TweeterAdminController', 'login', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('checklogin', '/checklogin', '\tweeterapp\control\TweeterAdminController', 'checkLogin', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('signup', '/signup', '\tweeterapp\control\TweeterAdminController', 'signup', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('checksignup', '/check_signup', '\tweeterapp\control\TweeterAdminController', 'checkSignup', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('logout', '/logout', '\tweeterapp\control\TweeterAdminController', 'logout', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
$router->addRoute('following', '/following', '\tweeterapp\control\TweeterController', 'viewFollowing', tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

/* définir une route par défaut */
$router->setDefaultRoute('/home');

/* titre onglet */
tweeterapp\view\TweeterView::setAppTitle('MiniTweeTR');

/* style */
tweeterapp\view\TweeterView::addStyleSheet("html/style.css");

/* exécution des routes */
$router->run();
