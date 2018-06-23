<?php

    require_once('../../apiEndpoint.php');

    class UpdateUsername extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $uname = $db->real_escape_string($_POST['uname']);

            parent::exitOnBadRequest($uid, $uname);

            if (preg_match($name, "/[a-zA-Z]{6}\d{6}/") === 1) {
                parent::returnApiError("username is not valid", 400);
            }

            $query = "SELECT * FROM Users WHERE uname = '$uname'";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows > 0) {
                parent::returnApiError("username already exists", 400)
            }

            $query = "UPDATE Users SET uname = '$uname' WHERE uid = $uid";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }
            
            parent::returnApiSuccess();

            $db->close();
        }

    }

    new UpdateUsername();

?>