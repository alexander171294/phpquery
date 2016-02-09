<?php

if(!defined(PHPQUERY_LOADER)) {
	include('../index.html');
	die();
}

$dsn = 'mysql:dbname='.dbData::$db.';host='.dbData::$host;
return new PDO($dsn, dbData::$user, dbData::$pass);