<?php
if ('upload-media.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access Denied');
}
?>     
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#uploadForm").validate({
			errorLabelContainer: jQuery("#inlineError"),
			messages: {
				videoTitle: {
				    required: 'Please enter a title for the video<br>'
				},			
		
				videoFile: {
				    required: 'You need to provide a video file<br>'
				},      	        	        	        
		 	}
		});
		
        jQuery('#videoKeywords').tagsInput({
		   'height':'40px',
		   'width':'300px',
		   'defaultText':'Add a tag',        	
        });	
	
	})
</script>

<div class="wrap">
		<h2>Brightcove Upload Media</h2>
		<br>
		<div id="inlineError"></div>

		<form id="uploadForm" enctype="multipart/form-data" method="POST">
			<table>
				<tr>
					<td class="heading">
						<em>*</em> Title:
					</td>

					<td>
						<input type="text" name="videoTitle" class="required">
					</td>				
				</tr>
			
				<tr>
					<td class="heading">
						Keywords:
					</td>

					<td>
						<input type="text" name="videoKeywords" id="videoKeywords"> (comma seperated)
					</td>				
				</tr>	
			
			
				<tr>
					<td class="heading">
						Short Video Description:
						<br>
						(max 250 chars)
					</td>

					<td>
						<textarea name="shortDescription"></textarea>
					</td>				
				</tr>		
			
				<tr>
					<td class="heading">
						Video Description:
						<br>
						(max 4000 chars)
					</td>

					<td>
						<textarea name="longDescription"></textarea>
					</td>				
				</tr>
						
				<tr>
					<td class="heading">
						<em>*</em> Video File:
					</td>

					<td>
						<input type="file" class="required" id="videoFile" name="videoFile" class="required">
					</td>				
				</tr>	
						
				<tr>
					<td class="heading">
						Video Thumbnail:
					</td>

					<td>
						<input type="file" id="videoThumbnail" name="videoThumbnail"> (PNG, JPG, GIF)
					</td>				
				</tr>	
																														
				<tr>
					<td></td>

					<td>
						<input type="submit" value="Upload">
					</td>				
				</tr>	
			</table>
		
		</form>	
</div>
  




 

