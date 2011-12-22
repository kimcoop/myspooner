<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#calendar').addClass('active');
	
	$('#big_calendar').fullCalendar({
		events: {
        url: '../functions/calendar_functions.php',
        type: 'POST',
        data: {
            action: 'fetchCalendar'
        },       
        error: function() {
            alert('Error fetching events.');
        },
        color: '#74E8D5',   // a non-ajax option
        textColor: '#333' // a non-ajax option
    } //end events	
	}); //end fullCalendar
	
	$('#createAnEvent').click(function() {	
		window.location.href='new_event.php';
	});

});

</script>

<div id="container">
<a href="javascript:window.print()">Click to print</a>

<span class='button_container' id='createAnEvent'>Create an event<span class='addNew'></span></span>

<div id="big_calendar"></div>


<? include('../include/footer.php') ?>