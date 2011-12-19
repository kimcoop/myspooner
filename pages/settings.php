<?
	include_once('../functions/blog.php');
	include_once('../functions/settings.php');
	include_once('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$.originals = { 
			'fname' : $('#fname').text(), 
			'lname' : $('#lname').text(),
			'email' : $('#email').text(),
			'phone' : $('#phone').text(),
			'mother' : $('#mother').val(),
			'father' : $('#father').val(),
			'about' : $('#about').val()
	};
	
	$('header span').removeClass('active');
	$('#settings').addClass('active');
	
	$('#edit').click(function() {
		$('.editable').each(function() {
			var el = $(this);
			id = el.attr('id');
			if (el[0].tagName == 'SPAN') {
				el.replaceWith( $('<input id='+id+' class="editing" style="float:left" type="text">').val(el.text()) );
			} else {
				el.removeAttr('disabled').addClass('editing').removeClass('editable');
			}
		});
	});
	
	$('#save').click(function() {
		if ( $('.editing').length != 0) {	
			var fname = $('#fname').val();
			var lname = $('#lname').val();
			var email = $('#email').val();
			var phone = $('#phone').val();
			var mother = $('#mother').val();
			var father = $('#father').val();
			var about = $('#about').val();
			
			if (fname=='' || lname=='' || email=='') {
				$('#notice').text('First name, last name, and email are required.').addClass('errorText').fadeIn().delay(2000).fadeOut();
			} else if (fname==$.originals['fname'] && lname==$.originals['lname'] && email==$.originals['email'] && phone==$.originals['phone'] && mother==$.originals['mother'] && father==$.originals['father'] && about==$.originals['about']) {
				solidify();
			} else if (!isValidEmail(email)) {
				$('#notice').text('Invalid email address.').addClass('errorText').fadeIn().delay(2000).fadeOut();
			} else {
			
			$.originals['fname'] = fname;
			$.originals['lname'] = lname;
			$.originals['email'] = email;
			$.originals['phone'] = phone;
			$.originals['mother'] = mother;
			$.originals['father'] = father;
			$.originals['about'] = about;
			
			var dataString = 'action=updateUser&fname='+fname+
												'&lname='+lname+
												'&email='+email+
												'&phone='+phone+
												'&mother='+mother+
												'&father='+father+
												'&about='+about;
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
				var el = $(this);
				text = el.val();
				id = el.attr('id');
				var original = $.originals[id];
				if ( el[0].tagName == 'INPUT' ) {
						el.replaceWith( $('<span id='+id+' class="editable">').text(original) );
				}	else {
						el.attr('disabled', true).val(original).removeClass('editing').addClass('editable');
				}
			});	
	});
	
});

function solidify() {
		$('.editing').each(function() {
			var el = $(this);
			text = el.val();
			id = el.attr('id');
				if ( el[0].tagName == 'INPUT' ) {
					el.replaceWith( $('<span id='+id+' class="editable">').text(text) );				
				}	else {//reset to original value
					el.attr('disabled', true).removeClass('editing').addClass('editable');
				}
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

<? echo "<div class='memberTimestamp'>Member since ".toDate(getJoinDate($id))."</div>";
	 echo "<br><div class='memberTimestamp'>Last login ".toDate(getLastLogin($id))."</div>"; ?>

<br>
<div class='settings'>
	<?
	
		$id = $_SESSION['user_id'];
		echo "<div class='settingType'><span class='type'>First name:</span><span id='fname' class='editable'>".getUsername($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Last name:</span><span id='lname' class='editable'>".getLastName($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Email:</span><span id='email' class='editable'>".getEmail($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Phone:</span><span id='phone' class='editable'>".getPhone($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>About:</span><span id='about' class='editable'>".getAbout($id)."</span></div>";
		echo "<br><br>";
		$mom = getMother($id);
		echo "<div class='settingType'><span class='type'>Mother:</span>".getUsersAsSelect('mother', $mom)."</div>";
		echo "<br><br>";
		$dad = getFather($id);
		echo "<div class='settingType'><span class='type'>Father:</span>".getUsersAsSelect('father', $dad)."</div>";
	
	?>
		<br>
		<input type='button' value='Edit' id='edit'>&nbsp;<input type='submit' id='save' value='Save'>
		<input type='button' id='cancel' value='Cancel'>
	
</div>

<? include('../include/footer.php') ?>