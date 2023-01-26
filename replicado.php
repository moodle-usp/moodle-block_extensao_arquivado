<?php

require_once('../../config.php');

require_once('classes/Sybase.php');

use block_extensao\Sybase;

$cursos = Sybase::fetchAll("SELECT * FROM CURSOCEU WHERE codund=8 AND dtainc LIKE '%2023%'");

echo "<pre>";

var_dump($cursos);
die();