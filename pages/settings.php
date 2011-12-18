<?

	include('../functions/settings.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$.originals = { 
			'fname' : $('#fname').text(), 
			'lname' : $('#lname').text(),
			'email' : $('#email').text()
	};

	$('header span').removeClass('active');
	$('#settings').addClass('active');
	
	$('#edit').click(function() {
		$('.editable').each(function() {
			text = $(this).text();
			id = $(this).attr('id');
			$(this).replaceWith( $('<input id='+id+' class="editing" style="float:left" type="text">').val(text) );
		});
	});
	
	$('#save').click(function() {
		if ( $('.editing').length != 0) {	
			var fname = $('#fname').val();
			var lname = $('#lname').val();
			var email = $('#email').val();
			
			if (fname==$.originals['fname'] && lname==$.originals['lname'] && email==$.originals['email']) {
				solidify();
			} else if (!isValidEmail(email)) {
				$('#notice').text('Invalid email.').addClass('errorText').fadeIn().delay(2000).fadeOut();
			} else {
			
			$.originals['fname'] = fname;
			$.originals['lname'] = lname;
			$.originals['email'] = email;
			
			var dataString = 'action=updateUser&fname='+fname+
												'&lname='+lname+
												'&email='+email;
			$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/settings.php',
						 data: dataString,
						 success: function() {
								$('#notice').text('Profile updated.').fadeIn().delay(2000).fadeOut();
								solidify();
						}
				});
				return false;	
			}
		}
	});
	
	$('#cancel').click(function() {
			$('.editing').each(function() {
				text = $(this).val();
				id = $(this).attr('id');
				var original = $.originals[id];
				$(this).replaceWith( $('<span id='+id+' class="editable">').text(original) );
			});	
	});
	
});

function solidify() {
		$('.editing').each(function() {
			text = $(this).val();
			id = $(this).attr('id');
			$(this).replaceWith( $('<span id='+id+' class="editable">').text(text) );
		});
	};

function isValidEmail(email) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(email);
};


</script>

<div id="container">


<h2>Settings</h2>

<div id="notice" style="display:none"></div>

<div class='settings'>
	<? 
	
		$id = $_SESSION['user_id'];
		echo "<div class='settingType'><span class='type'>First name:</span><span id='fname' class='editable'>".getUsername($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Last name:</span><span id='lname' class='editable'>".getLastName($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Email:</span><span id='email' class='editable'>".getEmail($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Joined on:</span><span>".toDate(getJoinDate($id))."</span></div>";
	
	?>
		<br>
		<input type='button' value='Edit' id='edit'>&nbsp;<input type='submit' id='save' value='Save'>
		<input type='button' id='cancel' value='Cancel'>
	
</div>

<? include('../include/footer.php') ?>