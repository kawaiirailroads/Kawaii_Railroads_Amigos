<?php
function str2html(string $string):string{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function db_open() {
    $user = "your_username";
    $password = "your_password";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ];
    $dbh = new PDO('mysql:host=DB_HOST;dbname=YOUR_DB', $user, $password, $opt);
    return $dbh; //返り値を返す
}
?>