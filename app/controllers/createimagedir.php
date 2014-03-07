<?php
$mybaseimagedir = '/home/cactus1/iheartlovesongs.com/images/';
$mybaseimagedir2 = scandir('/home/cactus1/iheartlovesongs.com/images/');
$ext = array('.GIF', '.JPG', '.PNG');
$filelist = array(); 
$mysqli = new MySQLI('j9sstuff.iheartlovesongs.com','j9root','nov0884','ecardsetc');
    if ($mysqli->connect_errno) {
   				 printf("Connect failed: %s\n", $mysqli->connect_error);
   				 exit();
					}	
		
foreach( $mybaseimagedir2  as $k => $dh2) {
$dh = $mybaseimagedir.$dh2.'/';
$myimagedir = $dh2;
         if(is_dir($dh) && $dh != '.' && $dh != '..'){
       if ($handle = opendir($dh)) {
         while($f = readdir($handle)) {
    // ignore directories
       if(is_dir('$dh/$f')) { 
        continue; 
    }
    
    foreach($ext as $e) {
        if(strpos(strtoupper($f), $e) !== false) {
            // push only files with allowable extensions into the array
           $myfilename = $f;
           $sql = "INSERT INTO ecardsetc.ecardimages(dir, name) VALUES( '$myimagedir', '$myfilename');";
           if (!$mysqli->query($sql)) {
    			printf("Errormessage: %s\n", $mysqli->error);
    			exit();
   }
         }
      }
 
   }


   }
   }
   }
   
$mysqli->close();  
 ?>


