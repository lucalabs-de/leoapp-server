<?php

    require_once('../../../apiEndpoint.php');

    class GetViewCount extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $id = $db->real_escape_string($_GET['p0']);

            parent::exitOnBadRequest($id);

            $query = "SELECT Gelesen FROM Eintraege WHERE EintragID = $id";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                parent::returnApiError("No entry with id $id", 404);
            }

            $json = array("views" => $result->fetch_assoc()['Gelesen']);

            parent::returnApiResponse($json);

            $db->close();
        }

    }

    new GetViewCount();

?>