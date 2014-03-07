<?php 
error_reporting(E_ALL); 

class ecardsController extends ApplicationController {

function index() {}


function gallery() {


//-------set up sessions and cookies--------
if(isset($_SESSION)){
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
}

if(isset($_COOKIE['orig_index'])) {
setcookie('orig_index','',0);
}

///---------get dirs---------------

$this-> myimgdirs = array();
$this-> mycrap1 = array();
	$mysqli = new MySQLI('j9sstuff.iheartlovesongs.com','j9root','nov0884','ecardsetc');
	  if ($mysqli->connect_errno) {
   				 printf("Connect failed: %s\n", $mysqli->connect_error);
   				 exit();
					}	
	 $sql2 = "SELECT dir from ecardsetc.ecardimages WHERE DIR != '..' GROUP BY DIR"; 
		// Retrieve all the data from the "example" table
		 if (!$mysqli->query($sql2)) {
    			printf("Errormessage: %s\n", $mysqli->error);
    			exit();
    			}
	 		if ($result = $mysqli->query($sql2)) { 
					while($row = $result->fetch_array())
						{
							$this->mycrap1[] = $row;
						}

						foreach($this->mycrap1 as $row)
							{
								array_push($this->myimgdirs, $row['dir']);
							}

					/* free result set */
				$result->close();
						
			   }
$mysqli->close();
$this->defaultdir = $this->id; 
if (empty($this->defaultdir)) { $this->defaultdir = "sutro_tower";}

if (!(in_array($this->defaultdir, $this->myimgdirs))) {
					print "ERRROR!";
					print  $this->id;
					var_dump($_REQUEST['_r']);
		}

if (in_array($this->defaultdir, $this->myimgdirs)) {

$this-> myimgfiles = array();
$this-> mycrap2 = array();
		$mysqli = new MySQLI('j9sstuff.iheartlovesongs.com','j9root','nov0884','ecardsetc');
	  if ($mysqli->connect_errno) {
   				 printf("Connect failed: %s\n", $mysqli->connect_error);
   				 exit();
					}	
		 $sql2 = "SELECT name from ecardsetc.ecardimages WHERE dir= '$this->defaultdir' and name != '..' GROUP BY name"; 
		 if ($result = $mysqli->query($sql2)) { 
					while($row = $result->fetch_array())
						{
							$this->mycrap2[] = $row;
						}
						
						foreach($this->mycrap2 as $row)
							{
								array_push($this->myimgfiles, $row['name']);
							}
					/* free result set */
				$result->close();
						
			   }
$mysqli->close();
		 	}

$this->per_page = 2;
$this->page = 0;
$this->max = ceil(sizeof($this->myimgfiles)/ $this->per_page);
    $this->chunks = array_chunk($this->myimgfiles,$this->per_page,true);
     $this->files = $this->chunks[$this->page];
   $this->paging_links = array();
    for($i=0;$i< $this->max;$i++) {
        $this->paging_links[] = "<a href=\"?_r=ecards/gallery/$this->defaultdir/$i\">" . ($i + 1) . '</a>';
    }
    $this->paging_links = implode(' | ' ,$this->paging_links);
 }

/////*******SSingle *************




function single() {

$this->next_previous_links = '';
$this->original_selection_link = '';



if(!isset($this->id) || $this->id == '' ) {
    header('Location: /' .'?_r=ecards/gallery/');
    exit;
}
else {
    $this->dir = $this->id; 
    $this->dir2 = str_replace('_', ' ', $this->id);
    
    if(!isset($this->mystuff2) || $this->mystuff2 == ''  ) {
      header('Location: /' .'?_r=ecards/gallery/'. $this->dir);
        exit;
    }
    else {
        $this->idx = $this->mystuff2;
    }
}
if(!empty($_COOKIE) && isset($_COOKIE['orig_index'])) {
    $this->orig_index = (int)$_COOKIE['orig_index'];
    $this->original_selection_link = '<a href=?_r=ecards/single/' .$this->dir .'/'.$this->orig_index .'>ORIGINAL SELECTION <a>';
}
else {
    setcookie('orig_index', $this->idx);
}

//get images for singles
 $this-> myimgfiles2 = array();
 
	    $this-> mycrap2 = array();
		$mysqli = new MySQLI('j9sstuff.iheartlovesongs.com','j9root','nov0884','ecardsetc');
	  if ($mysqli->connect_errno) {
   				 printf("Connect failed: %s\n", $mysqli->connect_error);
   				 exit();
					}	

 $sql2 = "SELECT name from ecardsetc.ecardimages WHERE dir= '$this->dir' and name != '..' GROUP BY name"; 
		 if ($result = $mysqli->query($sql2)) { 
					while($row = $result->fetch_array())
						{
							$this->mycrap2[] = $row;
						}
						
						foreach($this->mycrap2 as $row)
							{
								array_push($this->myimgfiles2, $row['name']);
							}
					/* free result set */
				$result->close();
						
			   }
$mysqli->close();

$this->max_idx = sizeof($this->myimgfiles2) - 1;
$this->next_idx = 0;
$this->prev_idx = 0;

if($this->idx + 1 > $this->max_idx) {
    $this->next_idx = 0;
}
else {
    $this->next_idx = $this->idx + 1;
}

if($this->idx - 1 < 0) {
    $this->prev_idx = $this->max_idx;
}
else {
    $this->prev_idx = $this->idx -1;
}
if($this->idx < 0) {
    $this->idx = $this->max_idx;
}

$this->next_previous_linksleft = "<a href=\"?_r=ecards/single/".$this->dir. '/' . $this->prev_idx. '">&larr;</a>' ;
$this->next_previous_linksright = "<a href=\"?_r=ecards/single/".$this->dir. '/' . $this->next_idx. '">&rarr;</a>';

$this->image = $this->myimgfiles2[$this->idx];

}
function form () {
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/cactus1/pear/php');
require 'HTML/QuickForm2.php';
require 'HTML/QuickForm2/Renderer.php';
require 'HTML/QuickForm2/Rule/Regex.php';
$this->message= '';

$this->form = new HTML_QuickForm2('upload', 'POST', array('action'=>'?_r=ecards/form'));
$this->fieldset = $this->form->addFieldset()->setLabel('Create Your E-Card');

$this->sender = $this->fieldset-> addText('Sender')->setLabel('Sender');
$this->sender->addRule('required','Please provide your name.');
//$this->sender->addRule('regex', 'Sender name should contain only letters', '[:alpha:]');
$this->senderemail = $this->fieldset-> addText('Sender Email')->setLabel('Sender Email');
$this->senderemail->addRule('email', 'E-Mail is required', 'email');
//$this->senderemail->addRule('required','Please provide your email address.');

$this->recipient = $this->fieldset-> addText('Recipient')->setLabel('Recipient');
$this->recipient->addRule('required','Please provide recipient\'s name.');
//$this->recipient->addRule('regex', 'Recipient name should contain only letters', '[:alpha:]');
$this->recipientemail = $this->fieldset-> addText('Recipient Email')->setLabel('Recipient Email');
$this->recipientemail->addRule('email', 'E-Mail is required', 'email');
$this->senderemail->addRule('required','Please provide your email addresss.');

$this->message = $this->fieldset-> addTextArea('message')->setLabel('Message');
$this->message->addRule('required','Please provide a message.');
$this->fieldset->addElement('submit', null, 'Submit!!');

if(empty($_POST) && isset($_POST)) {
	  $this-> formmsg = 'The form is not filled out correctly';
	}

if(!empty($_POST) && isset($_POST)) {
if($this->form->validate()) {
	 $this->formmsg = 'Success!!! Thanks for your submission!';
	}
}

if ($this->form->validate()) {
  echo 'Thank you for your submission';
}

if (!($this->form->validate())) {
$this->renderer = HTML_QuickForm2_Renderer::factory('default')->setOption(array('group_errors' => true));
var_dump($this->renderer);
$this->form->render($this->renderer); 
}


} 


}


?>


