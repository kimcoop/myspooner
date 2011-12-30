<?

	include('../functions/functions.php');
	include('../include/header.php');
	include('../functions/blog.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#blog').addClass('active');
	
	$('#postBlog').live('click', function(e) {
				e.preventDefault();
				var title = $('#title').val();
				var content =tinyMCE.get('content').html();alert(content);
				var tags = $('input.tagForArticle:checked');
				var userTags = $('input.userTag:checked');				
				
				var dataString = 'action=createArticle&title='+title
													+'&content='+content;
													
				$.each(tags, function() {
					dataString += '&tags[]=' + $(this).val();
				});
				
				$.each(userTags, function() {
					dataString += '&userTags[]=' + $(this).val();
				});
				
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: dataString,
						 success: function(data) {
							if (data.error) {
								notice(data.error, 1);
							} else {
								window.location.href = 'blog.php';
							}
						 }
				});// end ajax
				
				return false;
			});
			
		tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        plugins : "emotions,spellchecker,advhr,insertdatetime,preview", 
                
        // Theme options - button# indicated the row# only
        theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,formatselect",
        theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "insertdate,inserttime,|,spellchecker,advhr,,removeformat,|,sub,sup,|,charmap,emotions",      
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true
		});

});

</script>

<div id="container">
<br>
<a class="back" href="blog.php">&laquo;&nbsp;Back to blog</a>

<h2>New Blog Post</h2>

<div id="notice" style="visibility:hidden;">&nbsp;</div>

<div id="newPost">
	<form method="post" action="" style="width:500px;margin:0px auto">
		&nbsp;Title&nbsp;<input type="text" maxlength="255" id="title" placeholder="Enter title here"><br>
		<form method="post" action="show.php">
			<p>     
				<textarea id="content" name="content" cols="50" rows="15">This is some content that will be editable with TinyMCE.</textarea>
			</p>
		</form>
		
		<br>Tag your post to make it more searchable:
		<div id="tagsAsCheckboxes">
			<span id='tagError' style='visibility:hidden'>&nbsp;</span><br>
			<? echo getTagsAsCheckbox(); ?>
		</div>
		
		
		<div>
			<span class='addNew' id='createNewTag'></span>
			<input type="text" id="newTag" placeholder="New tag" style='width: 5em'>
		</div>
		
		<br>Tag people in this post:
		<? echo getUserTagsAsCheckbox(); ?>
		<br>
		<input type="submit" id="postBlog" value="Publish Post">
	</form>
</div>


<? include('../include/footer.php'); ?>