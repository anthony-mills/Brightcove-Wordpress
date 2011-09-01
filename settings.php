<?php
if ('settings.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access Denied');
}
?>
<script type="text/javascript">
	jQuery(function() {
			jQuery("#pluginSettings").validate({
				errorLabelContainer: jQuery("#errorMsg"),
				messages: {
					brightcove_publisher_id: {
					    required: 'Please enter your Brightcover publisher id<br>'
					},			
			
					brightcove_player_id: {
					    required: 'Please enter the id of the player you would like to use for playback<br>'
					},      	        
	
					brightcove_player_width: {
					    required: 'Please enter the width of your player<br>'
					},
	
					brightcove_player_height: {
					    required: 'Please enter the height of your player<br>'
					}, 	
					
					brightcove_read_token: {
					    required: 'Please enter a Brightcove API read token<br>'
					}, 									 	        	   
					
					brightcove_write_token: {
					    required: 'Please enter a Brightcove write token<br>'
					}, 					     
			 	}
			});
	
			jQuery(':input[placeholder]').placeholder();
		
	});	
</script>

<div class="wrap">

<h2>Plugin Settings</h2>
	<form method="post" id="pluginSettings" name="pluginSettings">

	<div id="errorMsg"></div>
	
	<?php
		if (!empty($errorMsg)) {
			echo '<div id="errorMsg">' . $errorMsg . '</div>';
		}
		
		if (!empty($successMsg)) {
			echo '<div id="successMsg">' . $successMsg . '</div>';			
		}		 
	?>
	<h4>Video Settings</h4>
		<p>
			<label>Publisher ID <em>*</em></label><br/>
			<input type="text" id="brightcove_publisher_id" name="brightcove_publisher_id" size="15" maxlength="15" value="<?= $pluginSettings['brightcove_publisher_id'];  ?>" class="required" /> 
		</p>
		
		<p>
			<label>Player ID <em>*</em></label><br/>
			<input type="text" id="brightcove_player_id" name="brightcove_player_id" size="15" maxlength="15" value="<?= $pluginSettings['brightcove_player_id']; ?>" class="required"/> 
		</p>
		
		<p>
			<label>Video Width <em>*</em></label><br/>
			<input type="text" id="brightcove_player_width" name="brightcove_player_width" size="4" maxlength="4" value="<?= $pluginSettings['brightcove_player_width']; ?>" class="required"/> 
		</p>
		
		<p>
			<label>Video height <em>*</em></label><br/>
			<input type="text" id="brightcove_player_height" name="brightcove_player_height" size="4" maxlength="4" value="<?= $pluginSettings['brightcove_player_height']; ?>" class="required"/> 
		</p>
		
	<h4>Brightcove Access Tokens</h4>
		<p>
			<label>Read Token <em>*</em></label><br/>
			<input type="text" id="brightcove_read_token" name="brightcove_read_token" size="75" maxlength="75" value="<?= $pluginSettings['brightcove_read_token'];  ?>" class="required"/> 
		</p>
		
		<p>
			<label>Write Token <em>*</em></label><br/>
			<input type="text" id="brightcove_write_token" name="brightcove_write_token" size="75" maxlength="75" value="<?= $pluginSettings['brightcove_write_token'];  ?>" class="required"/> 
		</p>

	<h4>Display Of Existing Videos</h4>
		<p>
			<label>Videos Per Page</label><br/>
			<?php
				/*
				 * Set 50 videos as the default
				 */
				if (empty($pluginSettings['brightcove_videos_per_page'])) {
					$pluginSettings['brightcove_videos_per_page'] = 50;	
				}
			?>
			<input type="text" id="brightcove_videos_per_page" name="brightcove_videos_per_page" size="4" maxlength="4" value="<?= $pluginSettings['brightcove_videos_per_page'];  ?>" /> 
		</p>
					
		<p>
			<input type="submit" class="button" value="Save" name="save" />
		</p>
	</form>

</div>
  




 

