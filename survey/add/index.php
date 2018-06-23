<?php

    require_once('../../apiEndpoint.php');

    new AddSurvey();

    class AddSurvey extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $id = $db->real_escape_string($_POST['id']);
            $to = $db->real_escape_string($_POST['to']);
            $title = $db->real_escape_string($_POST['title']);
            $answers = $db->real_escape_string($_POST['answers']);
            $description = $db->real_escape_string($_POST['desc']);
            $multiple = $db->real_escape_string($_POST['mult']);

            exitOnBadRequest($id, $to, $title, $answers, $description, $multiple);

            //REMOVE EXISTING SURVEY

            $query = "DELETE FROM Survey WHERE owner = ".$id;
            $db->query($query);

            $query = "DELETE FROM Answers WHERE survey = ".$id;
            $db->query($query);

            //INSERT SURVEY DATA
            switch($to) {
                case 0:
                    $to = "Alle";
                break;
                case 1:
                    $to = "Sek I";
                break;
                case 2:
                    $to = "Sek II";
                break;
                case 8:
                    $to = "EF";
                break;
                case 9:
                    $to = "Q1";
                break;
                case 10:
                    $to = "Q2";
                break;
                default:
                    $to += 2;
            }

            $query = "INSERT INTO Survey VALUES ($id, '$title', '$description', '$to', '$multiple', '".date("Y-m-d H:i:s")."')";

            $result = $db->query($query);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            //INSERT ANSWERS

            foreach (explode("_;_", $answers) as $answer) {
                $query = "INSERT INTO Answers VALUES (null, ".$id.", '".$answer."')";
                $result = $db->query($query);
                if ($result === false) {
                    returnApiError("Internal Server Error", 500);
                }
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>