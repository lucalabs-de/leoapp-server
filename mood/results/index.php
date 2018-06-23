<?php

    require_once('../../apiEndpoint.php');

    class GetMoodResults extends ApiEndpoint {

        protected function getMethod() {
            return "GET"; 
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $uid = $db->real_escape_string($_GET['uid']);
            $filter = $_GET['filter'];

            $json = array();
            $selection = array_unique(explode("|", $filter));

            if (sizeOf($selection) === 0) {
                $selection = array("own", "student", "teacher");
            }

            foreach ($selection as $cur) {
                switch($cur) {
                    case "own":
                    parent::exitOnBadRequest($uid);
                        $queryId = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Vote.uid = $uid GROUP BY vdate ORDER BY vdate DESC";
                        $arrayOwn = getArray($db->query($queryId));
                        $json["own"] = $arrayOwn;
                        break;
                    case "student":
                        $queryStudent = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Users.uid = Vote.uid AND Users.upermission != 2 GROUP BY vdate ORDER BY vdate DESC";
                        $arrayStudent = getArray($db->query($queryStudent));
                        $json["student"] = $arrayOwn;
                        break;
                    case "teacher":
                        $queryTeacher = "SELECT DAY(vdate) as vday, MONTH(vdate) as vmonth, YEAR(vdate) as vyear, AVG(vid) as vvalue FROM Vote, Users WHERE Users.uid = Vote.uid AND Users.upermission = 2 GROUP BY vdate ORDER BY vdate DESC";
                        $arrayTeacher = getArray($db->query($queryTeacher));
                        $json["teacher"] = $arrayOwn;
                        break;
                }
            }

            parent::returnApiResponse($json);

            $db->close();
        }

        private function getArray($result) {
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $array = array();
            while($row = $result->fetch_assoc()) {
                $result = array(
                    "value" => $row['vvalue'],
                    "date" => $row['vday'].".".$row['vmonth'].".".$row['vyear']
                );
                $array[] = $result;
            }

            return $array;
        }

    }

    new GetMoodResults();

?>