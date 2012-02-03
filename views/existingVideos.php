<div class="wrap">

<?php
try{
	// Make our API call
	$videos = $brightCove->findAll('video', $params);
}catch(Exception $e){
	$numVideos = 0;
	echo '<div id="warning"><span>You have an invalid token for token red, or token write. Please use a valid token. <a href="admin.php?page=bc-settings" ><strong>settings</strong></a>.</span></div>';	
}

$numVideos = count($videos);
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

	jQuery(function() {
								
				jQuery("#searchForm").validate({		
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
			<?php
				if ($pluginSettings['brightcove_videos_per_page']) {
					$videoRows = $pluginSettings['brightcove_videos_per_page'];	
				} else {
					$videoRows = 20;
				}
			?>
			jQuery('#myTable').paginateTable({ rowsPerPage: <?= $videoRows; ?> });
			jQuery("#myTable").tablesorter( {sortList: [[0,1]]} ); 
			
			jQuery(".colorBox").colorbox();		
		});			
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
							$mu[]= '<td><a href="'.get_option('siteurl').'/wp-content/plugins/brightcove_video/video-info.php?videoId='.$videos->id.'" class="colorBox" >'.$videos->name.'</a></td>';
							$mu[]= '<td>'. date("M j, Y", $creationDate). '</td>';
							$mu[]= '</tr>';				                
						 
						echo implode("\n",$mu);
					}else{
			
						for ($i=0; $i<$numVideos; $i++) {
							$creationDate = $videos[$i]->creationDate/1000;
							$mu[]= '<tr><td>'.$videos[$i]->id.'</td>';				
							$mu[]= '<td><a href="'.get_option('siteurl').'/wp-content/plugins/brightcove_video/video-info.php?videoId='.$videos[$i]->id.'" class="colorBox" >'.$videos[$i]->name.'</a></td>';
							$mu[]= '<td>'. date("M j, Y", $creationDate). '</td>';
							$mu[]= '</tr>';	
												
						 }  
						echo implode("\n",$mu);
					}
		     	
			}
		       ?>
	       </tbody>
       </table>

	  <?php 	  
		  if (($numVideos >= $videoRows ) && (!empty($numVideos))) { ?> 
				       <div class='pager'>
				           <a href='#' alt='Previous' class='prevPage'>Prev</a>
				           <span class='currentPage'></span> of <span class='totalPages'></span>
				           <a href='#' alt='Next' class='nextPage'>Next</a>
				       </div>
		 
		<?php  } ?>  
          
   </div> 

   <!-- end div allResults -->
   
   <div id="searchResults"></div>

   <div id="usage"> 
	<h5>Plug-in Usage</h5>

	<p>Once you've installed the plug-in, you can use the following syntax to incorporate publishing code into your WordPress content:</p>

	<pre> [brightcove video="123456" /] </pre>
   </div>  
   
</div> <!-- End Wrap --> 
