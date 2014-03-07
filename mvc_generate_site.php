#!/usr/local/bin/php-cli
<?php
///below, the script creates a lean, php mvc template

// Make sure to turn on argc and argv
ini_set('register_argc_argv', 1);

// Define some bottom-line directories
// relative to the script itself.
///$app_dir = dirname(dirname(__FILE__)) . '/app/';
$app_dir = '/home/cactus1/iheartlovesongs.com/app/';
// Supporting directories. You can add other directories
// here if you need them.
$controller_dir = $app_dir  . 'controllers/';
$model_dir      = $app_dir  . 'models/';
$view_dir       = $app_dir  . 'views/';
$layouts_dir    = $view_dir . 'layouts/';
$config_dir     = $app_dir  . 'config/';
$classes_dir    = $app_dir  . 'classes/';
$lib_dir        = $app_dir  . 'lib/';
$sessions_dir   = $app_dir  . 'sessions/';
$db_dir         = $app_dir  . 'db/';
$decorators_dir = $controller_dir  . 'decorators/';

$directories = array('controllers','models','views','views/layouts','db','config','classes','lib','sessions');

/*if(!file_exists(strtolower($app_dir))) {
   print 'Creating initial directory structure.'."\n";
    mkdir($app_dir,0700,true);
    mkdir($model_dir,0700,true);
    mkdir($controller_dir,0700,true);
    mkdir($decorators_dir,0700,true);
    mkdir($view_dir,0700,true);
    mkdir($view_dir.'default',0700,true);
    mkdir($layouts_dir,0700,true);
    mkdir($lib_dir,0700,true);
    mkdir($config_dir,0700,true);
    mkdir($classes_dir,0700,true);
    mkdir($sessions_dir,0700,true);
    mkdir($db_dir,0700,true);

   print "\n";
    $files = file(dirname(__FILE__).'/files/required_files.txt');
    print "Copying files: \n";
    foreach($files as $file) {
       $file = trim($file);
       if(!file_exists(strtolower(dirname(__FILE__).'/../'.$file))) {
          if(!file_exists(strtolower(dirname(__FILE__).'/../'.dirname($file)))) {
              mkdir(dirname(__FILE__).'/../'.dirname($file),0700,true);
          }
          if(is_dir(dirname(__FILE__).'/'.$file)) { continue; }
          print '.';
          copy(dirname(__FILE__).'/'.$file, dirname(__FILE__).'/../'.$file);
       }
    }
    print "\nFinished copying..\n";
    copy(dirname(__FILE__)  . '/files/index.php', dirname(__FILE__).'/../index.php');
    copy(dirname(__FILE__)  . '/files/setup.inc', dirname(__FILE__).'/../setup.inc');
    copy(dirname(__FILE__)  . '/files/LICENSE.txt', dirname(__FILE__).'/../LICENSE.txt');
    copy(dirname(__FILE__)  . '/files/README', dirname(__FILE__).'/../README');
}
*/
// Make some templates to create functions and classes
$method = array();

// Will need to make some default methods and views, index 
$methods = 'function index() {}'; 

// use eval() to use these templates to create dummy methods
$templates  = array(
        'controller' => '$out = "<?php
    class {$controller}Controller extends ApplicationController {
        $methods
    }
";',

        'model' =>' $out = "<?php
/**
 * File: $model.php
 * \$Id: \$
 */
    class ".ucwords($model)." extends SQLite3_Active_Record {
    }
";
',
// This is the styles.html file.
// Edit me at phplib/views/new_view/styles.html. 
       'view' => '$out = "<h1>{$view}.html</h1>
This is the {$view}.html file.<br />
Edit me at phplib/views/{$controller}/{$view}.html.
<p>";',

);

if(count($argv) < 3) {
    print <<<END

Usage: site/generate.php controller controller_name view1 view2 ... viewn
       site/generate.php model model_name field_name:type field_name2:type ...
END;
    exit;
}
elseif($argv[1] == 'controller') {

    $view = '';

    if($argv[2]) {
        $controller = $argv[2];
    }
    else {
        die('Missing controller name');
    }
    $controller_file = $app_dir .'controllers/'."{$controller}_controller.php";

    if(count($argv)> 3) {
        $view = $argv[3];
        $views = array_slice($argv,3);
        foreach($views as $method) {
            if($method == 'index') continue;
            $methods .= "\n\n        function $method() {}";
        }
        $view_file = $app_dir . "views/{$controller}/{$view}.html";
        $view_dir = $app_dir  . "views/{$controller}/";

        if(!is_dir($view_dir)) {
            mkdir($view_dir, 0755,TRUE);
        }
    }

    if(file_exists(strtolower($controller_file))) {
        print "Controller File exists!" . basename($controller_file). ' Overwrite(Y,n)?';
        $res = fgets(STDIN);

        if('n' == trim(strtolower($res))) {
            print ("... Ignoring\n");
        }
        else {
            $fh = fopen(strtolower($controller_file), 'w')
                    or die('Could not open ' . $controller_file);

            eval( $templates['controller']);

            fwrite($fh, $out) && print "Creating {$controller_file}\n";
            fclose($fh);
        }
    }
    else {
        $fh = fopen(strtolower($controller_file), 'w')
                or die('Could not open ' . $controller_file);
        eval( $templates['controller']);
        fwrite($fh, $out) && print "created {$controller_file}\n";
        fclose($fh);
    }

    //if(isset($view_file)) 
    if(count($argc > 2)) {
        $views = array_slice($argv, 3);
        $views[] = 'index';
        $views = array_unique($views);
        foreach($views as $view) {
            if(!file_exists(strtolower($app_dir."views/$controller"))) {
                mkdir($app_dir."views/$controller");
            }
            $view_file = $app_dir ."views/{$controller}/{$view}.html";
            if(file_exists(strtolower($view_file))) {
                print "View file exists! ". basename( $view_file) . ' Overwrite(Y,n)?';
                $res = fgets(STDIN);

                if('n' == trim(strtolower($res))) {
                    print("... ignoring\n");
                }
                else {
                    $fh = fopen(strtolower($view_file), 'w')
                            or die('Could not open ' . "$view_file\n");

                    eval( $templates['view']);
                    fwrite($fh, $out) && print "created {$view_file}\n";
                    fclose($fh);
                }
            }
            else {
                $fh = fopen(strtolower($view_file), 'w')
                    or die('Could not open ' . "$view_file\n");

                eval( $templates['view']);
                fwrite($fh, $out) && print "created {$view_file}\n";
                fclose($fh);
            }
        }
    }
}
elseif(strtolower($argv[1]) == 'view') {

    if($argv[2]) {
        $controller = $argv[2];
    }
    else {
        die('Missing controller name');
    }
    if($argv[3]) {
        $view = $argv[3];
    }
    else {
        die('Missing view name');
    }
    $view_file = $app_dir .'views/'."{$controller}/{$view}.html";

    if(file_exists(strtolower($view_file))) {
        print "View file exists! ". basename( $view_file) . ' Overwrite(Y,n)?';
        $res = fgets(STDIN);

        if('n' == trim(strtolower($res))) {
            print("... ignoring\n");
        }
        else {
            $fh = fopen(strtolower($view_file), 'w')
                    or die('Could not open ' . "$view_file\n");

            eval( $templates['view']);
            fwrite($fh, $out) && print "Create {$view_file}\n";
            fclose($fh);
        }
    }
    else {
        $fh = fopen(strtolower($view_file), 'w')
                or die('Could not open ' . "$view_file\n");

        eval( $templates['view']);
        fwrite($fh, $out) && print "Create {$view_file}\n";
        fclose($fh);
    }
}
elseif($argv[1] == 'model') {

    if(isset($argv[2])) {
        $model = $argv[2];
    }
    else {
        die('Missing model name');
    }
    $model_file = $app_dir .'models/'."{$model}.php";

    if(file_exists(strtolower($model_file))) {
        print "Model file exists! ". basename( $model_file) . '. Overwrite(Y,n)?';
        $res = fgets(STDIN);
        
        if('n' == trim(strtolower($res))) {
            print("... ignoring\n");
        }
        else {
            $fh = fopen(strtolower($model_file), 'w')
                    or die('Could not open ' . "$model_file\n");

            eval( $templates['model']);
            fwrite($fh, $out) && print "Create {$model_file}\n";
            fclose($fh);
        }
    }
    else {
        $fh = fopen(strtolower($model_file), 'w')
                or die('Could not open ' . "$model_file\n");

        eval( $templates['model']);
        fwrite($fh, $out) && print "Create {$model_file}\n";
        fclose($fh);
    }

    if(count($argv > 3)) {

        $names_vals = array();

        $names_vals['id'] = 'integer not null primary key';

        foreach(array_slice($argv,3) as $v) {
            list($k,$v) = explode(':', $v);
            $names_vals[$k] = $v;
        }

        $plural = $model .'s';
        $sql = "DROP TABLE IF EXISTS ${plural};CREATE TABLE ${plural} (\n";
        $tmp = array();

        $save= array();
        foreach($names_vals as $k => $v) {
            if($k == 'index') {$save[$k] = $v;continue;}
            $tmp[] = "$k $v";
        }
        $dbh = new SQLITE3(dirname(__FILE__).'/../app/db/default.sqlite');

        $tmp[] = "created_at datetime not null";
        $tmp[] = "modified_at datetime not null";
        $sql .= "\t" .implode(",\n\t",$tmp) . "\n)";
        $dbh = new SQLITE3(dirname(__FILE__).'/../app/db/default.sqlite');

        print "Creating the ${plural} table with this SQL\n";
        print "$sql\n";
        print "-------------------------\n";
        print "To create a users controllers, run this command: \n";
        print "\tmksite/generate.php controller users pagename pagename2 pagename3 etc... \n";
        $dbh->exec($sql);
        if(!empty($save)) {
            foreach($save as $i => $n) {
                $sql = "DROP INDEX IF EXISTS ${n}_index; CREATE UNIQUE INDEX ${n}_index ON ${plural} ($n)";
                //$sql = "CREATE UNIQUE INDEX ${n}_index ON ${plural} ($n)";
                $dbh->exec($sql);
            }
        }
    }



}
