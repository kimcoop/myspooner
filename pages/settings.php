<?
	include_once('../functions/blog.php');
	include_once('../functions/settings.php');
	include_once('../include/header.php');	
?>

<script type="text/javascript">

$(function() {

	var editingTrip = false;
	var tripID;
	
	var editingIndicator = $('<span class="greenText"></span>').html('<strong>Editing trip. Doubleclick a date to change it. </strong>');
	var saveButton = $('<input type="button" style="margin-right:.4em" value="Save" id="saveTripButton">');
	var cancelButton = $('<input type="button" value="Cancel" id="cancelTripButton">');
	var lineBreak = $('<div></div>').html('<br>');
	
	$('.edit').click(function() {
	
		if (!editingTrip) { //ensure we're not already editing a trip
				var noticeSpace = $('#dates_notice');
				noticeSpace.stop();
				editingTrip = true;
				parent = $(this).parent().parent().parent();
				tripID = parent.attr('id');
				
				parent.prepend(lineBreak);
				parent.prepend(cancelButton);
				parent.prepend(saveButton);
				parent.prepend(editingIndicator);
				
				var el = $(this).parent().siblings('.trip_date').addClass('editingThisTrip').attr('contenteditable',true);
				
			} else {
					$('#dates_notice').stop();
					$('#dates_notice').text('Please first save or cancel your changes.').addClass('errorText').css({
						'visibility':'visible',
						'opacity':0
					}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
			}
		});
	
	$('#cancelTripButton').live('click',function() {
		$(this).siblings('span').children('.trip_date').removeClass('editingThisTrip').removeAttr('contenteditable');
		removeButtons();
	}); //end cancelTripButton	
	
	$('#saveTripButton').live('click',function() {
	
			var startDateObj = $('.editingThisTrip:first');
			var startDate = startDateObj.text().trim();
			var endDate = startDateObj.next('.editingThisTrip').text().trim();
		
			var dataString = 'action=updateTrip&tripID='+tripID
												+'&startDate='+startDate
												+'&endDate='+endDate;
	
			$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/settings.php',
						 data: dataString,
						 success: function() {
								$('#dates_notice').text('Trip saved.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
						}
				});
				return false;
	}); //end saveTripButton	
	
	$('.delete').click(function() {
		var conf = confirm('Are you sure you want to delete this trip?');
		
		if (conf) {	
			var parent = $(this).parent().parent().parent();
			var tripID = parent.attr('id');
			var dataString = 'action=deleteTrip&tripID='+tripID;
			
				$.ajax({ 
							 type: 'post',
							 dataType: 'json',
							 url: '../functions/settings.php',
							 data: dataString,
							 success: function() {
									$('#dates_notice').text('Trip deleted.').addClass('errorText').css({
											'visibility':'visible',
											'opacity':0
										}).fadeTo('slow',1, function() {
										parent.fadeOut();
									}).delay(2000).fadeTo('slow',0);
							}
					});
					return false;
			}	
				
	}); //end delete


	$('.newTrip').live('click', function() {
		$('#newTrip').slideToggle();	
	});
	
	$('#cancelNewTrip').live('click', function() {
		$('#newTrip').slideUp();
	});

	$('#arrival').datepicker();
	$('#departure').datepicker();
	
	$('#saveSpoonerDates').live('click', function() {
		var user_id = $('#user_id').val();
		var arrival = $('#arrival').val();
		var departure = $('#departure').val();
		var notes = $('#notes').val();
		
		if (arrival=='' || departure=='') {
			$('#notice').text('Arrival and departure dates must be filled in.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
		} else {
	
		var dataString = 'action=setSpoonerDates'+
											'&user_id='+user_id+
											'&arrival='+arrival+
											'&departure='+departure+
											'&notes='+notes;
			
		var userTags = $('input.userTag:checked');
											
		$.each(userTags, function() {
			dataString += '&userTags[]=' + $(this).val();
		});
	
		$.ajax({
					 type: 'post',
					 dataType: 'json',
					 url: '../functions/settings.php',
					 data: dataString,
					 success: function(data) {
							$('#dates_notice').addClass('greenText').text(data.msg).fadeIn().delay(2000).fadeOut();
							$('#newTrip').fadeOut('slow');
							$('#trips').delay(1000).replaceWith( data.trips );
					}
			});
			return false;
		}
	}); //end click

	$.originals = { 
			'fname' : $('#fname').val(), 
			'lname' : $('#lname').val(),
			'email' : $('#email').val(),
			'phone' : $('#phone').val(),
			'mother' : $('#mother').val(),
			'father' : $('#father').val(),
			'about' : $('#about').val()
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
			
			if (fname=='' || lname=='' || email=='') {
				$('#notice').text('First name, last name, and email are required.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
			} else if (fname==$.originals['fname'] && lname==$.originals['lname'] && email==$.originals['email'] && phone==$.originals['phone'] && mother==$.originals['mother'] && father==$.originals['father'] && about==$.originals['about']) {
				solidify();
			} else if (!isValidEmail(email)) {
				$('#notice').text('Invalid email address.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
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
								$('#notice').text('Profile updated.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
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
	
	
function removeButtons() {
		editingIndicator.detach();
		saveButton.detach();
		lineBreak.detach();
		cancelButton.detach();
		editingTrip = false;
	};
	
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
	echo "<div class='memberTimestamp'>Member since ".toDate(getJoinDate($id))."</div>";
	 echo "<br><div class='memberTimestamp'>Last login ".toDate(getLastLogin($id))."</div>"; ?>

<br>
<div class='settings'>
	<?
		echo "<input type='hidden' value='$id' id='user_id'>";
		echo "<span class='type'>First name:</span><input type='text' disabled id='fname' class='editable' value='".getUsername($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>Last name:</span><input type='text' disabled id='lname' class='editable' value='".getLastName($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>Email:</span><input type='text' disabled id='email' class='editable' value='".getEmail($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>Phone:</span><input type='text' disabled id='phone' class='editable' value='".getPhone($id)."'>";
		echo "<br><br>";
		echo "<span class='type'>About:</span><textarea disabled id='about' class='editable'>".getAbout($id)."</textarea>";
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
	
	
	<div class='spoonerDates'>
		<h2>Spooner Trips</h2>
		<div id="dates_notice" style="visibility:hidden;margin-top:-1.5em">&nbsp;</div>
		
		<div class='button_container'>
			<div class='newTrip'>Ready to post a Spooner trip?
			<span class='addNew'></span></div>
		</div>
		
		<div id='newTrip' style='display:none'>
			<br>
			Arriving:&nbsp;<input type='text' placeholder='When are you coming?' id='arrival' value=''><br><br>
			Leaving:&nbsp;<input type='text' placeholder='When are you leaving?' id='departure' value=''><br><br>
			Trip notes:&nbsp;<input type='text' placeholder='Any other details' id='notes' style='z-index:4000' value=''><br><br>
			
			
			
		<? echo getUserTagsAsCheckbox('Tag others on this trip:'); ?>
			
			
			<br><br><br><br><br><br><input id='saveSpoonerDates' style='z-index:400000' type='button' value='Announce!'>
			<input id='cancelNewTrip' style='z-index:400000' type='button' value='Cancel'>
		</div><!-- end div#newTrip-->
		
		<br>
			<div id='trips'>
				<? echo formatSpoonerDates($id) ?>
			</div>
	</div><!-- end div.spoonerDates -->	
	
</div>

<? include('../include/footer.php') ?>