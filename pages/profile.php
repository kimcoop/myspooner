<?

	include('../functions/functions.php');
	include('../include/header.php');	
	
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$get = $_POST['action'];
		$get = htmlspecialchars(trim($action));
		switch($get) {
			case 'test':
				echo json_encode(array('msg'=>'testing'));
				echo '<h1>testing</h1>';
				break;
		}
	}
	
	echo $_POST['userID'];
	
?>

<script type="text/javascript">

$(function() {
			
			$('header span').removeClass('active');

			$('.write').live('click',function() {
				$('#new_wall_post').slideToggle();
			});


			$('.write_post').live('click', function(e) {
				e.preventDefault();
				var el = $(this);
				var contentArea = el.siblings('.commentContent');
				var content = contentArea.val();
				var toUser = $('#userID').val();
				if (content != '') {
					
					var dataString = 'action=writeOnWall&toUser='+toUser
														+'&post='+content;alert(dataString);
					$.ajax({ 
							 type: 'post',
							 dataType: 'json',
							 url: '../functions/blog.php',
							 data: dataString,
							 success: function(data) {
									//contentArea.val('');
									//commentSection.replaceWith(data.comments);
									alert(data.msg);
							 }
					});
				} //end if			
				return false;
			}); // end click
});

</script>

<div id="container">

<h2 id="profileOf">Viewing the profile of</h2>
<input id="userID" type="hidden">


<? include('../include/footer.php') ?>
