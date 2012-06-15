<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dnevni ljubavni horoskop primer</title>
<!-- link ka horoskopius CSS fajlu -->
<link rel="stylesheet" href="horoskopiuscss/horstyle.css" />
</head>

<body>
<?php
include('../lib/horoskopiusphpsdk.php');
$horoskopius = new HoroskopiusSDK();
$horoskopius->setPrivateKey("ovde_ide_vas_private_key");
$horoskopius->setPublicKey("ovde_ide_vas_public_key");
$horoskopius->setHoroscope(1);
$horoskopius->setCategory(1);
$horoskopius->setHoroscopeType(1);
$horoskopius->setResponseType("xml");
$horoskopius->setSpeedUp(2);//ne preporucujemo - ovo je protiv kreiranja lokalnog cache-a sto znatno usporava sdk jer svaki poziv ide prema našem serveru
$horoskopius->setCache(2);//ne preporucujemo - ovo je za override našeg serverskog cache-a
$horoskopius->setAlphabet(1); //nova funkcija 1 = latinica, 2 = ćirilica
$horoskopius->getResponse();
?>
</body>
</html>