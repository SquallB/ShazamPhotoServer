<?php

	if(isset($_POST['name']) && !empty($_POST['name'])) {
		$name = pg_escape_string($_POST['name']);
		require_once('class/CountryDAO.class.php');
		$dao = new CountryDAO();
		$country = new Country(array('name' => $name));
		$dao->save($country);
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Ajout d'un pays</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
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