<?php


// foreach($dbh->query("select * from record_user_tags") as $row)
// 	// print_r($row);


function insert_page_content($record_pagetitle, $record_recordtitle, $record_oid, $record_iid, $tag_text, $username, &$dbh)
{
	$record_id = insert_return_id("records", array( 
			"pagetitle" => $record_pagetitle, 
			"recordtitle" => $record_recordtitle,
			"oid" => $record_oid,
			"iid" => $record_iid 
		), $dbh );
	$tag_id = insert_return_id("tags", array( "text" => $tag_text), $dbh);
	$user_id = insert_return_id("users", array( "username" => $username), $dbh);

	return insert_no_id("record_user_tags", array( "user_id" => $user_id, "record_id" => $record_id, "tag_id" => $tag_id), $dbh);
}

function insert_return_id($table, $values, &$dbh)
{

	$check_params = array();

	foreach ($values as $column => $value) {
		$check_params[] = "{$column} = :{$column}";
	}

	$check_params = implode(" and ", $check_params);

	$check_sql = "select id from {$table} where {$check_params}";

	//  print $check_sql . "\n";
	
	$check = $dbh->prepare($check_sql);

	foreach ($values as $column => $value) {
		$check->bindParam(":{$column}", $values[$column]);
	}

	$check->execute();
	
	//  print "Rowcount: " . $check->rowcount() . "\n";
		
	if ($check->rowcount() > 0) {
		$row = $check->fetch(PDO::FETCH_ASSOC);
		unset($check);
		return $row['id'];
	}
	else {
		unset($check);

		$insert_columns_ary = array_keys($values);

		$insert_columns = implode(",", $insert_columns_ary );
		
		$insert_values = array();

		foreach ($insert_columns_ary as $value) {
		    $insert_values[] = ':' . $value;
		}  

		$insert_values = implode(",", $insert_values);

		$insert_sql = "insert into {$table} ({$insert_columns}) values ({$insert_values})";

		//  print $insert_sql . "\n";

		$insert = $dbh->prepare($insert_sql);

		foreach ($values as $column => $value) {

			$insert->bindParam(":{$column}", $values[$column]);
		}

		if ($insert->execute())
		{	
			unset($insert);
			return ($dbh->lastinsertid());
		}

		unset($insert);
	}

}


function insert_no_id($table, $values, $dbh)
{
	$check_params = array();

	foreach ($values as $column => $value) {
		$check_params[] = "{$column} = :{$column}";
	}

	$check_params = implode(" and ", $check_params);

	$check_sql = "select * from {$table} where {$check_params}";

	// print $check_sql . "\n";
	$check = $dbh->prepare($check_sql);

	foreach ($values as $column => $value) {
		$check->bindParam(":{$column}", $values[$column]);
	}

	$check->execute();
	
	// print "Rowcount: " . $check->rowcount() . "\n";
		
	if ($check->rowcount() > 0) {
		unset($check);
		return false; // no new record created
	}
	else {
		unset($check);
		$insert_columns_ary = array_keys($values);

		$insert_columns = implode(",", $insert_columns_ary );
		
		$insert_values = array();

		foreach ($insert_columns_ary as $value) {
		    $insert_values[] = ':' . $value;
		}  

		$insert_values = implode(",", $insert_values);

		$insert_sql = "insert into {$table} ({$insert_columns}) values ({$insert_values})";

		// print $insert_sql . "\n";

		$insert = $dbh->prepare($insert_sql);

		foreach ($values as $column => $value) {

			$insert->bindParam(":{$column}", $values[$column]);
		}
		$success = $insert->execute();
		unset($insert);
		return ($success);
	}
}






