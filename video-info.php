<?php

// Include the BCMAPI SDK
require_once('includes/bc-mapi.php');

$tokenRead;
$tokenWrite;


require_once('../../../wp-config.php');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db(DB_NAME,$link);

//getting tokens from DB
$sql = sprintf("SELECT * FROM wp_bc_video_plugin WHERE userId=1");
	
	
	$result = mysql_query($sql,$link) or die(mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$tokenRead = trim($row->tokenRead);
		$tokenWrite = trim($row->tokenWrite);	
	}


// Instantiate the class, passing it our Brightcove API tokens (read, then write)
$bc = new BCMAPI(
   $tokenRead,
   $tokenWrite
);
  
$videoId = $_GET['videoId'];
$videos = $bc->find('find_video_by_id', $videoId);

function convertMilliseconds($ms){
	$milliseconds = $ms; // number of milliseconds
	$minutes = floor($milliseconds / (1000 * 60));
	$seconds = ceil($milliseconds % (1000 * 60) / 1000);
	echo $minutes . ':' . (($seconds < 10) ? '0' : '') . $seconds;
}

$creationDate = $videos->creationDate/1000;
$publishedDate = $videos->publishedDate/1000;
$lastModifiedDate = $videos->lastModifiedDate/1000;

$creationDate = date("M j, Y", $creationDate);
$publishedDate = date("M j, Y", $publishedDate);
$lastModifiedDate = date("M j, Y", $lastModifiedDate);
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link href='css/tabbedContent.css' rel='stylesheet' type='text/css' />
<link href='css/videoInfo.css' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript">


//tab effects
var TabbedContent = {
	init: function() {	
		$(".tab_item").mouseover(function() {
		
			var background = $(this).parent().find(".moving_bg");
			
			$(background).stop().animate({
				left: $(this).position()['left']
			}, {
				duration: 300
			});
			
			TabbedContent.slideContent($(this));
			
		});
	},
	
	slideContent: function(obj) {
		
		var margin = $(obj).parent().parent().find(".slide_content").width();
		margin = margin * ($(obj).prevAll().size() - 1);
		margin = margin * -1;
		
		$(obj).parent().parent().find(".tabslider").stop().animate({
			marginLeft: margin + "px"
		}, {
			duration: 300
		});
	}
}




$(document).ready(function() {
	TabbedContent.init();
});

</script>
</head>
<body>
<div class='tabbed_content'>
	    <div class='tabs'>
	        <div class='moving_bg'>
&nbsp;	        </div>
	        <span class='tab_item'>
	            Basic </span>
	        <span class='tab_item'>
	            Advanced </span>	    </div>
	 
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
                                    <td><span id="valueField"><?php convertMilliseconds($videos->length) ; ?></span></td>
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

</body>
</html>