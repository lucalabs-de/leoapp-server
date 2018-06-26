<?php

    require_once('../../apiEndpoint.php');
    require_once('../../secureUser.php');

    //Adds a new user to the LeoApp, based on a checksum by the verification script

    class AddUser extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {

            $db = parent::getConnection();

            $checksum = $_POST['checksum'];
            $device = $db->real_escape_string($_POST['device'])
            $name = $db->real_escape_string($_POST['name']);

            parent::exitOnBadRequest($checksum, $name);

            if (verifyChecksum($checksum, $name)) {
                parent::returnApiError("checksum does not match username", 400);
            }

            $query = "SELECT uname, uid, uklasse FROM Users WHERE udefaultname = '$name'";
            $result = $db->query($query);
            
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if($result->num_rows == 0) { //User doesn't exist yet and is created
                $permission = $db->real_escape_string($_GET['permission']);
                $klasse = "N/A";
                if($permission == 2) {
                    $klasse = "TEA";
                }
                $date = date("Y-m-d");
                
                $query = "INSERT INTO Users VALUES (null, '$name', '$name', '$klasse', $permission, '$date')";
                $result = $db->query($query);
                if ($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }
            }
        
            parent::returnApiSuccess();

            $db->close();
        }

    }

    new AddUser();

?>