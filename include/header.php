<? 
	session_start();
	include('functions/functions.php') 

?>

<!DOCTYPE html>
<html>
<head>
<title>MySpooner</title>

<link type="text/css" rel="stylesheet" href="../styles/main.css"/>
<link type="text/css" rel="stylesheet/less" href="../styles/myspooner.less"/>
<link type="text/css" rel="stylesheet/less" href="../styles/interior.less"/>

<!--FONTS-->
<link href='http://fonts.googleapis.com/css?family=Lancelot' rel='stylesheet' type='text/css'>

<script type="text/javascript" src="../js/less.min.js"></script>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->

<script type="text/javascript">

$(function() {
		
		$('#logout').click(function(e) {
			e.preventDefault();
			$.ajax({ 
					 type: 'post',
					 data: 'action=logout',
					 dataType: 'json',
					 url: '../functions/functions.php',
					 success: function(data) {
							window.location.href = "../index.php";
					 }
			});
			return false;
		});
		
		$('.nav').click(function() {
			id = this.id;
			window.location.href = id + '.php';		
		});
		
		
	});	
		
</script>		

</head>

<? if (isset($_SESSION['username'])) { ?>

<header>

	<span id="username"><? echo $_SESSION['username'] ?></span>
	<span id="logout">Logout</span>

<span class="nav" id="calendar">Calendar</span>
<span class="nav" id="blog">Blog</span>
<span class="nav" id="settings">Settings</span>
<span class="nav" id="photos">Photos</span>
<span class="nav" id="home">Home</span>

</header>

<? } ?>

<body>