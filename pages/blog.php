<?

	include('../functions/blog.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {
			
			$('header span').removeClass('active');
			$('#blog').addClass('active');
			
			$('.addNewPost').click(function() {
				window.location.href = 'blogpost.php';
			});
				
		$('.postComment').live('click', function() {
			var el = $(this);
			var contentArea = el.siblings('.commentContent');
			var content = contentArea.val();
			var commentForm = el.parent().parent();
			var commentSection = commentForm.parent().parent();
			
			if (content != '') {
				var articleID = el.siblings('.artID').val();
				
				var dataString = 'action=comment&content='+content
													+'&articleID='+articleID;
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: dataString,
						 success: function(data) {
							contentArea.val('');
							commentSection.fadeTo('slow',0, function() {
									$(this).replaceWith(data.comments);
								}).delay(1000).fadeTo('slow',1, function() {
									commentForm.slideUp('slow');
								});
						 }
				});
							
				return false;
			}
		
		});
		
			
		$('.addComment').live('click', function() {
			$(this).children('.commentForm').slideDown();
		});
		
		$('#do_search').live('click', function() {
			criteria = $('#criteria').val();
			
			if (criteria != '') {
				
				var dataString = 'action=searchBlog&criteria='+criteria;
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: dataString,
						 success: function(data) {							 
								$('#container').load('filterblog.php', function() {
									$('#filterOn').text('Displaying results for search: '+criteria);
									$('#posts').html(data.results);
								});// end function							 
						 }
				});
							
				return false;
			}
			
		});
			
});
</script>

<div id="container">


<h2>Blog</h2>

<div class='button_container addNewPost'>
	Create a blog post
	<div class="addNew"></div>
</div>

<div id="search_container">
	<input id="criteria" type="search">&nbsp;<input type="button" id="do_search" value="Search">
</div>

<div id="articles">
<? echo getAllArticles(); ?>
</div>


<? include('../include/footer.php') ?>