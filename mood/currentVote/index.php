<?php

    require_once('../../apiEndpoint.php');

    new GetMoodVote();

    class GetMoodVote extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = getConnection();

            $uid = $_GET['id'];
            $date = date("Y-m-d");

            $query = "SELECT vid FROM Vote WHERE uid = $uid AND vdate = '$date'";
            $result = $db->query($query);

            if($result->num_rows > 0) {
                returnApiError("No current vote for id $id", 404);
            }
            
            returnApiResponse(array("value" => $result->fetch_assoc()['vid']));

            $db->close();
        }

    }

?>