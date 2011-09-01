<?php
if ('settings.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access Denied');
}
?>
<script type="text/javascript">
	jQuery(function() {
			jQuery("#pluginSettings").validate({
				errorLabelContainer: jQuery("#validationError"),
				messages: {
					publisher_id: {
					    required: 'Please enter your Brightcover publisher id<br>'
					},			
			
					player_id: {
					    required: 'Please enter the id of the player you would like to use for playback<br>'
					},      	        
	
					player_width: {
					    required: 'Please enter the width of your player<br>'
					},
	
					player_height: {
					    required: 'Please enter the height of your player<br>'
					}, 	
					
					read_token: {
					    required: 'Please enter a Brightcove API read token<br>'
					}, 									 	        	   
					
					write_token: {
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

	<div id="validationError"></div>
	
	<h4>Video Settings</h4>
		<p>
			<label>Publisher ID <em>*</em></label><br/>
			<input type="text" id="publisher_id" name="publisher_id" size="15" maxlength="15" value="<? $pluginSettings['publisher_id'];  ?>" class="required" /> 
		</p>
		
		<p>
			<label>Player ID <em>*</em></label><br/>
			<input type="text" id="player_id" name="player_id" size="15" maxlength="15" value="<?= $pluginSettings['player_id']; ?>" class="required"/> 
		</p>
		
		<p>
			<label>Video Width <em>*</em></label><br/>
			<input type="text" id="player_width" name="player_width" size="4" maxlength="4" value="<?= $pluginSettings['player_width']; ?>" class="required"/> 
		</p>
		
		<p>
			<label>Video height <em>*</em></label><br/>
			<input type="text" id="player_height" name="player_height" size="4" maxlength="4" value="<?= $pluginSettings['player_height']; ?>" class="required"/> 
		</p>
		
	<h4>Brightcove Access Tokens</h4>
		<p>
			<label>Read Token <em>*</em></label><br/>
			<input type="text" id="read_token" name="read_token" size="75" maxlength="75" value="<? $pluginSettings['read_token'];  ?>" class="required"/> 
		</p>
		
		<p>
			<label>Write Token <em>*</em></label><br/>
			<input type="text" id="write_token" name="write_token" size="75" maxlength="75" value="<? $pluginSettings['write_token'];  ?>" class="required"/> 
		</p>

	<h4>Display Of Existing Videos</h4>
		<p>
			<label>Videos Per Page</label><br/>
			<input type="text" id="videos_per_page" name="videos_per_page" size="4" maxlength="4" value="<? $pluginSettings['videos_per_page'];  ?>" /> 
		</p>
					
		<p>
			<input type="submit" class="button" value="Save" name="save" />
		</p>
	</form>

</div>
  




 

