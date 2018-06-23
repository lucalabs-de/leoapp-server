<?php

    require_once('../../apiEndpoint.php');

    new UpdateUsername();

    class UpdateUsername extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $uname = $db->real_escape_string($_POST['uname']);

            exitOnBadRequest($uid, $uname);

            if (preg_match($name, "/[a-zA-Z]{6}\d{6}/") === 1) {
                returnApiError("username is not valid", 400);
            }

            $query = "SELECT * FROM Users WHERE uname = '$uname'";
            $result = $db->query($query);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows > 0) {
                returnApiError("username already exists", 400)
            }

            $query = "UPDATE Users SET uname = '$uname' WHERE uid = $uid";
            $result = $db->query($query);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }
            
            returnApiSuccess();

            $db->close();
        }

    }

?>