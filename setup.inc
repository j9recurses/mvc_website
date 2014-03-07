<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

//set_include_path('/users/dputnam/:/users/dputnam/PEAR/'.get_include_path());
set_include_path('/home/cactus1/pear/pear/:'.get_include_path());

// Create all of these directories:
// mkdir -p ~/app/{models,views,controllers,config,views/layouts,sessions,lib,db,classes,controllers/decorators,classes/helpers,lib/simpletest}

define('APP',dirname(__FILE__) .     '/app/'       );
define('MODELS'     , APP      .     'models/'     );  
define('CONFIG'     , APP      .     'config/'     );  
define('CLASSES'    , APP      .     'classes/'    );  
define('VIEWS'      , APP      .     'views/'      );  
define('LAYOUTS'    , VIEWS    .     'layouts/'    );  
define('IMG_DIR', dirname(__FILE__). '/images/'    ); 
define('SESSIONS'   , APP      .     'sessions/'   );  
define('LIB'        , APP      .     'lib/'        );  
define('DB'         , APP      .     'db/'         );  
define('CACHE'      , APP      .     'cache/'      );  
define('CONTROLLERS', APP    .       'controllers/');
define('DECORATORS' , CONTROLLERS .  'decorators/' );  
define('XML'        , APP .          'xml'         );
define('DB_FILE'    , DB .           'default.sqlite');

if(!file_exists(SESSIONS)) die('Please create the sessions directory: ' . SESSIONS);

// MISCELLANEOUS
date_default_timezone_set('America/Los_Angeles');
//$curr_user = get_current_user();

//if($curr_user == 'dputnam') {
 //   define("IMAGE_DIR","/users/$curr_user/public_html/images/130b/");
 //   define("UPLOAD_TMP_DIR","/users/$curr_user/public_html/images/130b/");
   // ini_set('upload_tmp_dir',"/users/$curr_user/public_html/images/130b");
//}
//else {
    define("IMAGE_DIR","/home/cactus1/iheartlovesongs.com/mvcimages/inputimages/");
    define("UPLOAD_TMP_DIR","/home/cactus1/iheartlovesongs.com/mvcimages/inputimages/");
    ini_set('upload_tmp_dir',"/home/cactus1/iheartlovesongs.com/mvcimages/inputimages/");
//}

// START SESSIONS
session_save_path(SESSIONS);
session_name(substr(md5('lovesongs'),0,8));
session_set_cookie_params('', '.iheartlovesongs');
session_start();


// REQUIRE FILES WE USE WITH EVERY REQUEST.
require_once CONTROLLERS . 'application_controller.php';
require_once CLASSES . 'request.php';
require_once CLASSES . 'decorator.php';
require_once LIB . 'library.php';

// AUTOLOAD
/**
 *  Autoload classes to simplify file loading.
 *  Handle Controller classes first.
 *
 */
function __autoload($klass) {
    if((strpos(strtolower($klass),'controller')) !== FALSE) {
        $klass =substr($klass,0,strpos(strtolower($klass),'controller'));
        if(file_exists(CONTROLLERS.strtolower($klass.'_controller.php'))) {
            require_once CONTROLLERS.strtolower($klass) . '_controller.php';
            return true;
        }
    }
    elseif((strpos(strtolower($klass),'decorator')) !== FALSE) {
        $klass =substr($klass,0,strpos(strtolower($klass),'decorator'));
        if(file_exists(DECORATORS.strtolower($klass.'_decorator.php'))) {
            require_once DECORATORS.strtolower($klass) . '_decorator.php';
            return true;
        }
    }
    elseif(file_exists(MODELS . strtolower($klass). '.php')) {
        require_once(MODELS .strtolower($klass) . '.php');
    }
    elseif(file_exists(CLASSES . strtolower($klass). '.php')) {
        require_once(CLASSES .strtolower($klass) . '.php');
    }
} 
