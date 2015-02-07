<?php

	if(isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['value']) && !empty($_POST['value'])) {
		$name = pg_escape_string($_POST['name']);
		$value = pg_escape_string($_POST['value']);
		require_once('class/LanguageDAO.class.php');
		$dao = new LanguageDAO();
		$language = new Language(array('name' => $name, 'value' => $value));
		$dao->save($language);
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Index</title>
</head>
<body>
	<h1>Ajout d'une langue</h1>

	<form method="post">
		<p>
			<label for="name">Nom :</label>
			<input type="text" name="name" id="name" />
		</p>
		<p>
			<label for="value">Valeur :</label>
			<input type="text" name="value" id="value" />
		</p>
		<p>
			<input type="submit" />
		</p>
	</form>
</body>
</html>