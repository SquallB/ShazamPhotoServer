<?php

include_once('CountryDAO.class.php');

$dao = new CountryDAO();
$countries = $dao->findAll();
foreach($countries as $country) {
	echo $country->getId() . ' : ' . $country->getName() . '<br/>';
}

echo '<br/>';

include_once('LanguageDAO.class.php');

$dao2 = new LanguageDAO();
$languages = $dao2->findAll();
foreach($languages as $language) {
	echo $language->getId() . ' : ' . $language->getName() . ' : ' . $language->getValue() . '<br/>';
}

?>