<?php
if ('videos.php' == basename($_SERVER['SCRIPT_FILENAME'])){
	die ('Please do not access this file directly. Thanks!');
}

?>

<html>  
<head>   
    <link rel="stylesheet" href="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/css/' ;?>style.css" type="text/css" /> 
    <link rel="stylesheet" href="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/css/' ;?>shadowbox.css" type="text/css" />  
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>   
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>jquery.tablesorter.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>quickpager.js"></script>
    <script type="text/javascript" src="<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/js/' ;?>shadowbox.js"></script>
    
    
    <style>
    
    	#info, #success, #warning,  #validation {
		border: 1px solid;
		margin: 10px 0px;
		padding:15px 10px 15px 50px;
		background-repeat: no-repeat;
		background-position: 10px center;
		margin-top: 25px;
		}
		#info {
		color: #00529B;
		background-color: #BDE5F8;
		background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/info.png'; ?>');
		}
		#success {
		color: #4F8A10;
		background-color: #DFF2BF;
		background-image:url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/good.png';?>');
		}
		#warning {
		color: #9F6000;
		background-color: #FEEFB3;
		background-image: url('<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/images/warning.png';?>');
		}
		
    
    </style>

</head> 

<body> 

<div class="wrap">

<?php

// Include the BCMAPI SDK
require_once('includes/bc-mapi.php');

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


// Define our parameters
$params = array(
    'video_fields' => 'id,name,shortDescription,creationDate',
    'sort_by' => 'CREATION_DATE'
);


$numVideos = 20;

try{
	// Make our API call
	$videos = $bc->findAll('video', $params);
}catch(Exception $e){
	$numVideos = 0;
	echo '<div id="warning"><span>You have an invalid token for token red, or token write. Please use a valid token. <a href="admin.php?page=bc-settings" ><strong>settings</strong></a>.</span></div>';
	
}





if (isset($_POST['submit'])) { 
	$searchBy = trim($_POST['searchBy']);
	$valueSearch = trim($_POST['valueSearch']);
		
	if(empty($valueSearch)){
		$showErrorEmpty = true;
	}else{	
		if($searchBy == "id"){	
			if (!preg_match("/\d/", $valueSearch)) {	
					$showErrorNumber = true;												
			}else{
				 
				$videos = $bc->find('find_video_by_id', $valueSearch);	
				$numVideos	= count($videos);
			}
		}
			
		if($searchBy == "name"){
			 $videos = $bc->find('videosbytext', $valueSearch);
			 $numVideos	= count($videos);
		}				
		
	}
	
}

?>

<script type="text/javascript">
var isNumber = false;
		
//hide error when name is selected
function changeSearchType(){
	searchBy = document.getElementById("searchBy").value;
	if(searchBy == "name"){
		document.getElementById("error").style.display = "none";
	}
}
		
//check number before sending the form
function checkNumber(searchForm){
	searchBy = document.getElementById("searchBy").value;
	
	if(searchBy == "id"){
		if(isNumber){
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

//check number on field searchValue
function checkNumberField(value){
	searchBy = document.getElementById("searchBy").value;
	
	if(searchBy == "id"){
		if(isNaN(value)){
			document.getElementById("error").style.display = "inline";
			document.getElementById("error").style.color = "#FF0000";
			isNumber = true;
		}else{
			document.getElementById("error").style.display = "none";
			isNumber = false;
		}
		
	}
	
	return false;
}

$(document).ready(function () {
			$('#myTable').paginateTable({ rowsPerPage: 10 });
		});
				

$(document).ready(function () {
								
				$("#searchForm").validate({		
					rules: {
						valueSearch:{
							required: true,
							minlength: 3,
							digits:false
						}
					},
					messages: {
						valueSearch:{
							required: "This field is required.",
							minlength: "Please enter at least 3 characters."
						}
					}
				
				});	
					
		});

//Calling lightbox to display video info
Shadowbox.init({});	
		
//table sorter
$(document).ready(function() 
			{ 
				$("#myTable").tablesorter( {sortList: [[0,1]]} ); 
			} 
		); 		
	
</script>

<?php   

if($tokenRead == 'Token Read Goes Here' || $tokenWrite == 'Token Read Goes Here' ){
		$numVideos = 0;
		echo '<div id="warning"><span>You need to change your <a href="admin.php?page=bc-settings" ><strong>settings</strong></a>.</span></div>';
	}else{
		$showTable = true;
	}


?>

 <h2>Brightcove Media</h2>

 <noscript>
 		
 		
	<meta http-equiv="Refresh" content="1;URL=<?php echo get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/nojs.html' ?>">

 		
        
 </noscript>

    <div id="searchFormDiv" style="margin-top: 10px;">
    <p>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="searchForm" onsubmit="return checkNumber(this);">
            <select id="searchBy" name="searchBy" onchange="changeSearchType(); return false;">
                <option value="id" selected="selected">Video ID</option>
                <option value="name" >Video Title</option>
            </select>
            <input  type="text" name="valueSearch" id="valueSearch" value="" size="40" maxlength="50" onchange="checkNumberField(this.value);"/>
            <span id="error">Only digits allowed</span> 
            <?php 
				if($showErrorEmpty){
					echo '<span style="color: #FF0000;">Search field is a required field </span>';
				}
				
				if($showErrorNumber){
					echo '<span style="color: #FF0000;"> Only digits allowed</span>';	
				}
			?>
			 
            <input  type="submit" name="submit" value="Search"  /> 
    		
    </form>
    </p>
    </div>
 
    <div id="allResults" >
    
        <table id="myTable" class="tablesorter" cellspacing="0" >
        <thead>
        <tr>        
            <th>Video ID</th>
            <th>Video Title</th>
            <th>Date Uploaded</th>
              
        </tr>
        </thead>
        <tbody>
        
        
        <?php
        
        if($showTable){
        
			$mu = array();
			
			if($numVideos == 0 || $numVideos == ""){ 
				echo "<tr><td colspan='3'> <center><span style='color: #FF0000;'>No Videos were Found! </span></center></td></tr>" ;
			}
			
			if($numVideos == 1){
				$creationDate = $videos->creationDate/1000;
					$mu[]= '<tr><td>'.$videos->id.'</td>';				
					$mu[]= '<td><a href="'.get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/videoInfo.php?videoId='.$videos->id.'" rel="shadowbox;width=700;height=500" >'.$videos->name.'</a></td>';
					$mu[]= '<td>'. date("M j, Y", $creationDate). '</td>';
					$mu[]= '</tr>';				                
				 
				echo implode("\n",$mu);
			}else{
			
				for ($i=0; $i<$numVideos; $i++) {
					$creationDate = $videos[$i]->creationDate/1000;
					$mu[]= '<tr><td>'.$videos[$i]->id.'</td>';				
					$mu[]= '<td><a href="'.get_option('siteurl').'/wp-content/plugins/wp-brightcove-video-plugin/videoInfo.php?videoId='.$videos[$i]->id.'" rel="shadowbox;width=700;height=500" >'.$videos[$i]->name.'</a></td>';
					$mu[]= '<td>'. date("M j, Y", $creationDate). '</td>';
					$mu[]= '</tr>';	
								                
				 }  
				echo implode("\n",$mu);
			}
     	
        }
       ?>
       </tbody>
       </table>
       <div class='pager'>
           <a href='#' alt='Previous' class='prevPage'>Prev</a>
           <span class='currentPage'></span> of <span class='totalPages'></span>
           <a href='#' alt='Next' class='nextPage'>Next</a>
       </div>
          
   </div> <!-- end div allResults -->
   
   <div id="searchResults">
   
   </div>

<div id="usage"> 

<h5>Plug-in Usage</h5>

<p>Once you've installed the plug-in, you can use the following syntax to incorporate publishing code into your WordPress content:</p>

<pre> [brightcove video="123456" /] </pre>

</div>  


<div id="donations"> 
<br />
<hr style="height:1px;color:#666;">

<p><a href="http://xtremenews.info/wordpress-plugins/wp-brightcove-video-plugin">Plugin Home Page</a></p>
</div>
   
</div> <!-- End Wrap -->

      
</body>  
</html>  
