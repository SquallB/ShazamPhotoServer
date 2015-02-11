<?php

include_once('class/MonumentDAO.class.php');
$dao = new MonumentDAO();
$monuments = $dao->findAll();

?>

<!DOCTYPE html>
<html>
<head>
	<title>Gestion des monuments</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
</head>
<body>
	<h1>Gestion des monuments</h1>

	<table>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Année de construction</th>
			<th>Localisation</th>
			<th>Adresse</th>
			<th>Ville</th>
			<th>Pays</th>
		</tr>

		<?php

		foreach($monuments as $monument) {
			echo '<tr>
					<td>' . $monument->getId() . '</td>
					<td>';
					if(sizeof($monument->getCharacteristics()) > 0) {
						echo $monument->getCharacteristics()[0]->getName();
					}
			echo '</td>
				  <td>' . $monument->getYear() . '</td>
				  <td>' . $monument->getLocalization()->getLatitude() . ', ' . $monument->getLocalization()->getLongitude() . '</td>
				  <td>' . $monument->getAddress()->getNumber() . ' ' . $monument->getAddress()->getStreet() . '</td>
				  <td>' . $monument->getAddress()->getCity()->getName() . '</td>
				  <td>' . $monument->getAddress()->getCity()->getCountry()->getName() . '</td>
				 </tr>';
		}

		?>
	</table>	
</body>
</html>