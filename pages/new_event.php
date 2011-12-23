<?

	include_once('../functions/functions.php');
	include('../include/header.php');
	include_once('../functions/blog.php');
	include_once('../functions/calendar.php');
	
?>

<script type="text/javascript">

$(function() {

	$('#eventStart').datetimepicker({
			ampm: true
		});
	$('#eventEnd').datetimepicker({
			ampm: true
		});

	$('header span').removeClass('active');
	$('#calendar').addClass('active');
			
			$('#createNewEvent').click(function() {
				var name = $('#name').val();
				var content = $('#content').val();
				var start = $('#eventStart').val().trim();
				var end = $('#eventEnd').val().trim();
				
				if (name != '' && content != '') {
				
					var tags = $('input.tagForArticle:checked');				
					
					var dataString = 'action=createEvent&name='+name
														+'&content='+content
														+'&start='+start
														+'&end='+end;
														
					$.each(tags, function() {
						dataString += '&tags[]=' + $(this).val();
					});
				
					$.ajax({ 
								 type: 'post',
								 dataType: 'json',
								 url: '../functions/calendar_functions.php',
								 data: dataString,
								 success: function(data) {
								 	if (data.error) {
										$('#notice').text(data.error).addClass('errorText').css({
											'visibility':'visible',
											'opacity':0
										}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
									} else {
											window.location.href='calendar.php';
										}
								 }
						}); //end ajax
						
					} else {
						$('#notice').text('Event name and description required.').addClass('errorText').css({
								'visibility':'visible',
								'opacity':0
							}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
					}
					return false;
				});	//end click

});

</script>

<div id="container">

<a class="back" href="calendar.php">&laquo;&nbsp;Back to calendar</a>

<h2>New Event</h2>

<div id="notice" style="visibility:hidden;">&nbsp;</div>

<div id="newPost">
	<form method="post" action="">
		<input type="text" maxlength="255" id="name" placeholder="Enter event name here"><br>
		<textarea style='height:6em' id="content" maxlength="4000" placeholder="Enter description here"></textarea><br>	
		
		<label>Start date: </label><input type="text" id="eventStart" class="smaller" placeholder="Like 03/23/2012"><br>
		<label>End date: </label><input type="text" id="eventEnd" class="smaller" placeholder="Like 03/23/2012"><br>
		
		<div id="tagsAsCheckboxes">
			<? echo getTagsAsCheckbox('Tag your event'); ?>
		</div>		
		
		<div style='margin-left:3em'>
		<span class='addNew' id='createNewTag'></span>
		<input type="text" id="newTag" placeholder="New tag" style='width: 5em'>
		</div>
		
		<br>
		<input type="button" id="createNewEvent" value="Publish Event">
	</form>
</div>


<? include('../include/footer.php'); ?>