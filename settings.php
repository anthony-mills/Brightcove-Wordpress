<?php
if ('settings.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access Denied');
}
?>
<div class="wrap">

<h2>Plugin Settings</h2>
	<form method="post" id="pluginSettings" name="pluginSettings">

	<h4>Video Settings</h4>
		<p>
			<label>Publisher ID</label><br/>
			 <input type="text" id="publisher_id" name="publisher_id" size="75" maxlength="75" value="<? $pluginSettings['publisher_id'];  ?>" /> 
		</p>
		
		<p>
			<label>Player ID</label><br/>
			 <input type="text" id="player_id" name="player_id" size="16" maxlength="25" value="<?= $pluginSettings['player_id']; ?>" /> 
		</p>
		
		<p>
			<label>Video Width</label><br/>
			 <input type="text" id="player_width" name="player_width" size="4" maxlength="4" value="<?= $pluginSettings['player_width']; ?>" /> 
		</p>
		
		<p>
			<label>Video height</label><br/>
			 <input type="text" id="player_height" name="player_height" size="4" maxlength="4" value="<?= $pluginSettings['player_height']; ?>" /> 
		</p>
		
	<h4>Brightcove Access Tokens</h4>
		<p>
			<label>Read Token</label><br/>
			 <input type="text" id="read_token" name="read_token" size="75" maxlength="75" value="<? $pluginSettings['read_token'];  ?>" /> 
		</p>
		
		<p>
			<label>Write Token</label><br/>
			 <input type="text" id="write_token" name="write_token" size="75" maxlength="75" value="<? $pluginSettings['write_token'];  ?>" /> 
		</p>
			
		<p>
			<input type="submit" class="button" value="Save" name="save" />
		</p>
	</form>

</div>
  




 

