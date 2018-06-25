<?php

    require_once('../../apiEndpoint.php');

    class AddMoodVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $db->real_escape_string($_POST['id']);
            $vid = $db->real_escape_string($_POST['vote_id']); //1-5
            $grund = $db->real_escape_string($_POST['grund']); //reason optional and currently unused
            $date = date("Y-m-d");

            parent::exitOnBadRequest($uid, $vid);

            if ($vid < 1 || $vid > 5) {
                parent::returnApiError("$vid is not a valid value", 400);
            }

            $query = "SELECT uid FROM Users WHERE uid = $uid";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                parent::returnApiError("user $uid does not exist", 404);
            }

            $query = "INSERT INTO Vote VALUES ($vid, $uid, '$date', '$grund') ON DUPLICATE KEY UPDATE vid = $vid";
            $result = $db->query($query);
            
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

    new AddMoodVote();

?>