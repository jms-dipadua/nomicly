<?php 
    $host = "localhost";
    $user = jamesdipadua;
    $db_name = nomics;
    $db_psswd = Yaxz6BfZZxtT5Uef;
    
    mysql_connect($host,$user, $db_psswd) OR DIE ('Unable to connect to database! Please try again later.');
    mysql_select_db($db_name);
    
?>