<?php
require_once('./common.php');

# Generate Unique ID
#$token=str_replace('.','',uniqid('', true));

# Get Web form datas
$token = mysqli_real_escape_string($db,$_POST['token']);
$coordinates_lat = mysqli_real_escape_string($db,$_POST['coordinates_lat']);
$coordinates_lon = mysqli_real_escape_string($db,$_POST['coordinates_lon']);
$comment = mysqli_real_escape_string($db,$_POST['comment']);
$categorie = mysqli_real_escape_string($db,$_POST['categorie']);
$address = mysqli_real_escape_string($db,$_POST['address']);
$time = mysqli_real_escape_string($db,$_POST['time']);
$time = floor($time / 1000);

# Check if token exist
$query_token = mysqli_query($db,"SELECT * FROM obs_list WHERE obs_token='".$token."' LIMIT 1");
if(mysqli_num_rows($query_token) == 1 OR empty($token)) {
  $token=strtoupper(substr(str_replace('.','',uniqid('', true)), 0, 8));
}

# Init Datas
$status = 0;
#$json = array('token' => $token, 'status' => 0, 'street' => 'Rue non trouvé');
$json = array('token' => $token, 'status' => 0);
# Insert user datas to MySQL Database
if(!empty($coordinates_lat) and !empty($coordinates_lon) and !empty($comment) and !empty($categorie)) {

  mysqli_query($db,'INSERT INTO obs_list (`obs_coordinates_lat`,`obs_coordinates_lon`,`obs_address_string`,`obs_comment`,`obs_categorie`,`obs_token`,`obs_time`,`obs_status`) VALUES
				  ("'.$coordinates_lat.'","'.$coordinates_lon.'","'.$address.'","'.$comment.'","'.$categorie.'","'.$token.'","'.$time.'",0)') ;

  if($mysqlerror = mysqli_error($db)) {
    $status = 1;
    error_log('CREATE_ISSUE : MySQL Error '.$mysqlerror);
  }
}
else {
  $status = 1;
  error_log('CREATE_ISSUE : Field not supported');
}

# If error force return 500 ERROR CODE
if($status != 0) {
  http_response_code(500);
}

## Get steet informations
#$url_steet='https://www.mapquestapi.com/geocoding/v1/reverse?key='.$mapquestapi_key.'&location='.$coordinates_lat.'%2C'.$coordinates_lon.'&outFormat=json&thumbMaps=false&delimiter=%2C';
#$street_download_path = './places/'.$token.'.json';

#if(!file_exists($street_download_path)) {
#  $json_content = file_get_contents($url_steet);
#  file_put_contents($street_download_path, $json_content);
#} else {
#  $json_content = file_get_contents($street_download_path);
#}

#$json_street = json_decode($json_content, true); 
#$street_name = $json_street['results'][0]['locations'][0]['street'];

# Return Token value
$json['status'] = $status;
#$json['street'] = $street_name;
echo json_encode($json);
?>
