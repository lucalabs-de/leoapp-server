<?php

    require_once('../../../apiEndpoint.php');

    new GetViewCount();

    class GetViewCount extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = getConnection();

            $id = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($id);

            $query = "SELECT Gelesen FROM Eintraege WHERE EintragID = $id";
            $result = $db->query($query)

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                returnApiError("No entry with id $id", 404);
            }

            $json = array("views" => $result->fetch_assoc()['Gelesen']);

            returnApiResponse($json);

            $db->close();
        }

    }

?>