<?
	include_once('../functions/blog.php');
	include_once('../functions/settings.php');
	include_once('../include/header.php');	
?>

<script type="text/javascript">

$(function() {

	$.originals = { 
			'fname' : $('#fname').val(), 
			'lname' : $('#lname').val(),
			'email' : $('#email').val(),
			'phone' : $('#phone').val(),
			'mother' : $('#mother').val(),
			'father' : $('#father').val(),
			'about' : $('#about').val(),
			'birthday' : $('#birthday').val()
	};
	
	$('header span').removeClass('active');
	$('#settings').addClass('active');
	
	$('#edit').live('click', function() {
		$('.editable').each(function() {
			var el = $(this);
			id = el.attr('id');
			el.removeAttr('disabled').addClass('editing').removeClass('editable');
		});
	});
	
	$('#save').live('click', function() {
		if ( $('.editing').length != 0) {	
			var fname = $('#fname').val();
			var lname = $('#lname').val();
			var email = $('#email').val();
			var phone = $('#phone').val();
			var mother = $('#mother').val();
			var father = $('#father').val();
			var about = $('#about').val();
			var birthday = $('#birthday').val();
			
			if (fname=='' || lname=='' || email=='') {
				notice('First name, last name, and email are required.', 1);
			} else if (fname==$.originals['fname'] && lname==$.originals['lname'] && email==$.originals['email'] && phone==$.originals['phone'] && mother==$.originals['mother'] && father==$.originals['father'] && about==$.originals['about'] && birthday==$.originals['birthday']) {
				solidify();
			} else if (!isValidEmail(email)) {
				notice('Invalid email address.', 1);
			} else {
			
			$.originals['fname'] = fname;
			$.originals['lname'] = lname;
			$.originals['email'] = email;
			$.originals['phone'] = phone;
			$.originals['mother'] = mother;
			$.originals['father'] = father;
			$.originals['about'] = about;
			$.originals['birthday'] = birthday;
			
			var dataString = 'action=updateUser&fname='+fname+
												'&lname='+lname+
												'&email='+email+
												'&phone='+phone+
												'&mother='+mother+
												'&father='+father+
												'&about='+about+
												'&birthday='+birthday;
			$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/settings.php',
						 data: dataString,
						 success: function() {
								notice('Profile updated.', 0);
								solidify();
						}
				});
				return false;	
			}
		}
	});
	
	$('#cancel').live('click', function() {
			$('.editing').each(function() {
				var el = $(this);
				text = el.val();
				id = el.attr('id');
				var original = $.originals[id];
				el.attr('disabled', true).val(original).removeClass('editing').addClass('editable');
			});	
	});
	
	$('#birthday').datepicker();
	
});

function solidify() {
		$('.editing').each(function() {
			var el = $(this);
			text = el.val();
			id = el.attr('id');
			el.attr('disabled', true).removeClass('editing').addClass('editable');
		});
	};

</script>

<div id="container">


<h2>Profile Info</h2>

<div id="notice" style="visibility:hidden">&nbsp;</div>

<? 
	 $id = $_SESSION['user_id'];
	 echo "<input type='hidden' value='$id' id='user_id'>";

   echo "<div class='memberTimestamp'>Member since ".toDateOnly(getJoinDate($id))."</div>";
	 echo "<br><div class='memberTimestamp'>Last login ".toDate(getLastLogin($id))."</div>"; ?>

<br>
<div class='settings'>
	<? 
		$email = getEmail($id);
		echo "<img src='".getGravatar($email, 80)."'/><br><br>";
			
		echo "<span class='type'>First name:</span><input type='text' disabled id='fname' class='editable' value='".getUsername($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>Last name:</span><input type='text' disabled id='lname' class='editable' value='".getLastName($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>Email:</span><input type='text' disabled id='email' class='editable' value='".$email."'>";
		echo "<br><br>";
		echo "<span class='type'>Phone:</span><input type='text' disabled id='phone' class='editable' value='".getPhone($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>About:</span><textarea disabled id='about' class='editable'>".getAbout($id)."</textarea>";
		echo "<br><br>";
		echo "<span class='type'>Birthday:</span><input type='text' disabled id='birthday' class='editable' value='".getBirthday($id)."'>";
		echo "<br><br>";
		$mom = getMother($id);
		echo "<span class='type'>Mother:</span>".getUsersAsSelect('mother', $mom);
	?>
		
		<div class='tooltip' style='position:relative;right:-500px;'>
			<span class='question'></span> Why these?
			<div class='answer' style='visibility:hidden'>Entering your parents keeps the family tree up-to-date! Click Calendar to view it.</div>
		</div>
		
	<?
		$dad = getFather($id);
		echo "<br><br><span class='type'>Father:</span>".getUsersAsSelect('father', $dad);
	
	?>
		<br><br>
		<input type='button' value='Edit' id='edit'>&nbsp;<input type='submit' id='save' value='Save'>
		<input type='button' id='cancel' value='Cancel'>

	
</div>

<? include('../include/footer.php') ?>