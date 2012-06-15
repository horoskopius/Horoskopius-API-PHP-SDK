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
$horoskopius->setCategory(2); //ukoliko zelite poslovni promenite kategoriju u broj 3
$horoskopius->setHoroscopeType(1);
$horoskopius->setResponseType("xml");
$horoskopius->setAlphabet(1); //nova funkcija 1 = latinica, 2 = ćirilica
$horoskopius->getResponse();
?>
</body>
</html>