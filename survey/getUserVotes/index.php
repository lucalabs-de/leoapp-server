<?php

    require_once('../../apiEndpoint.php');

    class GetUserVotes extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {

            $db = parent::getConnection();

            $user = $db->real_escape_string($_GET['p0']);

            parent::exitOnBadRequest($user);

            $query = "SELECT answer as ans FROM Result WHERE user = $user";
            $result = $db->query($query);

            if($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $json = array();
            $votes = array();
            while($row = $result->fetch_assoc()) {
                $votes[] = $row['ans'];
            }

            $json["voted_for"] = $votes;

            parent::returnApiResponse($json);

            $db->close();
        }

    }

    new GetUserVotes();

?>