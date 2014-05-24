<?php
header("Content-Type: text/csv");
header('Content-Disposition: attachment; filename=alix.csv');

include "db.php";

$dsn = 'mysql:host=localhost;dbname=crawler';
$username = 'crawler';
$password = 'alix';
$dbh = new PDO($dsn, $username, $password );

$stmt = $dbh->query("select * from users_tagging_records");

$rows = $stmt->fetchall(PDO::FETCH_ASSOC);

$headers = true;

foreach ($rows as $row)
{
	if ($headers)
	{
		$headers = false;
		$hary = array();
		foreach(array_keys($row) as $header)
			$hary[] = '"' . $header . '"';
		print implode(";", $hary) . "\n";
		unset($hary);
	}

	$cary = array();

	foreach($row as $cell)
		$cary[] = '"' . str_replace('"', '""', $cell) . '"' ;

	print implode(";", $cary) . "\n";

	unset($cary);

}
