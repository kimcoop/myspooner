<? 	


session_start();
include('include/header.php');


?>

<script type="text/javascript">

$(function() {
		
		$('#loginButton').click(function(e) {
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
		
		$('#registerButton').click(function(e) {
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
		
		
});

</script>

<div id="container">

<div id="wrapper">
	<h1>MySpooner</h1>
	
	<div id="error" style="display:none"></div>
	
	<form class="main" method="post" id="loginForm" action="">
		<table>
			<tr><th><label for=email1>Email:&nbsp;</label></th><td><input required autofocus name="email1" id="email1" type="email"/></td></tr>
			<tr><th><label for=password1>Password:&nbsp;</label></th><td><input class="clear" required name="password1" id="password1" type="password"/></td></tr>
			<tr><td colspan="2" style="text-align:center"><input type="submit" name="login" id="loginButton" value="Login"/></td></tr>
		</table>
	</form>
	<br/>
	<form class="main" method="post" id="registerForm" action="">
		<table>
			<tr><th><label for=email>Email*:&nbsp;</label></th><td><input required name="email" id="email" type="email"/></td></tr>
			<tr><th><label for=password>Password*:&nbsp;</label></th><td><input class="clear" required name="password" id="password" type="password"/></td></tr>
			<tr><th><label for=fname>First name*:&nbsp;</label></th><td><input class="clear" required name="fname" id="fname" type="text"/></td></tr>
			<tr><th><label for=lname>Last name*:&nbsp;</label></th><td><input class="clear" required name="lname" id="lname" type="text"/></td></tr>
			<tr><td colspan="2" style="text-align:center"><input type="submit" name="register" id="registerButton" value="Register"/></td></tr>
		</table>
	</form>
	
</div><!-- end wrapper -->

<? include('include/footer.php'); ?>