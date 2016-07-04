<?php

if(!defined('PHPQUERY_LOADER')) {
	include('../index.html');
	die();
}

if(!file_exists('settings.php'))
{
	return null;
} else {
	$dsn = 'mysql:dbname='.dbData::$db.';host='.dbData::$host;
	return new PDO($dsn, dbData::$user, dbData::$pass);
}