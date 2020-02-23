<?php

if (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
	$id = $_POST['id'];
	
}
$id = 70;
	$mysqli = new Mysqli('localhost', 'root' ,'', 'test_hobby');

	$query = $mysqli->query("
			SELECT description
			FROM posts 
			WHERE id = ".$id
			);

	while($row = $query->fetch_assoc()){
		$description = strip_tags(htmlspecialchars_decode($row['description']));
	}
var_dump($description);

    $des = array(
		'description' => $description,
    ); 

    echo json_encode($des); 


