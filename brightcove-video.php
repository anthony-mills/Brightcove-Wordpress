<?php 
/* 
Plugin Name: Brightcove Video Plugin
Plugin URI: http://www.imageination.tv
Description: Upload and embed videos to and from your bright cove account
Version: 0.1 
Author: Anthony Mills
Author URI: http://www.development-cycle.com
*/

if ('wp-brightcove-video-plugin.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Access denied');
}

// Add the admin menus
add_action('admin_menu', 'bc_video_menu');

// Include the required JS & CSS
add_action('admin_print_styles', 'brightcove_video_load_css');
add_action('admin_print_scripts', 'brightcove_video_load_js');

function bc_video_menu() {
   add_menu_page('Brightcove Video', 'Brightcove Video', 'manage_options', 'brightcove-video',  'wp_bc_videos');
    
   add_submenu_page('brightcove-video', __('Plugin Settings','brightcove-settings'), __('Plugin Settings','brightcove-settings'), 'manage_options', 'brightcove-settings', 'plugin_settings');              
   add_submenu_page('brightcove-video', __('Brightcove Upload','upload-media'), __('Upload Media','upload-media'), 'manage_options', 'upload_media', 'wp_upload_media');                   
}

/*
 * Render the plugin settings page
 */
function plugin_settings(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('settings.php');
}

function wp_upload_media(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('upload_media.php');
}

function wp_bc_videos(){
	if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page' );
    }
	require_once('videos.php');
}

function Brightcove_Parse($content)
{
    $content = preg_replace_callback("/\[brightcove ([^]]*)\/\]/i", "Brightcove_Render", $content);
    return $content;
}

function Brightcove_Render($matches)
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

    foreach ( (array) $attributes[1] as $key => $value )
  {
        // Strip out legacy quotes
        $arguments[$value] = str_replace('"', '', $attributes[2][$key]);
    }


    if (( !array_key_exists('video', $arguments) ) && ( !array_key_exists('player', $arguments) ))
  {
        return '<div style="background-color:#f99; padding:10px;">Brightcove Player Widget Error: Required parameter "video" or "player" is missing!</div>';
        exit;
    }
    else
    {
    $video = $arguments['video'];
  }

    if( array_key_exists('width', $arguments) )
  {
        $height = $arguments['width'];
    }

    if( array_key_exists('height', $arguments) )
  {
        $height = $arguments['height'];
    }

    if( array_key_exists('player', $arguments) )
  {
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

/*
 * Load javascript required by the backend administration pages
 */
function brightcove_video_load_js()
{	
	wp_enqueue_script('validateJS', WP_PLUGIN_URL . '/brightcove_video/js/jquery.validate.js', array('jquery'), '1.0');
	wp_enqueue_script('placeholdersJS', WP_PLUGIN_URL . '/brightcove_video/js/jquery.placeholders.js', array('jquery'), '1.0');
	wp_enqueue_script('colorBox', WP_PLUGIN_URL . '/brightcove_video/js/jquery.colorbox.js', array('jquery'), '1.0');
	wp_enqueue_script('tableSorter', WP_PLUGIN_URL . '/brightcove_video/js/jquery.tablesorter.js', array('jquery'), '1.0');	
	wp_enqueue_script('tablePaginator', WP_PLUGIN_URL . '/brightcove_video/js/jquery.paginator.js', array('jquery'), '1.0');		
}

/*
 * Load the custom style sheets for the admin pages
 */
function brightcove_video_load_css()
{
	wp_register_style('brightcove_video_default', WP_PLUGIN_URL . '/brightcove_video/css/style.css');
	wp_enqueue_style('brightcove_video_default');
	
	wp_register_style('brightcove_video_colorbox', WP_PLUGIN_URL . '/brightcove_video/css/colorbox.css');
	wp_enqueue_style('brightcove_video_colorbox');		
}

/*
 * Check the plugin has been configured
 */
function brightcove_video_check_plugin_settings()
{
	$pluginSettings['brightcove_publisher_id'] = get_option('brightcove_publisher_id');
	$pluginSettings['brightcove_player_id'] = get_option('brightcove_player_id');
	$pluginSettings['brightcove_player_width'] = get_option('brightcove_player_width');
	$pluginSettings['brightcove_player_height'] = get_option('brightcove_player_height');
	$pluginSettings['brightcove_read_token'] = get_option('brightcove_read_token');
	$pluginSettings['brightcove_write_token'] = get_option('brightcove_write_token');
		
	if ((empty($pluginSettings['brightcove_publisher_id'])) || (empty($pluginSettings['brightcove_player_id'])) || (empty($pluginSettings['brightcove_read_token'])) || (empty($pluginSettings['brightcove_write_token']))) {
		require_once('configuration_required.php');
		exit;	
	} else {
		return $pluginSettings;
	}
}
?>
