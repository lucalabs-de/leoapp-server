<?php

    require_once('../../apiEndpoint.php');

    //Adds a new user to the LeoApp, based on a checksum by the verification script

    class AddUser extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {

            $db = parent::getConnection();

            $checksum = $_POST['checksum'];
            $name = $db->real_escape_string($_POST['name']);

            parent::exitOnBadRequest($checksum, $name);

            if (strcmp($this->getChecksumFromName($name), $checksum) !== 0) {
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
                
                $query = "INSERT INTO Users VALUES (null, '".$name."', '".$name."', '".$klasse."', ".$permission.", '".$date."')";
                $result = $db->query($query);
                if ($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }
            }
        
            parent::returnApiSuccess();

            $db->close();
        }

        private function getChecksumFromName($name) {
            $hex = "";
            for ($i = 0; $i < 6; $i++) {
                $char = (ord($name[$i]) - 97) % 16;
                if ($char > 9) {
                    $hex = $hex . chr(55 + $char);
                } else {
                    $hex = $hex . $char;
                }
                if (strlen($name) == 12) {
                    $hex = $hex . $name[6 + $i];
                }
            }

            return $hex;
        }

    }

    new AddUser();

?>