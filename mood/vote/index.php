<?php

    require_once('../../apiEndpoint.php');

    class AddMoodVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $vid = $db->real_escape_string($_POST['vid']);
            $grund = $db->real_escape_string($_POST['grund']); //reason optional and currently unused
            $date = date("Y-m-d");

            parent::exitOnBadRequest($uid, $vid);

            $query = "INSERT INTO Vote VALUES ($vid, $uid, '$date', '$grund')";

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