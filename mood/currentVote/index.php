<?php

    require_once('../../apiEndpoint.php');

    class GetMoodVote extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $_GET['id'];
            $date = date("Y-m-d");

            $query = "SELECT vid FROM Vote WHERE uid = $uid AND vdate = '$date'";
            $result = $db->query($query);

            if($result->num_rows > 0) {
                parent::returnApiError("No current vote for id $id", 404);
            }
            
            parent::returnApiResponse(array("value" => $result->fetch_assoc()['vid']));

            $db->close();
        }

    }

    new GetMoodVote();

?>