<?php

    require_once('../../apiEndpoint.php');

    new AddMoodVote();

    class AddMoodVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $vid = $db->real_escape_string($_POST['vid']);
            $grund = $db->real_escape_string($_POST['grund']); //reason optional and currently unused
            $date = date("Y-m-d");

            exitOnBadRequest($uid, $vid);

            $query = "INSERT INTO Vote VALUES ($vid, $uid, '$date', '$grund')";

            $result = $db->query($query);
            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>