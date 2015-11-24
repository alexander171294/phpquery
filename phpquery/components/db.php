<?php

$dsn = 'mysql:dbname='.dbData::$db.';host='.dbData::$host;
return new PDO($dsn, dbData::$user, dbData::$pass);