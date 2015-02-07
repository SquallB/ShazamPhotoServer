<?php

	if(isset($_POST['name']) && !empty($_POST['name'])) {
		$name = pg_escape_string($_POST['name']);
		include('pgconf.php');
		$db = pg_connect($pgconf);
		pg_prepare($db, 'query_add_language', 'INSERT INTO country (name) VALUES ($1)');
		pg_execute($db, 'query_add_language', array($name));
		pg_close($db);
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Index</title>
</head>
<body>
	<h1>Ajout d'une pays</h1>

	<form method="post">
		<p>
			<label for="name">Nom :</label>
			<input type="text" name="name" id="name" />
		</p>
		<p>
			<input type="submit" />
		</p>
	</form>
</body>
</html>