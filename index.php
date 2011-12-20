<? 	


session_start();


if (isset($_SESSION['user_id'])) header('Location: /pages/home.php');

include('include/header.php');


?>

<script type="text/javascript">

$(function() {
		
		$('#loginButton').live('click',function(e) {
			e.preventDefault();
			var email = $('#email1').val();
			var password = $('#password1').val();
			
			var dataString = 'action=login&email='+email
												+'&password='+password;		
			$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: 'functions/functions.php',
					 data: dataString,
					 success: function(data) {
						if (data.error != '') {
							$('#error').text(data.error).addClass('error').fadeIn('slow').delay(1000).fadeOut();
						} else {
							window.location.href = "pages/home.php";
						}
					 }
			});
			$('#loginForm input.clear').val('');
			return false;
		});
		
		$('#registerButton').live('click',function(e) {
			e.preventDefault();
			var email = $('#email').val();
			var password = $('#password').val();
			var fname = $('#fname').val();
			var lname= $('#lname').val();
			
			var dataString = 'action=register&email='+email
												+'&password='+password
												+'&fname='+fname
												+'&lname='+lname;		
			$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: 'functions/functions.php',
					 data: dataString,
					 success: function(data) {
						if (data.error != '') {
							$('#error').text(data.error).addClass('error').fadeIn('slow').delay(1000).fadeOut();
						} else {
							window.location.href = "pages/home.php";
						}
					}
			});
			$('#registerForm input.clear').val('');
			return false;
		});
		
		$('#linkRegister').click(function() {
			$('#access').html( $('#reg').html() );
			$('#reg').detach();
		});
		
});

</script>

<div id="container">

<div id="wrapper">
	<fieldset id="front">
	<legend><h1>MySpooner</h1></legend>
	
	<div id="error" style="display:none"></div>
	
	<div id='access'>
	<form class="main" method="post" id="loginForm" action="">
		<table>
			<tr><th><label for=email1>Email:&nbsp;</label></th><td><input required autofocus name="email1" id="email1" type="email"/></td></tr>
			<tr><th><label for=password1>Password:&nbsp;</label></th><td><input class="clear" required name="password1" id="password1" type="password"/></td></tr>
			<tr><td colspan="2" style="text-align:center"><input type="submit" name="login" id="loginButton" value="Login"/></td></tr>
		</table>
	</form>
	<a href='return:false' style='float:right;margin:0 .5em .2em 0;' id='linkRegister'>New? Click here to register.</a>
	</div>	
			
	</fieldset>
	
	<div id='reg' style="display:none">
	<p>Welcome! Sign up by filling out these fields.</p>
	<form class="main" method="post" id="registerForm">
		<table>
			<tr><th><label for=email>Email*:&nbsp;</label></th><td><input required name="email" id="email" type="email"/></td></tr>
			<tr><th><label for=password>Password*:&nbsp;</label></th><td><input class="clear" required name="password" id="password" type="password"/></td></tr>
			<tr><th><label for=fname>First name*:&nbsp;</label></th><td><input class="clear" required name="fname" id="fname" type="text"/></td></tr>
			<tr><th><label for=lname>Last name*:&nbsp;</label></th><td><input class="clear" required name="lname" id="lname" type="text"/></td></tr>
			<tr><td colspan="2" style="text-align:center"><input type="submit" name="register" id="registerButton" value="Register"/></td></tr>
		</table>
	</form>
	</div>
	
</div><!-- end wrapper -->

<? include('include/footer.php'); ?>