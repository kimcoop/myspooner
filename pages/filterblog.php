<script type="text/javascript">

$(function() {
			
			original_content = '';
			edit_section = '';
			articleID = '';
			
			$('header span').removeClass('active');
			$('#blog').addClass('active');	
			
			$('.edit').live('click',function() {
			
				var editButton = $(this);
				articleID = editButton.parent('.button_container').siblings('.article_id').val();
				edit_section = editButton.parent().siblings('.article_content');
				original_contents = edit_section.text();
				edit_section.html('<textarea class="article_content" style="width:98%">'+original_contents+'</textarea>' );
				editButton.after( $('<input type="button" class="revert" value="Cancel">')  );
				editButton.after( $('<input type="button" class="saveArticle" value="Save">')  );
				
			});
			
			$('.revert').live('click',function() {
				edit_section.html('<span class="article_content">'+original_contents+'</span>');			
				removeButtons();
			});
			
			$('.saveArticle').live('click',function() {
				articleID = $(this).parent('.button_container').siblings('.article_id').val();
				contents = $('textarea.article_content').val();
				
				el = $(this);
				dataString = 'action=editArticle&content='+contents+'&articleID='+articleID;
			
				$.ajax({ 
									 type: 'post',
									 dataType: 'json',
									 url: '../functions/blog.php',
									 data: dataString,
									 success: function(data) {
										notice('Article updated.', 0);
										el.parent().siblings('.article_content').replaceWith('<span class="article_content">'+contents+'</span>');			
										removeButtons();
									}
							});
			});// end saveArticle.click
			
			
			$('.delete').live('click',function() {
			
				articleID = $(this).parent('.button_container').siblings('.article_id').val();
				var conf = confirm('Are you sure you want to delete this blog post?');
				
				if (conf) {
					var dataString = 'action=deleteArticle&articleID='+articleID;
					
						$.ajax({ 
									 type: 'post',
									 dataType: 'json',
									 url: '../functions/blog.php',
									 data: dataString,
									 success: function(data) {
										notice('Article deleted.', 0);
									}
							});
							return false;
					}
			}); //end delete
			
			
			function removeButtons() {
			
				$('.revert').remove();
				$('.saveArticle').remove();
			
			};
});

</script>

<br>
<a class="back" href="blog.php">&laquo;&nbsp;Back to blog</a>

<h2 id="filterOn"></h2>

<div id="search_container">
	<span id="search_error" style="visibility:none;width:10em;">&nbsp;</span>
	<span id="notice" style="visibility:none;float:left;">&nbsp;</span>
	<input id="criteria" type="search">&nbsp;<input type="button" id="do_search" value="Search">
</div>

<div id="posts">Loading.</div>