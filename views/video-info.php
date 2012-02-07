<?php
// error_reporting(0);
require_once(__DIR__ . '/../../../../wp-config.php');

// Get the existing videos from 
$pluginSettings = brightcoveVideoCheckPluginSettings();
$brightCove = brightcoveVideoGetApi($pluginSettings);

$videoId = $_GET['videoId'];


$videos = $brightCove->find('find_video_by_id', $videoId);

$creationDate = $videos->creationDate/1000;
$publishedDate = $videos->publishedDate/1000;
$lastModifiedDate = $videos->lastModifiedDate/1000;

$creationDate = date("M j, Y", $creationDate);
$publishedDate = date("M j, Y", $publishedDate);
$lastModifiedDate = date("M j, Y", $lastModifiedDate);
?>
<script type="text/javascript">

//tab effects
var TabbedContent = {
	init: function() {	
		jQuery(".tab_item").click(function() {
		
			var background = jQuery(this).parent().find(".moving_bg");
			
			jQuery(background).stop().animate({
				left: jQuery(this).position()['left']
			}, {
				duration: 300
			});
			
			TabbedContent.slideContent(jQuery(this));
			
		});
	},
	
	slideContent: function(obj) {
		
		var margin = jQuery(obj).parent().parent().find(".slide_content").width();
		margin = margin * (jQuery(obj).prevAll().size() - 1);
		margin = margin * -1;
		
		jQuery(obj).parent().parent().find(".tabslider").stop().animate({
			marginLeft: margin + "px"
		}, {
			duration: 300
		});
	}
}

jQuery(function() {
	TabbedContent.init();
});
</script>

<div class='tabbed_content'>
	    <div class='tabs'>
	        <div class='moving_bg'>&nbsp;</div>
	        <span class='tab_item'>
	            Basic 
	        </span>
	        
	        <span class='tab_item'>
	            More Info
	        </span>	    
 	  	</div>
	 
  <div class='slide_content'>
	        <div class='tabslider'>
	 
                        <ul>
                            <li>
                            	<h2>Basic Video Information:</h2>
                            	                         	
                            	<table id="myTable1">
                                  <tr>
                                    <td><span id="labelField"> Video ID </span></td>
                                    <td><span id="valueField"> <?php echo $videos->id ; ?> </span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Video Title </span></td>
                                    <td><span id="valueField"><?php echo $videos->name ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Short Description </span></td>
                                    <td><?php echo $videos->shortDescription ; ?></span></td>
                                  </tr>
                                  
                                  <tr>
                                    <td><span id="labelField"> Wordpress Shortcode </span></td>
                                    <td>[brightcove video="<?php echo $videos->id; ?>" ]</span></td>
                                  </tr>
                                                                    
                                </table>
                                                               
                           </li>
                        </ul>
                        <ul>
                            <li>
                                <h2>Advanced Video Information</h2>  
                                
                                
                                <table id="myTable2">
                                  <tr>
                                    <td><span id="labelField"> Thumbnail</span></td>
                                    <td><span id="valueField"><img src="<?php echo $videos->thumbnailURL ; ?>" title="Thumbnail" /></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Creation Date </span></td>
                                    <td><span id="valueField"><?php echo $creationDate ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Published Date </span></td>
                                    <td><span id="valueField"><?php echo $publishedDate ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Last Modified Date </span></td>
                                    <td><span id="valueField"><?php echo $lastModifiedDate ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Length </span> </td>
                                    <td><span id="valueField"><?php brightcoveVideoConvertMilliseconds($videos->length) ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Times this Video has been played since its creation </span></td>
                                    <td><span id="valueField"><?php echo $videos->playsTotal ; ?></span></td>
                                  </tr>
                                  <tr>
                                    <td><span id="labelField"> Times this Video has been played within the past seven days</span></td>
                                    <td><span id="valueField"><?php echo $videos->playsTrailingWeek ; ?></span></td>
                                  </tr>
                                </table>
                                                                            
                            </li>
                        </ul>	
	        </div>
  </div>
</div>
