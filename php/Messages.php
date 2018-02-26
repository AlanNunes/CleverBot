 <?php
class Messages {
	private $id;
	private $text;
	private $answer;
	private $conn;

	public function __construct($db) {
		$this->conn = $db;
	}

	public function send($txt) {

	}

	public function answer() {

	}

	public function setText($txt){
		$this->text = $this->safe_data($txt);
	}

	public function explodeString($txt){
		$words = array();
		$words = explode(" ", $txt);

		// It removes all empty elements
		$words = array_filter($words, function($value) { return $value !== ''; });

		return $words;
	}

	// This function is going to verify all matchs of words in the row
	// And say if this answer is apropriate to be returned
	// The answers is only apropriated just if the match is above or equal 60 percent
	public function analizeMatches($words, $sentence){
		$size = sizeof($words);
		$matches = 0;
		for( $i = 0; $i < $size; $i++ ){
			if( stripos($sentence, $words[$i]) ){
				$matches++;
			}
		}
		$matchPorcent = (100*$matches)/$size;
		if( $matchPorcent >= 60 ){
			// Sure of the answer
			return TRUE;
		}else{
			// Not sure of the answer
			return FALSE;
		}
	}

	public function askUserAnswer(){

	}

	public function findAnswer($words){
		$size = sizeof($words);

		for( $i = 0; $i < $size; $i++ ){
			$query = "SELECT * FROM messages WHERE msgText LIKE '%". $words[$i] ."%'";
			$result = $this->conn->query($query);

			// Word found in this row
			if( $result->num_rows > 0 ){
				$row = $result->fetch_assoc();
				$sentence = $row["msgText"];
				$msgId = $row["msgId"];

				if( $this->analizeMatches($words, $sentence) ){
					// The message match with the answer in the row
					// But we need to know if this message has a answer
					if( empty($row["msgAnswerId"]) OR $row["msgAnswerId"] == "" ){
						// This message has no answer
						// Then we need to ask a help for the user
						// And make him answer this for us and save it in our DataBase
						return array("AskUser" => TRUE, "ERROR" => FALSE, "msgId" => $msgId);
					}else{
						// The message has a answer
						// Now we need to catch this answer 
						// That's relationed to the message
						$answerId = $row["msgAnswerId"];
						$query = "SELECT msgText FROM messages WHERE msgId = $answerId";
						$result = $this->conn->query($query);
						$row = $result->fetch_assoc();
						if( $result->num_rows > 0 ){
							// We found an answer !
							return array("AskUser" => FALSE, "ERROR" => FALSE, "msgId" => "", "msgText" => $row["msgText"]);
						}else{
							// Oops! We have are not sure of the answer
							// Let's ask the user one
							return array("AskUser" => TRUE, "ERROR" => FALSE, "msgId" => $msgId);
						}
					}
				}
			}
		}

		// We found exactly nothing, so let's save the user's message
		// And ask him to answer his own question for us
		// But before we need to check if this message already exists
		$query = "SELECT msgId FROM messages WHERE msgText = '$this->text' LIMIT 1";
		$result = $this->conn->query($query);
		if( !($result->num_rows > 0) ){
			$query = "INSERT INTO messages (msgText) VALUES ('$this->text')";
			$this->conn->query($query);
		}
		// It's needed to get the id of the message that we just inserted
		$query = "SELECT msgId FROM messages WHERE msgText = '$this->text'";
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		$msgId = $row["msgId"];

		return array("AskUser" => TRUE, "ERROR" => FALSE, "msgId" => $msgId);
	}

	public function saveHelp($txt, $msgOriginId){
		$query = "INSERT INTO messages (msgText) VALUES ('$txt')";
		$this->conn->query($query);

		$query = "SELECT msgId FROM messages WHERE msgText = '$txt'";
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		$msgAnswerId = $row["msgId"];

		$query = "UPDATE messages SET msgAnswerId = '$msgAnswerId' WHERE msgId = '$msgOriginId' LIMIT 1";
		$this->conn->query($query);

		return "Thanks for your help !";
	}

	// This function fetch an unknown message. I mean a message with no answer
	// So we can find one and show to the user
	public function fetchUnknownMessages(){
		$query = "SELECT msgId, msgText FROM messages WHERE msgId >= RAND() * (SELECT MAX(msgId) FROM messages) AND msgAnswerId IS NULL LIMIT 1";
		$result = $this->conn->query($query);
		if( $result->num_rows > 0 ){
			// Uhuul I'm so thrilled, we've found an unanswered message
			// Let's text this message and wait for the user's answer and save it
			// The best is that he's not going to notice it !
			$row = $result->fetch_assoc();

			return array("AskUser" => TRUE, "ERROR" => FALSE, "msgId" => $row["msgId"], "msgText" => $row["msgText"]);
		}else{
			//return $this->saySomething();
			// Why this function ?
			// It means that we have found no empty words
			// Then we have to fetch some message from our DataBase and make the talk carry on
		}
	}

	public function removeSpecialCharacters($txt){
		$txt = str_replace(',', '', $txt);
		$txt = str_replace('.', '', $txt);
		$txt = str_replace('?', '', $txt);
		$txt = str_replace('!', '', $txt);
		$txt = str_replace(':', '', $txt);
		$txt = str_replace(';', '', $txt);
		$txt = str_replace("'", '', $txt);

		return $txt;
	}

	public function safe_data($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
}
?>