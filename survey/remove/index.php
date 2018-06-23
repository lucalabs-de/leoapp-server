<?php

    require_once('../../apiEndpoint.php');

    new RemoveSurvey();

    class RemoveSurvey extends ApiEndpoint {

        protected function getMethod() {
            return "DELETE"; //TODO add htaccess to redirect remove/{id} to remove/index.php?id={id}
        }

        protected function handleRequest() {
            $db = getConnection();

            $survey = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($survey);

            $query = "DELETE FROM Survey WHERE owner = $survey";
            $result = $db->query($query);
          
            $query = "DELETE FROM Answers WHERE survey = $survey";
            $result2 = $db->query($query);
          
            $query = "DELETE FROM Result WHERE answer IN (SELECT id FROM Answers WHERE survey = $survey)";
            $result3 = $db->query($query);
          
            if($result === false || $result2 === false || $result3 === false) {
                returnApiError("Internal Server Error", 500);
            }
          
            returnApiSuccess();

            $db->close();
        }

    }

?>