<?php
// apache static pages: 1200/sec
// php 900/sec
// drupal 20/sec  w/caching 100 
// wordpress 15/sec
// moodle 15/sec 
// ?_r=admin/all_users/&colorschome=official&
// Caching
// modules/libraries Class get them and install them and figure them out
// memcache saves commonly used pages and database queries in memory 
// simply create your own caching method
//
// files creation time, access time, change time (meta info: oownership, permissions),  and modification time
// on creation the ctime and mtime are the same
include 'setup.inc';
//$start = microtime(true);

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $file_name = md5($_SERVER['REQUEST_URI']);

    if(file_exists(CACHE . $file_name) && (filemtime(CACHE . $file_name) + 3600 > time())) {
        readfile(CACHE . $file_name);
        exit;
    }

    ob_start();
}

Request::run();

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $out = ob_get_clean();
    $fh = fopen(CACHE . $file_name,'w');
    fwrite($fh, $out);
    fclose($fh);
    print $out; 
}
