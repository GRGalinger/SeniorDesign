<?php

require_once 'functions.inc.php';
require_once 'dbh.inc.php';

require_once dirname(__DIR__, 1) .'/vendor/autoload.php';
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

if (!file_exists("client_id_dropbox.json")) exit("Client secret file not found");
$data = file_get_contents("client_id_dropbox.json");
$data_json = json_decode($data, true);
$dropboxKey = $data_json['client_id'];
$dropboxSecret =$data_json['client_secret'];



// Check if this user has an access token in the db and if its expired 
$userId = $_SESSION['userid'];
$row = getUserCredentials($conn, $userId, "dropbox_credentials");

if ($row != false) {
    // Check if access token is expired
    var_dump($row);
    if (checkAccessToken($conn, $row['created'], $row['expires'])){
        // there is no refreshtoken functionality with this dropbox package (kunalvarma05)
 
        
    } 
    
    // Upload process..
    //Configure Dropbox Application
    $app = new DropboxApp($dropboxKey, $dropboxSecret, $row['accessToken']);

    //Configure Dropbox service
    $dropbox = new Dropbox($app);
    
    $authHelper = $dropbox->getAuthHelper();

    $dir = new DirectoryIterator('../uploads/');
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
        $fileName = $fileinfo->getFilename();
        try {
            $dropboxFile = new DropboxFile('../uploads/' . $fileinfo->getFilename());
            $uploadedFile = $dropbox->upload($dropboxFile, "/" . $fileName, ['autorename' => true]);
            // echo $uploadedFile->getPathDisplay();
        
        } catch (Exception $exc) {
            echo $exc->getMessage() . "\n";
        }
        }
    }


} else {
    //Configure Dropbox Application
    $app = new DropboxApp($dropboxKey,$dropboxSecret);
    
    //Configure Dropbox service
    $dropbox = new Dropbox($app);

    //DropboxAuthHelper
    $authHelper = $dropbox->getAuthHelper();
    
   
    //Callback URL
    $callbackUrl = "http://localhost/Projects/SeniorDesign/Development/includes/dropbox_oauthcallback.inc.php";
   
}







?>