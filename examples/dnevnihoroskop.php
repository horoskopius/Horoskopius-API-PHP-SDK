<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dnevni horoskop primer</title>
<style type="text/css">
#horoskopius {font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.5}
#horoskopius h2 {color: #39C; text-transform:uppercase}
#horoskopius h3 {color: #C30}
#horoskopius ul { list-style-type:none; margin:0; padding:0;}
#horoskopius ul li { padding-bottom:10px; border-bottom:1px solid #ddd}
#horoskopius ul li:last-child { border-bottom:none}
.horoskopius-link { color: #666; font-size:90%;padding-top:20px;}
</style>

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
$horoskopius->getResponse();
?>
</body>
</html>