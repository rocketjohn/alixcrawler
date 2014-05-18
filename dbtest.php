<?php

$dsn = 'mysql:host=localhost;dbname=crawler';
$username = 'crawler';
$password = 'alix';


$dbh = new PDO($dsn, $username, $password );

$stmt = $dbh->prepare("INSERT INTO record_user_tags (record_id, user_id, tag_id) VALUES (:rid, :uid, :tid)");

$stmt->bindParam(':rid', $rid);
$stmt->bindParam(':uid', $uid);
$stmt->bindParam(':tid', $tid);

$rid = $uid = $tid = 4;

$stmt->execute();

foreach($dbh->query("select * from record_user_tags") as $row)
	print_r($row);


