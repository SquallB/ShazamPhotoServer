<?php

include_once('class/MonumentDAO.class.php');
$dao = new MonumentDAO();

if(isset($_GET['action']) && isset($_GET['id'])) {
	if($_GET['action'] === 'delete') {
		$id = pg_escape_string($_GET['id']);
		$monument = $dao->find($id);
		if($monument->getId() !== null) {
			$dao->delete($monument);
		}
	}
}

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

	<p>
		<a href="add_monument.php">Ajouter un monument</a>
	</p>

	<table>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Année de construction</th>
			<th>Localisation</th>
			<th>Adresse</th>
			<th>Ville</th>
			<th>Pays</th>
			<th>Supprimer</th>
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
				  <td><a href="?action=delete&id=' . $monument->getId() . '">X</td>
				 </tr>';
		}

		?>
	</table>	
</body>
</html>