<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Clever Bot</title>
	<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css">
	<link rel="stylesheet" type="text/css" href="css/default.css">
    <script type="text/jscript" src="js/jquery-1.11.1.min.js"></script>
    <script type="text/jscript" src="js/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
    <script type="text/jscript" src="bootstrap/js/bootstrap.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div data-role="page" id="mainpage">

	<div data-role="header" class="header" style="background: #009FDD; text-shadow: none;">
		<h1>Clever Bot - Machine Learning</h1>
	</div>

	<div role="main" class="ui-content" class="content">
		<!-- <center> -->
			<div class="container" id="container">
				<div class="box" id="box">

				</div>
				<div class="inputs">
					<input type="text" name="message" class="message" id="message">
					<button class="button" id="send">Send</button>
				</div>
			</div>
		<!-- </center> -->
	</div>

	<div data-role="footer" data-position="fixed" class="footer" data-tap-toggle="false">
	  <!-- <div data-role="navbar" data-iconpos="top">
		      <ul> 
	        		   <li><a style="background: #009FDD; text-shadow: none; border: solid 1px #0087bc;" href="php/cadastrarHor.html" data-icon="plus">Horários</a></li>   
					   <li><a style="background: #009FDD; text-shadow: none; border: solid 1px #0087bc;" href="php/cadastrarFunc.php" data-icon="plus">Funcionários</a></li>        
					   <li><a style="background: #009FDD; text-shadow: none; border: solid 1px #0087bc;" href="php/cadastrarCargo.php" data-icon="plus">Cargos</a></li>       
			  </ul>		
		</div> -->
		<!-- Desenvolvido por Alan Nunes-->
  </div>
  <script src="js/jquery.mask.js"></script>
<script>
	var askHelp = false;
	var msgId;

	$(document).ready(function(){
		$("#send").click(function(){
			var message = $("#message").val();
			// alert(message);
			sendMessage(message);
		});

		function sendMessage(msg){
			if( askHelp ){
				var process = "saveHelp";
			} else {
				var process = "sendMessage";
			}

			$("#box").append("<br/><div class='messageRight'><strong>[VOCÊ]</strong>"+ msg +"</div><br/><br/>");
			$.post("php/Controll.php",
	        {
	        	message: msg,
	        	msgId: msgId,
	        	isHelp: askHelp,
	        	process: process
	        },
	        function(data,status)
	        {
	        	var data = JSON.parse(data);

	        	askHelp = data.askHelp;
	        	msgId = data.msgId;

	            $("#box").append("<br/><div class='messageLeft'><strong>[VOCÊ]</strong>"+ data.answer +"</div><br/><br/>");
	            $("#message").val("");
	        });
		}
	});
</script>
</div>

</body>
</html>