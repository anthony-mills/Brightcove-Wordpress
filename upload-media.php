<?php
if ('upload-media.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access Denied');
}
?>     
<Style>
    
    	/* Errors */

		#info, #success, #warning, #error, #validation {
			border: 1px solid;
			margin: 10px 0px;
			padding:5px 5px 5px 50px;
			background-repeat: no-repeat;
			background-position: 10px center;
			margin-top: 25px;
		}
		#info {
			color: #00529B;
			background-color: #BDE5F8;
			background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/brightcove-video/images/info.png'; ?>');
		}
		#success {
			color: #4F8A10;
			background-color: #DFF2BF;
			background-image:url('<?php echo get_option('siteurl').'/wp-content/plugins/brightcove-video/images/good.png';?>');
		}
		#warning {
			color: #9F6000;
			background-color: #FEEFB3;
			background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/brightcove-video/images/warning.png';?>');
		}
		#inlineError {
			color: #D8000C;
			width: 98%;
			border: dashed 2px #D8000C;
			background-color: #FFBABA;
			padding-left: 5px;
			line-height: 23px;
			display: none;
		}
		
		.heading {
			text-align: right;
			font-weight: bold;
		}
		
		td {
			border: none;
		}
</style>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#uploadForm").validate({
			errorLabelContainer: Jquery("#inlineError"),
			messages: {
				videoTitle: {
				    required: 'Please enter a title for the video<br>'
				},			
		
				videoFile: {
				    required: 'You need to provide a video file<br>'
				},      	        	        	        
		 	}
		});
	
	})
</script>

<div class="wrap">

	<?php
	if ((!empty($_POST)) && (!empty($_FILES["videoFile"]))) {
		$tokenRead;
		$tokenWrite;

		$showErrorEmpty = false;
		$showErrorNumber = false;
		$showTable = false ;

		//getting tokens from DB
		$sql = sprintf("SELECT * FROM wp_bc_video_plugin WHERE userId=1");
			$result = mysql_query($sql) or die(mysql_error());
	
	
			while ($row = mysql_fetch_object($result)) {
				$tokenRead = trim($row->tokenRead);
				$tokenWrite = trim($row->tokenWrite);	
			}

		// Instantiate the class, passing it our Brightcove API tokens (read, then write)
		$bc = new BCMAPI(
		   $tokenRead,
		   $tokenWrite
		);		
	} else {
	?>
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
						<input type="text" name="videoKeywords"> (comma seperated)
					</td>				
				</tr>	
			
			
				<tr>
					<td class="heading">
						Short Video Description:
						<br>
						(max 255 chars)
					</td>

					<td>
						<textarea name="shortDescription"></textarea>
					</td>				
				</tr>		
			
				<tr>
					<td class="heading">
						Video Description:
						<br>
						(max 5000 chars)
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
	<?php
	}
	?>
</div>
     
</body>  
</html>
  




 

