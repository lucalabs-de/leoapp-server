<?php

    require_once('../../apiEndpoint.php');

    new GetBasicUserInfo();

    class GetBasicUserInfo extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = getConnection();

            $name = $db->real_escape_string($_GET['name']);

            exitOnBadRequest($name)

            $query = "SELECT uid, uklasse as k, upermission as p, ucreatedate as c, uname as n, udefaultname as d FROM Users WHERE udefaultname = '$name' OR uname = '$name'";
            $result = $db->query($query);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                returnApiError("No user with name $name", 404);
            }

            $row = $result->fetch_assoc();

            $json = array(
                "id" => $row['uid'],
                "name" => $row['n'],
                "defaultname" => $row['d'],
                "grade" => $row['k'],
                "permission" => $row['p'],
                "createdate" => $row['c']
            );

            returnApiResponse($json);

            $db->close();
        }

    }

?>