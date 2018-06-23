<?php

    require_once('../../apiEndpoint.php');

    class GetUserInfo extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = getConnection();

            $id = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($id);

            $query  = "SELECT udefaultname as d, uklasse as k, upermission as p, ucreatedate as c, uname as n FROM Users WHERE uid = $id";
            $result = $db->query($query); 

            if($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                returnApiError("No user with id $id", 404);
            }

            $assoc = $result->fetch_assoc();

            $json = array(
                "id" => $id,
                "name" => $assoc['n'],
                "defaultname" => $assoc['d'],
                "grade" => $assoc['k'],
                "permission" => $assoc['p'],
                "createdate" => $assoc['c']
            );

            returnApiResponse($json);

            $db->close();
        }

    }

    new GetUserInfo();

?>