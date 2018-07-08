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

            $checksum = $db->real_escape_string($_POST['checksum']);
            $device = $db->real_escape_string($_POST['device']);
            $name = $db->real_escape_string($_POST['name']);

            parent::exitOnBadRequest($checksum, $name, $device);

            if (!verifyChecksum($checksum, $name)) {
                parent::returnApiError("checksum not valid", 400);
            }

            $query = "SELECT uname, uid, uklasse FROM Users WHERE udefaultname = '$name'";
            $result = $db->query($query);
            
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if($result->num_rows == 0) { //User doesn't exist yet and is created
                $permission = (strlen($name) == 6 ? 2 : 1);
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

            $query = "SELECT uid FROM Users WHERE udefaultname = '$name'";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $id = $result->fetch_assoc()['uid'];

            $query = "INSERT INTO Devices VALUES ('$device', $id, '$checksum', null) ON DUPLICATE KEY UPDATE checksum = '$checksum'";
            $result = $db->query($query);
        
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiResponse(array("user_id" => $id));

            $db->close();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::NONE;
        }

    }

    new AddUser();

?>