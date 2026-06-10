<?php
session_start();

// Alle sessiegegevens verwijderen bij uitloggen.
$_SESSION = [];
session_destroy();

header('Location: ../../views/login.php');
exit;
