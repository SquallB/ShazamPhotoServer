<?php
	$start = microtime(true);

	$countries = '';
	include_once('class/CountryDAO.class.php');
	$countryDAO = new CountryDAO();
	$countriesArray = $countryDAO->findAll();
	foreach($countriesArray as $country) {
		$countries .= '<option value="'. $country->getId() .'">' . $country->getName() . '</option>';
	}

	$languages = '';
	include_once('class/LanguageDAO.class.php');
	$languageDAO = new LanguageDAO($countryDAO->getConnection());
	$languagesArray = $languageDAO->findAll();
	foreach($languagesArray as $language) {
		$languages .= '<option value="'. $language->getId() .'">' . $language->getName() . '</option>';
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
		$countryId = pg_escape_string($_POST['country']);
		$languageId = pg_escape_string($_POST['language']);

		include_once('class/MonumentDAO.class.php');
		$localization = new Localization(array('latitude' => $latitude, 'longitude' => $longitude));
		$language = $languageDAO->find($languageId);
		$country = $countryDAO->find($countryId);
		$city = new City(array('name' => $city, 'country' => $country));
		$address = new Address(array('number' => $number, 'street' => $street, 'city' => $city));
		$monument = new Monument(array('photopath' => $photoPath, 'year' => $year, 'nbvisitors' => 0, 'nblikes' => 0, 'address' => $address, 'localization' => $localization));
		$characteristic = new MonumentCharacteristics(array('name' => $name, 'description' => $description, 'language' => $language));
		$monument->setCharacteristics(array($characteristic));
		$monumentDAO = new MonumentDAO($countryDAO->getConnection());
		$monumentDAO->save($monument);
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Ajout d'un monument</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
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
			<label for="year">Année de construction :</label>
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
			<label for="number">Numéro :</label>
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