<?php
require_once('/var/www/html/sharkPulse/pulseMonitor/postgreConfig.php');
include_once('/var/www/html/sharkPulse/wp-load.php'); //for getting global variables like wpdb
$current_user = wp_get_current_user();

// Set class to store vulnerable, endagered and critically endagered species from cons_status.csv.
// Will be used to check if user input contains them. 
class Set
{
    private $elements = [];

    public function add($element)
    {
        $this->elements[$element] = true;
    }

    public function remove($element)
    {
        unset($this->elements[$element]);
    }

    public function contains($element)
    {
        return isset($this->elements[$element]);
    }
}

$vulnerable = new Set;
$critically_endangered = new Set;
$endangered = new Set;
$near_threatened = new Set;
$least_concern = new Set;

$cons_status_file = fopen("/var/www/html/sharkPulse/pulseMonitor/cons_status.csv","r");
/* Add entries from the csv to the vulnerable/endangered/critically endangered lists */
while (($data = fgetcsv($cons_status_file,1000,",")) !== FALSE) {
     /*NOTE: strtolower() Converts to lowercase. Expressions will be checked in lowercase */
        // Add specied according its type
 	    if(strcmp($data[1] ,"Vulnerable") == 0 ) {
            $vulnerable->add( strtolower($data[0]) ); 
		}
		else if(strcmp($data[1] ,"Endangered") == 0 ) {
            $endangered->add ( strtolower($data[0]) );
		}
		else if( strcmp($data[1] ,"Critically Endangered") == 0 ) {
            $critically_endangered->add( strtolower($data[0]) );
		}
        else if( strcmp($data[1] ,"Near Threatened") == 0 ) {
            $near_threatened->add( strtolower($data[0]) );
		}
        else if( strcmp($data[1] ,"Least Concern") == 0 ) {
            $least_concern->add( strtolower($data[0]) );
		}
}
$vulnerable->add("test");




if (array_key_exists('QUERY_STRING', $_SERVER)) {
	    $uri = $_SERVER['QUERY_STRING'];
} else {
    exit(1);
}
if(getenv('REQUEST_METHOD') == "POST") {
	$email = $current_user->user_email;
	$user_id = $current_user->ID;
	$id = $_POST["id"];
	$comment = $_POST["comment"];
	$species = $_POST["species"];
	$common = $_POST["common"];
	// $common = $_POST["common"];
	$sql_table = "select * from instagram where id=$id;";
	$instagram = pg_fetch_row(pg_query($dbconn, $sql_table));
	$shark = $instagram[7];
	// $is_shark_2 = $QQ[11];
	$users_email = $instagram[18];
	$lat = $_POST["formLatitude"];
	$lon = $_POST["formLongitude"];
	// $users_email_2 = $instagram[13];
	// $control = $instagram[18];
	date_default_timezone_set('America/New_York');
	$date = date("Y-m-d H:i:s"); 
	//$conservation_status = $
	//shell_exec("Rscript conservation_status.R {$species}");

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
</head>
<body>
<style>
img {
	float;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>
    <h1>Thank You</h1>
    <p>Here is the information you have submitted:</p>
    <ol>
	<li>Shark: '.$_POST["radioshark"].'</li>
        <li>Species Name: '.$_POST["species"].'</li>
	<div>
	<img src='.$_POST["img_name"].' style="width:30%;">
	</div>

	<li>image ID: '.$_POST["id"].'</li>
	<li>email: '.$email.'</li>
    </ol>
<input type="submit" value="Return to Map" 
    onclick="window.location=\'http://sp2.cs.vt.edu/validationMonitor/sharkPulseForm_single.php\';" />   
    
</body>
</html>';


	$validation_tracking = "insert into user_validations(user_email, image_id, social_network) values ('$email', $id, 'instagram');";
	pg_query($dbconn, $validation_tracking);
	
	if (isset($_POST['radioaq'])){
		$radioaq = $_POST["radioaq"];
		$aq_query = "update instagram SET aquarium='$radioaq' where id=$id;";
		pg_query($dbconn, $aq_query);
	}

	if (isset($_POST['radioshark'])) {
		$radioChoice = $_POST["radioshark"];
		$date_query = "update instagram SET valtime='$date' where id=$id;";
		pg_query($dbconn, $date_query);

		if ($comment!=''){
			$sql_comment="update instagram SET comment='$comment' where id=$id;";
			pg_query($dbconn, $sql_comment);
		}

		if ($radioChoice=="no"){
			$sql = "update instagram SET validated=true, validator='$email', users_email='$email', shark='not_shark' where id=$id;";
			$result = pg_query($dbconn, $sql);
			$answer_points = "update userbase SET points=points+3 where email='$email';";
			$answer_points = pg_query($dbconn, $answer_points);
			
		}
		elseif ($radioChoice="yes"){
			// $sql = "update instagram SET validated='t', shark='shark' where id=$id;";
			$answer_points = "update userbase SET points=points+3 where email='$email';";
			$answer_points = pg_query($dbconn, $answer_points);
			if ($species != ""){
				
				$sql = "update instagram SET validated=true, users_email='$email', validator='$email', shark='shark', common_name='$common', species_name='$species', latitude=$lat, longitude=$lon where id=$id;";
				 
				// Get number of species name of user input from sharkpulse
				$species_query = pg_query($dbconn , "select species_name from sharkpulse where species_name='$species';");
				$species_count=pg_num_rows($species_query);
		        
				// Get total count of species in sharkpulse
				/*
				$baseline= pg_query($dbconn , "select count('species_name') as total from sharkpulse;"); 
				while($row = pg_fetch_array($query)){
					$total = $row["total"];
				}
				*/
				$baseline = pg_query($dbconn , "select species_name from sharkpulse;");
				$total=pg_num_rows($baseline);

				$number = ($species_count / $total) * 100; 	                                    // Number of occurences of user input / total species in sharkpulse
				$threshold = 1; 			                                                    // Anything less than that is rare
				// $sql_user = "update userbase SET points=points+3 where email='$email';";		
				if($number < $threshold && $number > 0){ 
					$rare = "update userbase SET points=points+5 where email='$email';";
					$result_rare = pg_query($dbconn, $rare);    // 5 val points total if rare
																// currently not working
				}

				// Check for conservation status. strtolower converts user input to lower case, 
				// then checks if it exists in one of the sets. 
				if($vulnerable->contains( strtolower($species) )) {
					$sql_user = "update userbase SET points=points+5 where email='$email';";     // 5 val points total if vulnerable
				}
				else if($endangered->contains( strtolower($species) )) {
					$sql_user = "update userbase SET points=points+7 where email='$email';";     // 7 val points total if endangered
				}
				else if($critically_endangered->contains( strtolower($species) )) {
					$sql_user = "update userbase SET points=points+10 where email='$email';";     // 10 val points total if critically endangered
				}
				else if($near_threatened->contains( strtolower($species) )) {
					$sql_user = "update userbase SET points=points+3 where email='$email';";     // 3 val points total if near threatened
				}
				else if($least_concern->contains( strtolower($species) )) {
					$sql_user = "update userbase SET points=points+2 where email='$email';";     // 2 val points total if least concern
				}
				
				$result_user = pg_query($dbconn, $sql_user);
			}
			// if ($common != ""){
			// 	$sql = "update data_mining SET validated=true, users_email='$email', is_shark_2=true, common_name_1='$common', yes_elo=yes_elo+(select elo from userbase where email='$email') where id=$id;";
			// 	$sql_user = "update userbase SET points=points+3 where email='$email';";
			// }
			// if ($species != "" && $common != ""){
			// 	$sql = "update data_mining SET validated=true, users_email='$email', is_shark_2=true, species_name_1='$species', common_name_1='$common', yes_elo=yes_elo+(select elo from userbase where email='$email') where id=$id;";
			// 	$sql_user = "update userbase SET points=points+5 where email='$email';";
			// }
			$result = pg_query($dbconn, $sql);
			$result_user = pg_query($dbconn, $sql_user);
		}

	}
	pg_close($dbconn);
}

// Testing 
 //var_dump($vulnerable->contains("Triakis maculata")); //true
// var_dump($vulnerable->contains("not present"));      //false

?>


