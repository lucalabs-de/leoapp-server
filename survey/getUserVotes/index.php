<?php

    require_once('../../apiEndpoint.php');

    new GetUserVotes();

    class GetUserVotes extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {

            $db = getConnection();

            $user = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($user);

            $query = "SELECT answer as ans FROM Result WHERE user = $user";
            $result = $db->query($query);

            if($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            $json = array();
            $votes = array();
            while($row = $result->fetch_assoc()) {
                $array[] = $row['ans'];
            }

            $json["voted_for"] = $votes;

            returnApiResponse($json);

            $db->close();
        }

    }

?>