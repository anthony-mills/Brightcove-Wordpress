<?php 
/* 
Plugin Name: Brightcove Video Plugin
Description: Upload and embed videos to and from your bright cove account
Version: 0.1 
Author: Anthony Mills
Author URI: http://www.development-cycle.com
*/

if ('brightcove-video.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access denied');
}

// Add the admin menus
add_action('admin_menu', 'brightcoveVideoMenu');

// Include the required JS & CSS
add_action('admin_print_styles', 'brightcoveVideoLoadCss');
add_action('admin_print_scripts', 'brightcoveVideoLoadJs');

function brightcoveVideoMenu() {
	add_menu_page('Brightcove Video', 'Brightcove Video', 'manage_options', 'brightcove-video',  'brightcoveVideoExistingMedia');    
	add_submenu_page('brightcove-video', __('Plugin Settings','brightcove-settings'), __('Plugin Settings','brightcove-settings'), 'manage_options', 'brightcove-settings', 'brightcoveVideoPluginSettings');              
	add_submenu_page('brightcove-video', __('Brightcove Upload','upload-media'), __('Upload Media','upload-media'), 'manage_options', 'upload_media', 'brightcoveVideoUploadMedia');                   
}

/**
 * 
 * Render the plugin settings page
 * 
 **/
function brightcoveVideoPluginSettings(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	if (!empty($_POST)) {
		if ((!empty($_POST['brightcove_publisher_id'])) && (!empty($_POST['brightcove_player_id'])) && (!empty($_POST['brightcove_player_width'])) && 
			(!empty($_POST['brightcove_player_height'])) && (!empty($_POST['brightcove_read_token'])) && (!empty($_POST['brightcove_write_token']))) {
				
			register_setting( 'brightcove-video', 'brightcove_publisher_id' );
			register_setting( 'brightcove-video', 'brightcove_player_id' );
			register_setting( 'brightcove-video', 'brightcove_player_width' );
			register_setting( 'brightcove-video', 'brightcove_player_height' );
			register_setting( 'brightcove-video', 'brightcove_read_token' );
			register_setting( 'brightcove-video', 'brightcove_write_token' );			

			update_option( 'brightcove_publisher_id', $_POST['brightcove_publisher_id'] );
			update_option( 'brightcove_player_id', $_POST['brightcove_player_id'] );
			update_option( 'brightcove_player_width', $_POST['brightcove_player_width'] );
			update_option( 'brightcove_player_height', $_POST['brightcove_player_height'] );			
			update_option( 'brightcove_write_token', $_POST['brightcove_write_token'] );
			update_option( 'brightcove_read_token', $_POST['brightcove_read_token'] );	
			$successMsg = 'Settings successfully saved';						 
		} else {
			$errorMsg = 'You need to provide all required fields';	
		}
		
		if (!empty($_POST['brightcove_videos_per_page'])) {
			register_setting( 'brightcove-video', 'brightcove_videos_per_page' );	
			update_option( 'brightcove_videos_per_page', $_POST['brightcove_videos_per_page']);			
		}	
	}
	$pluginSettings = brightcoveVideoCheckPluginSettings(0);
	require_once('settings.php');
}

function brightcoveVideoUploadMedia(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('views/upload-media.php');
}

/**
 * 
 * Display existing videos stored on Brightcove
 * 
 **/
function brightcoveVideoExistingMedia(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	
	// Get the existing videos from 
	$pluginSettings = brightcoveVideoCheckPluginSettings();
	$brightCove = brightcoveVideoGetApi($pluginSettings);
	  
	$params = array('video_fields' => 'id,name,shortDescription,creationDate', 'sort_by' => 'CREATION_DATE');

	require_once('views/existing-videos.php');
}
 
function brightcoveVideoParse($content)
{
    $content = preg_replace_callback("/\[brightcove ([^]]*)\/\]/i", "brightcoveVideoRender", $content);
    return $content;
}

function brightcoveVideoRender($matches)
{
	global $video, $player, $publisher, $width, $height, $arguments;

	$sql = sprintf("SELECT * FROM wp_bc_video_plugin WHERE userId=1");
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$tokenRead = $row->tokenRead;
		$tokenWrite = $row->tokenWrite;	
		$publisherId = $row->publisherId; 
		$playerId = $row->playerId; 
		$width = $row->width; 
		$height = $row->height;
	}



	//set video info for brightcove player
	//Set the publisher ID - YOU MUST SET THIS TO YOUR OWN PUBLISHER ID
	$publisher = $publisherId;
	
	//Set a default player to use - YOU MUST SET THIS TO YOUR OWN DEFAULT PLAYER
	$player = $playerId;
	
	//Set width and height for the default video player
	$width = $width;
	$height = $height;
	
	//Define default video variable
	$videoid = 0;
	
	//The actual parse content function called by the filter
	//This will use the callback function BCVideo_Render to do the
	//actual text replacement for the widget
   
    $output = '';
    $matches[1] = str_replace(array('&#8221;','&#8243;'), '', $matches[1]);
    preg_match_all('/(\w*)=(.*?) /i', $matches[1], $attributes);
    $arguments = array();

    foreach ( (array) $attributes[1] as $key => $value ) {
        // Strip out legacy quotes
        $arguments[$value] = str_replace('"', '', $attributes[2][$key]);
    }

    if (( !array_key_exists('video', $arguments) ) && ( !array_key_exists('player', $arguments) )) {
        return '<div style="background-color:#f99; padding:10px;">Brightcove Player Widget Error: Required parameter "video" or "player" is missing!</div>';
        exit;
    } else {
    	$video = $arguments['video'];
	}

	if( array_key_exists('width', $arguments) ) {
		$height = $arguments['width'];
	}

	if( array_key_exists('height', $arguments) ) {
		$height = $arguments['height'];
	}

	if( array_key_exists('player', $arguments) ) {
		$player = $arguments['player'];
	}
         
  $output .= '<script language="JavaScript" type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>
		<object id="myExperience$BCpost" class="BrightcoveExperience">
		  <param name="bgcolor" value="#FFFFFF" />
		  <param name="width" value="480" />
		  <param name="height" value="270" />
		  <param name="playerID" value="'.$player.'" />
		  <param name="publisherID" value="'.$publisher.'"/>
		  <param name="isVid" value="true" />
		  <param name="isUI" value="true" /> 
		  <param name="@videoPlayer" value="'.$video.'" />
		</object>';    
             
    return $output;
}

/**
 * 
 * Load javascript required by the backend administration pages
 * 
 **/
function brightcoveVideoLoadJs()
{	
	wp_enqueue_script('validateJS', WP_PLUGIN_URL . '/brightcove_video/js/jquery.validate.js', array('jquery'), '1.0');
	wp_enqueue_script('placeholdersJS', WP_PLUGIN_URL . '/brightcove_video/js/jquery.placeholders.js', array('jquery'), '1.0');
	wp_enqueue_script('colorBox', WP_PLUGIN_URL . '/brightcove_video/js/jquery.colorbox.js', array('jquery'), '1.0');
	wp_enqueue_script('tableSorter', WP_PLUGIN_URL . '/brightcove_video/js/jquery.tablesorter.js', array('jquery'), '1.0');	
	wp_enqueue_script('tablePaginator', WP_PLUGIN_URL . '/brightcove_video/js/jquery.paginator.js', array('jquery'), '1.0');		
}

/**
 * 
 * Load the custom style sheets for the admin pages
 * 
 **/
function brightcoveVideoLoadCss()
{
	wp_register_style('brightcove_video_default', WP_PLUGIN_URL . '/brightcove_video/css/style.css');
	wp_enqueue_style('brightcove_video_default');
	
	wp_register_style('brightcove_video_colorbox', WP_PLUGIN_URL . '/brightcove_video/css/colorbox.css');
	wp_enqueue_style('brightcove_video_colorbox');	
	
	wp_register_style('brightcove_tabbed_content', WP_PLUGIN_URL . '/brightcove_video/css/tabbedcontent.css');
	wp_enqueue_style('brightcove_tabbed_content');	

	wp_register_style('brightcove_video_info', WP_PLUGIN_URL . '/brightcove_video/css/videoinfo.css');
	wp_enqueue_style('brightcove_video_info');				
}

/**
 * 
 * Check the plugin has been configured
 * 
 **/
function brightcoveVideoCheckPluginSettings($redirect = 1)
{
	$pluginSettings['brightcove_publisher_id'] = get_option('brightcove_publisher_id');
	$pluginSettings['brightcove_player_id'] = get_option('brightcove_player_id');
	$pluginSettings['brightcove_player_width'] = get_option('brightcove_player_width');
	$pluginSettings['brightcove_player_height'] = get_option('brightcove_player_height');
	$pluginSettings['brightcove_read_token'] = get_option('brightcove_read_token');
	$pluginSettings['brightcove_write_token'] = get_option('brightcove_write_token');

	if ($redirect == 1) {
		if ((empty($pluginSettings['brightcove_write_token'])) || (empty($pluginSettings['brightcove_read_token'])) || 
			(empty($pluginSettings['brightcove_publisher_id'])) || (empty($pluginSettings['brightcove_player_id'])) || 
			(empty($pluginSettings['brightcove_player_width'])) || (empty($pluginSettings['brightcove_player_height']))) {
				require_once('views/settings-required.php');
				exit;
				
		} else {
			return $pluginSettings;
		}
	} else {
		if (!empty($pluginSettings)) {
			return $pluginSettings;
		} 
	}
}

/**
 * 
 * Instansiate Brightcove API
 * 
 **/
function brightcoveVideoGetApi($pluginSettings)
{
	require_once('includes/bc-mapi.php');
	
	$brightCove = new BCMAPI(
	   $pluginSettings['brightcove_read_token'],
	   $pluginSettings['brightcove_write_token']
	);	
	
	return $brightCove;
}

/**
 * 
 * Format brightcove times into something readable
 * 
 **/
function brightcoveVideoConvertMilliseconds($ms){
	$milliseconds = $ms; // number of milliseconds
	$minutes = floor($milliseconds / (1000 * 60));
	$seconds = ceil($milliseconds % (1000 * 60) / 1000);
	echo $minutes . ':' . (($seconds < 10) ? '0' : '') . $seconds;
}