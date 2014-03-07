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


$this->per_page = 20;
$this->page = 0;
if(!(empty($this->mystuff2)))
{
    $this->page= $this->mystuff2;
}


$this->max = ceil(sizeof($this->myimgfiles)/ $this->per_page);
$this->chunks = array_chunk($this->myimgfiles,$this->per_page,true);
//var_dump ($this->chunks);
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

///capitalize dir names

$this->coolsttuff =  explode(" ",$this->dir2);
foreach($this->coolsttuff as $v => $z) 
{
$this->coolsttuff[$v] = ucfirst($z);
}
$this->dir2 = implode(" ", $this->coolsttuff);


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


///***********FORM************************
function form () {
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/cactus1/pear/php');
require 'HTML/QuickForm2.php';
require 'HTML/QuickForm2/Renderer.php';
require 'HTML/QuickForm2/Rule/Regex.php';


$this->usrdir = $this->id;
$this->usrimage = $this->mystuff2;


if(isset($_SESSION['user_data'])) {
 extract($_SESSION['user_data']);
 
    $this->sender= $sender;
    $this->senderemail = $sender_email;
    $this->recipient =  $recipient;
    $this->recipientemail = $recipient_email;
    $this->mymessage = htmlentities(stripslashes($message), ENT_QUOTES,'utf-8');
    
$this->form = new HTML_QuickForm2('upload', 'POST', array('action'=>'?_r=ecards/form/'. $this->usrdir .'/'.$this->usrimage));
$this->fieldset = $this->form->addFieldset()->setLabel('Create Your E-Card');
//$this->sender = $this->fieldset-> addText('sender')->setLabel('Sender')->addElement(array('value' => $this->sender));
$this->sender = $this->fieldset-> addElement('text', 'sender',array('value' => $this->sender));
$this->sender->setLabel('Sender');
$this->sender->addRule('required','Please provide your name');
$this->senderemail = $this->fieldset-> addElement('text', 'sender_email',array('value' => $this->senderemail));
$this->senderemail ->setLabel('Sender Email');
$this->senderemail->addRule('required','Error! Please provide your email address.');
$this->senderemail->addRule('email', 'Error! Email Address is not in proper format', 'required');
$this->recipient = $this->fieldset->addElement('text', 'recipient',array('value' => $this->recipient ));
$this->recipient->setLabel('Recipient');
$this->recipient->addRule('required','Please provide recipient\'s name.');
$this->recipientemail = $this->fieldset->addElement('text', 'recipient_email',array('value' => $this->recipientemail));
$this->recipientemail->setLabel('Recipient Email');
$this->recipientemail->addRule('email', 'Error! Email Address is not in proper format', 'required');
$this->recipientemail->addRule('required','Error! Please provide recipient email address.');
$this->message = $this->fieldset->addElement('textarea', 'message', 'text', array('cols'=>60, 'rows'=>12));
$this->message->setLabel('Message'); 
$this->message->setValue($this->mymessage);
$this->message->addRule('required','Error! Please provide a message.');
$this->fieldset->addElement('submit', null, 'Submit!!');
$this->fieldset->addElement('Reset', null, 'Reset!!');
$this->perfect = false;
    }
   
   
if(!(isset($_SESSION['user_data']))) {

$this->sender = ''; 
$this->senderemail  = ''; 
$this->recipient  = ''; 
$this->recipientemail  = ''; 
$this->message = ''; 
$this->perfect = false;
$this->form = new HTML_QuickForm2('upload', 'POST', array('action'=>'?_r=ecards/form/'. $this->usrdir .'/'.$this->usrimage));
$this->fieldset = $this->form->addFieldset()->setLabel('Create Your E-Card');
//$this->sender = $this->fieldset-> addText('sender')->setLabel('Sender')->addElement(array('value' => $this->sender));
$this->sender = $this->fieldset-> addElement('text', 'sender',array('value' => $this->sender));
$this->sender->setLabel('Sender');
$this->sender->addRule('required','Please provide your name');
$this->senderemail = $this->fieldset-> addElement('text', 'sender_email',array('value' => $this->senderemail));
$this->senderemail ->setLabel('Sender Email');
$this->senderemail->addRule('required','Error! Please provide your email address.');
$this->senderemail->addRule('email', 'Error! Email Address is not in proper format', 'required');
$this->recipient = $this->fieldset->addElement('text', 'recipient',array('value' => $this->recipient ));
$this->recipient->setLabel('Recipient');
$this->recipient->addRule('required','Please provide recipient\'s name.');
$this->recipientemail = $this->fieldset->addElement('text', 'recipient_email',array('value' => $this->recipientemail));
$this->recipientemail->setLabel('Recipient Email');
$this->recipientemail->addRule('email', 'Error! Email Address is not in proper format', 'required');
$this->recipientemail->addRule('required','Error! Please provide recipient email address.');
$this->message = $this->fieldset->addElement('textarea', 'message', 'text', array('cols'=>55, 'rows'=>10));
$this->message->setLabel('Message'); 
$this->message->addRule('required','Error! Please provide a message.');
$this->fieldset->addElement('submit', null, 'Submit!!');
$this->fieldset->addElement('Reset', null, 'Reset!!');
}



$this->formerrors = array();

if(empty($_POST) && isset($_POST)) {
	  $this->formerrors[1] = 'The form is not filled out correctly';
	}

if(!empty($_POST) && isset($_POST)) {


if($this->form->validate()) {


	 $this->sender = filter_var($_POST ["sender"], FILTER_FLAG_NO_ENCODE_QUOTES) ;
	 $this->recipient = filter_var($_POST ["recipient"], FILTER_FLAG_NO_ENCODE_QUOTES) ;
 	 $this->message = htmlentities(stripslashes(filter_var($_POST ["recipient"], FILTER_FLAG_NO_ENCODE_QUOTES))) ;
	 $this->formmsg = array();
	 $this->sender_email = '';
	
	 if (filter_var($_POST["sender_email"], FILTER_VALIDATE_EMAIL)) 
	 {
    		 $this->sender_emailnotq = filter_var($_POST ["sender_email"], FILTER_SANITIZE_EMAIL) ;
    		 $this->senderdomain = array_pop(explode("@",$this->sender_emailnotq));
    		 
    		 
    		 if(checkdnsrr($this->senderdomain . '.', 'MX')) {
    		 
    			 $this->sender_email = $this->sender_emailnotq;
    		 	}
    		
     		 if(!(checkdnsrr($this->senderdomain . '.', 'MX'))) {
        		$this->formerrors[1]  = "Invalid domain in sender email address. Please check and resubmit form.";
      			}
	   }

   	if(!(filter_var($_POST ["recipient_email"], FILTER_VALIDATE_EMAIL)))
   {
  	  		$this->formmsg[1]  = "Invalid sender email address. Please check and resubmit form.";
	}
    
		
		 if (filter_var($_POST["recipient_email"], FILTER_VALIDATE_EMAIL)) 
	 {
    		 $this->recipient_emailnotq = filter_var($_POST ["recipient_email"], FILTER_SANITIZE_EMAIL) ;
    		 $this->recipientdomain = array_pop(explode("@",$this->recipient_emailnotq));
    		 
    		 
    		 if(checkdnsrr($this->recipientdomain . '.', 'MX')) {
    		 
    			 $this->recipient_email = $this->sender_emailnotq;
    
    			 $_SESSION['user_data'] = $_POST;
    			 $_SESSION['usrdir'] = $this->usrdir;
    			 $_SESSION['usrimage'] = $this->usrimage;
    			 
    			 
    			///var_dump($_SESSION);
				header('Location:?_r=ecards/preview/'. $this->usrdir. '/'. $this->usrimage);
      		    exit;
    		 		
    		 	}
    		
     		 if(!(checkdnsrr($this->recipientdomain . '.', 'MX'))) {
        		$this->formerrors[2]  = "Invalid domain in recipient email address. Please check and resubmit form.";
        		/// header('Location:?_r=ecards/form/'. $this->usrdir. '/'. $this->usrimage);
      			}
	   }

   	if(!(filter_var($_POST ["recipient_email"], FILTER_VALIDATE_EMAIL)))
   {
  	  		$this->formerrors[2]  = "Invalid recipient email address. Please check and resubmit form.";
  	  		header('Location:?_r=ecards/form/'. $this->usrdir. '/'. $this->usrimage);
	}
    
	}
	 if(sizeof($this->formerrors) >= 0)  {
 	 	
 	 	$this->myerrors = '<ul><li>';
 		foreach ($this->formerrors as $k => $v)
        $this->myerrors = 	$this->myerrors. '</li><li>'.$v . '</li>';
        $this->myerrors = 	$this->myerrors.'</ul>';
    }
	
}

if (!($this->form->validate())) {
$this->renderer = HTML_QuickForm2_Renderer::factory('default')->setOption(array('group_errors' => true));
$this->form->render($this->renderer); 

} 
}


function send() {

set_include_path(get_include_path() . PATH_SEPARATOR . '/home/cactus1/pear/php');
require 'Mail.php';


if(isset($_SESSION['user_data'])) {
 extract($_SESSION['user_data']);
 
    $this->from = 'postmaster@iheartlovesongs.com';
    $this->name = $sender;
    $this->zsender =  $sender;
    $this->email = $sender_email;
    $this->zsenderemail = $sender_email;
    $this->to =  $recipient_email;
    $this->zrecipient = $recipient;
    $this->zrecipientemail = $recipient_email;
    $this->usrmesage = $message;
    $this->usrdir2 = $_SESSION['usrdir'];
	$this->usrimage2 = $_SESSION['usrimage'];
    $this->subject =  $this->zsender . " has sent you an ecard from iheartlovesongs.com";
    $this->body = "<html><head><title>". $this->subject."</title></head><body><div style=\"clear:both\" id=\"ecardimage-container\">
	<div style=\"width:450px;float:left\"><img src=\"http://iheartlovesongs.com/images/".$this->usrdir2 ."/". $this->usrimage2.
		"\" width=\"450\" height=\"450\"></div> <div style=\"width:260px; text-align:center; padding:2em;float:left\">
	<p> Hi " .  ucwords( $this->zrecipient).",</p>
	<p>". ucwords(  $this->zsender) . " has sent you this " . ucwords(humanize($this->usrdir2))
	. " ecard, along with the message below: </p> <div id=final_message>".
	 htmlentities(stripslashes( $this->usrmesage), ENT_QUOTES,'utf-8'). "</div></div></div>
	 <p><a href=\"http://iheartlovesongs.com/?_r=ecards/gallery/". $this->usrdir2. "\">Send a Reply Ecard</a></div> 
	 </body>
	</html> ";
    
 
   
	$this->host = "mail.iheartlovesongs.com";
	$this->username = "postmaster@iheartlovesongs.com";
	$this->password = "retrogal1984";
	$this->headers = array ('From' => $this->from, 
        'To' => $this->to ,
        'Subject' => $this->subject, 
        'Content-Type' =>'text/html',
        'Reply To' =>    $this->zsenderemail
        
    );
    $this->smtp = Mail::factory('smtp',
        array ('host' => $this->host,
            'auth' => true,
            'username' => $this->username,
            'password' => $this->password,
            'port' => '25'
        )
    );
    $this->mail = $this->smtp->send($this->to, $this->headers, $this->body);
    if (PEAR::isError($this->mail)) {
        echo($this->mail->getMessage());
    }
    else {
        $this->success = "Your Ecard was successfully sent!";
    }
   
   
    }
    }
}

?>


