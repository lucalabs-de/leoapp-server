<?php

    require_once('../../apiEndpoint.php');

    class GetEntries extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $date = date("Y-m-d");

            $query = "SELECT EintragID, Anhang, Gelesen, Titel, Adressat, Inhalt, UNIX_TIMESTAMP(Erstelldatum) as Erstell, UNIX_TIMESTAMP(Ablaufdatum) as Ablauf FROM Eintraege WHERE Ablaufdatum >= '$date' ORDER BY Erstell DESC";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $json = array();

            while ($row = $result->fetch_assoc()) {

                $entry = array(
                    "id" => $row['EintragID'],
                    "title" => $row['Titel'],
                    "content" => $row['Inhalt'],
                    "recipient" => $row['Adressat'],
                    "view_counter" => $row['Gelesen'],
                    "attachment" => $row['Anhang'],
                    "valid_until" => $row['Ablauf']
                );

                $json[] = $entry;
            }
            
            protected function getPermissionLevel() {
                return PermissionLevel::ONLY_AUTHENTICATION;
            }

            parent::returnApiResponse($json);

            $db->close();
        }

    }

    new GetEntries();

?>