<?php
	$start = microtime(true);

	include('pgconf.php');
	$db = pg_connect($pgconf);

	$countries = '';
	include_once('class/CountryDAO.class.php');
	$countryDAO = new CountryDAO();
	$countriesArray = $countryDAO->findAll();
	foreach($countriesArray as $country) {
		$countries .= '<option value="'. $country->getName() .'">' . $country->getName() . '</option>';
	}

	$languages = '';
	include_once('class/LanguageDAO.class.php');
	$languageDAO = new LanguageDAO($countryDAO->getConnection());
	$languagesArray = $languageDAO->findAll();
	foreach($languagesArray as $language) {
		$languages .= '<option value="'. $language->getValue() .'">' . $language->getName() . '</option>';
	}

	if(isset($_POST['name']) && !empty($_POST['name'])) {// && isset($_POST['photoPath']) && !empty($_POST['photoPath']) && isset($_POST['year']) && !empty($_POST['year']) && isset($_POST['latitude']) && !empty($_POST['latitude']) && isset($_POST['longitude']) && !empty($_POST['longitude']) && isset($_POST['description']) && !empty($_POST['description']) && isset($_POST['number']) && !empty($_POST['number']) && isset($_POST['street']) && !empty($_POST['steet']) && isset($_POST['city']) && !empty($_POST['city'])) {
		$name = pg_escape_string($_POST['name']);
		$photoPath = pg_escape_string($_POST['photoPath']);
		$year = pg_escape_string($_POST['year']);
		$latitude = pg_escape_string($_POST['latitude']);
		$longitude = pg_escape_string($_POST['longitude']);
		$description = pg_escape_string($_POST['description']);
		$number = pg_escape_string($_POST['number']);
		$street = pg_escape_string($_POST['street']);
		$city = pg_escape_string($_POST['city']);
		$country = pg_escape_string($_POST['country']);
		$language = pg_escape_string($_POST['language']);

		pg_prepare($db, 'query_add_localization', 'INSERT INTO localization (latitude, longitude) VALUES ($1, $2)');
		pg_execute($db, 'query_add_localization', array($latitude, $longitude));
		pg_prepare($db, 'query_get_localization', 'SELECT id FROM localization WHERE latitude = $1 AND longitude = $2');
		$result = pg_execute($db, 'query_get_localization', array($latitude, $longitude));
		if($result && $row = pg_fetch_row($result)) {
			$idLocalization = $row[0];
		}

		pg_prepare($db, 'query_get_language', 'SELECT id FROM language WHERE value = $1');
		$result = pg_execute($db, 'query_get_language', array($language));
		if($result && $row = pg_fetch_row($result)) {
			$idLanguage = $row[0];
		}

		pg_prepare($db, 'query_get_country', 'SELECT id FROM country WHERE name = $1');
		$result = pg_execute($db, 'query_get_country', array($country));
		if($result && $row = pg_fetch_row($result)) {
			$idCountry = $row[0];
		}

		pg_prepare($db, 'query_get_city', 'SELECT id FROM city WHERE name = $1 AND country_id = $2');
		$result = pg_execute($db, 'query_get_city', array($city, $idCountry));
		if($result && $row = pg_fetch_row($result)) {
			$idCity = $row[0];
		}
		else {
			pg_prepare($db, 'query_add_city', 'INSERT INTO city (name, country_id) VALUES ($1, $2)');
			pg_execute($db, 'query_add_city', array($city, $idCountry));
			$result = pg_execute($db, 'query_get_city', array($city, $idCountry));
			if($result && $row = pg_fetch_row($result)) {
				$idCity = $row[0];
			}
		}

		pg_prepare($db, 'query_add_address', 'INSERT INTO address (number, street, city_id) VALUES ($1, $2, $3)');
		pg_execute($db, 'query_add_address', array($number, $street, $idCity));
		pg_prepare($db, 'query_get_address', 'SELECT id FROM address WHERE number = $1 AND street = $2 AND city_id = $3');
		$result = pg_execute($db, 'query_get_address', array($number, $street, $idCity));
		if($result && $row = pg_fetch_row($result)) {
			$idAddress = $row[0];
		}

		pg_prepare($db, 'query_add_monument', 'INSERT INTO monument (photoPath, year, nbVisitors, nbLikes, localization_id, address_id) VALUES ($1, $2, 0, 0, $3, $4)');
		pg_execute($db, 'query_add_monument', array($photoPath, $year, $idLocalization, $idAddress));
		pg_prepare($db, 'query_get_monument', 'SELECT id FROM monument WHERE photoPath = $1 AND year = $2 AND localization_id = $3 AND address_id = $4');
		$result = pg_execute($db, 'query_get_monument', array($photoPath, $year, $idLocalization, $idAddress));
		if($result && $row = pg_fetch_row($result)) {
			$idMonument = $row[0];
		}

		pg_prepare($db, 'query_add_monument_characteristic', 'INSERT INTO monument_characteristics (name, description, language_id, monument_id) VALUES ($1, $2, $3, $4)');
		pg_execute($db, 'query_add_monument_characteristic', array($name, $description, $idLanguage, $idMonument));
	}

	pg_close($db);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Index</title>
</head>
<body>
	<h1>Ajout d'un monument</h1>

	<form method="post">
		<p>
			<label for="name">Nom :</label>
			<input type="text" name="name" id="name" />
		</p>
		<p>
			<label for="photoPath">Chemin de la photo :</label>
			<input type="text" name="photoPath" id="photoPath" />
		</p>
		<p>
			<label for="year">Ann&eacute;e de construction :</label>
			<input type="text" name="year" id="year" />
		</p>
		<p>
			<label for="latitude">Latitude :</label>
			<input type="text" name="latitude" id="latitude" />
		</p>
		<p>
			<label for="longitude">Longitude :</label>
			<input type="text" name="longitude" id="longitude" />
		</p>
		<p>
			<label for="description">Description :</label>
			<input type="text" name="description" id="description" />
		</p>
		<p>
			<label for="number">Num&eacute;ro :</label>
			<input type="text" name="number" id="number" />
		</p>
		<p>
			<label for="street">Rue :</label>
			<input type="text" name="street" id="street" />
		</p>
		<p>
			<label for="city">Ville :</label>
			<input type="text" name="city" id="city" />
		</p>
		<p>
			<label for="country">Pays :</label>
			<select id="country" name="country">
				<?php echo $countries; ?>
			</select>
			<a href="add_country.php">Ajouter un pays</a>
		</p>
		<p>
			<label for="language">Langue :</label>
			<select id="language" name="language">
				<?php echo $languages; ?>
			</select>
			<a href="add_language.php">Ajouter une langue</a>
		</p>
		<p>
			<input type="submit" />
		</p>

		<?php
			$end = microtime(true);
			$diff = $end - $start;

			echo '<p>' . $diff . '</p>';
		?>
	</form>
</body>
</html>