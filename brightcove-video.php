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

define(BRIGHTCOVE_PLUGIN_DIR, 'Brightcove-Wordpress');

// Add the admin menus
add_action('admin_menu', 'brightcoveVideoMenu');

// Include the required JS & CSS
add_action('admin_print_styles', 'brightcoveVideoLoadCss');
add_action('admin_print_scripts', 'brightcoveVideoLoadJs');

// Add the embed shotcode
add_shortcode('brightcoveVideo', 'brightcoveVideoEmbed');

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

	if ((!empty($_POST)) && (!empty($_FILES["videoFile"]))) {
		if (($_FILES['videoFile']['error'] == 0) && ($_FILES['videoFile']['size'] > 0) && ($_FILES['videoFile']['size'] > 0) 
			&& (move_uploaded_file($_FILES['videoFile']['tmp_name'], dirname(__FILE__) . '/uploads/' . $_FILES['videoFile']['name' ]))) {
				$pluginSettings = brightcoveVideoCheckPluginSettings();
				$brightcove = brightcoveVideoGetApi($pluginSettings);
	
				if (!empty($_POST['shortDescription'])) {
					if (strlen($_POST['shortDescription']) > 250) {
						$metaData['shortDescription'] = preg_replace("/[^a-zA-Z0-9\s.,?!]/", '', substr($_POST['shortDescription'], 0, 250));
					} else {
						$metaData['shortDescription'] = preg_replace("/[^a-zA-Z0-9\s.,?!]/", '',  $_POST['shortDescription']);
					}
				}

				if (!empty($_POST['longDescription'])) {
					if (strlen($_POST['longDescription']) > 4000) {
						$metaData['longDescription'] = preg_replace("/[^a-zA-Z0-9\s.,?!]/", '',  substr($_POST['longDescription'], 0, 4000));
					} else {
						$metaData['longDescription'] = preg_replace("/[^a-zA-Z0-9\s.,?!]/", '',  $_POST['longDescription']);
					}
				}
	
                if (!empty($videoData['videoKeywords'])) {
                        $metaData['tags'] = $videoData['videoKeywords'];
                }
                $metaData['name'] = preg_replace("/[^a-zA-Z0-9\s.,?!]/", '', $videoData['videoTitle']);
	
		        $options = array('create_multiple_renditions' => 'true', 'encode_to' => 'MP4');
				
				$videoId = $brightcove->createMedia('video', dirname(__FILE__) . '/uploads/' . $_FILES['videoFile']['name'], $metaData, $options);
				
				if (!empty($videoId)) {
					$msg[] = 'Video uploaded successfully.';
				}
			} else {
				$msg[] = 'Error uploading file.';
			}
	
	}
	//print_r($_FILES);
	//exit;
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
 
/**
 * 
 * Load javascript required by the backend administration pages
 * 
 **/
function brightcoveVideoLoadJs()
{	
	wp_enqueue_script('validateJS', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.validate.js', array('jquery'), '1.0');
	wp_enqueue_script('placeholdersJS', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.placeholders.js', array('jquery'), '1.0');
	wp_enqueue_script('colorBox', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.colorbox.js', array('jquery'), '1.0');
	wp_enqueue_script('tableSorter', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.tablesorter.js', array('jquery'), '1.0');	
	wp_enqueue_script('tablePaginator', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.paginator.js', array('jquery'), '1.0');
	wp_enqueue_script('tagsInput', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/js/jquery.tagsinput.js', array('jquery'), '1.0');			
}

/**
 * 
 * Load the custom style sheets for the admin pages
 * 
 **/
function brightcoveVideoLoadCss()
{
	wp_register_style('brightcove_video_default', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/css/style.css');
	wp_enqueue_style('brightcove_video_default');
	
	wp_register_style('brightcove_video_colorbox', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/css/colorbox.css');
	wp_enqueue_style('brightcove_video_colorbox');	
	
	wp_register_style('brightcove_tabbed_content', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/css/tabbedcontent.css');
	wp_enqueue_style('brightcove_tabbed_content');	

	wp_register_style('brightcove_video_info', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/css/videoinfo.css');
	wp_enqueue_style('brightcove_video_info');	
	
	wp_register_style('brightcove_video_tags', WP_PLUGIN_URL . '/' . BRIGHTCOVE_PLUGIN_DIR . '/css/tagsinput.css');
	wp_enqueue_style('brightcove_video_tags');					
}

/**
 * 
 * Embed the video into a page or post
 * 
 */
function brightcoveVideoEmbed($videoInfo)
{	
	if ((empty($videoInfo['video'])) && (!is_numeric($videoInfo['video']))) {
		return false;
	}
	$pluginSettings = brightcoveVideoCheckPluginSettings();
	$videoTemplate = file_get_contents( dirname(__FILE__) . '/views/embed-video.php');
	
	if ((!empty($videoInfo['width'])) && (is_numeric($videoInfo['width']))) {
		$videoTemplate = str_replace('{player_width}', $videoInfo['width'], $videoTemplate);
	} else {
		$videoTemplate = str_replace('{player_width}', 640, $videoTemplate);
	}

	if ((!empty($videoInfo['height'])) && (is_numeric($videoInfo['height']))) {
		$videoTemplate = str_replace('{player_height}', $videoInfo['height'], $videoTemplate);
	} else {
		$videoTemplate = str_replace('{player_height}', 360, $videoTemplate);
	}	
	$videoTemplate = str_replace('{player_id}', $pluginSettings['brightcove_player_id'], $videoTemplate);
	$videoTemplate = str_replace('{video_id}', $videoInfo['video'], $videoTemplate);
	echo $videoTemplate;	
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