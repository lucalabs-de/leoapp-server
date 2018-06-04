<?php

    require_once('../dbconfig.php');

    $db = new mysqli(dbhost, dbuser, dbpass, dbname);

    if ($db->connect_error)
        die("-connection failed: ".$db->connect_error);

    $id = $db->real_escape_string($_GET['id']);

    //udefaultname, uklasse, upermission, ucreatedate

    $query  = "SELECT udefaultname as d, uklasse as k, upermission as p, ucreatedate as c FROM Users WHRE uid=$id";
    $result = $db->query($query); 

    if($result === false)
        die("-ERR");

    $assoc = $result->fetch_assoc();

    echo $id."_;_".$assoc['d']."_;_".$assoc['k']."_;_".$assoc['p']."_;_".$assoc['c'];

?>