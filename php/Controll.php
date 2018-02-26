<?php
require('DataBase.php');
require('Messages.php');
if(isset($_POST["process"]) && !empty($_POST["process"])) {

	switch ($_POST["process"]) {
		case 'sendMessage':
			sendMessage();
			break;

		case 'saveHelp':
			saveHelp();
			break;
		
		default:
			echo "ERROR 404 - No Process Found";
			break;
	}
}

function sendMessage(){
	$db = new DataBase();
	$conn = $db->getConnection();

	$message = new Messages($conn);
	$txt = $_POST["message"];
	// We need the original text to use it after
	$message->setText($txt);

	$txt = $message->removeSpecialCharacters($txt);
	$words = array();
	$words = $message->explodeString($txt);
	$result = $message->findAnswer($words);

	if( $result["AskUser"] == TRUE ){
		$response = array("answer" => "How would you answer the message below ?<br>".$txt, "askHelp" => TRUE, "msgId" => $result["msgId"]);
		echo json_encode($response);
	}else{
		$response = array("answer" => $result["msgText"], "askHelp" => FALSE, "msgId" => $result["msgId"]);
		echo json_encode($response);
	}
}

function saveHelp(){
	$db = new DataBase();
	$conn = $db->getConnection();

	$message = new Messages($conn);

	$txt = safe_data($_POST["message"]);
	$msgId = safe_data($_POST["msgId"]);
	$result = $message->saveHelp($txt, $msgId);
	// $response = array("answer" => $result);

	echo json_encode($response);
}

function safe_data($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// echo "<br/><div class='messageLeft'><strong>[ALAN]</strong>". $answer["text"] ."<br/><br/>";
// 	if(strpos($answer["pic"], ".mp3") !== FALSE){
// 		echo "<audio controls><source src='img/answers/". $answer["pic"] ."' type='audio/mpeg'>Your browser does not support the audio element.</source></audio>";
// 	}else if(!empty($answer["pic"])){
// 		echo "</div><br/><img src='img/answers/". $answer["pic"] ."' width='120px' height='120px' style='display:block; border-radius: 15px;float:left;padding-right:10px;'><br/>";
// 	}

?>