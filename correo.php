<?php
//Utiliza el paquete de debian php-mail y php-mail-mime (Pear Mail)
require_once "Mail.php";
//Abrir la session
session_start();
//Restringir acceso solo con peticion
if (!$_SESSION['correo_ok']) {
	header("Location: index.php");
    exit;
}
//Incluir archivo php de conexiòn
include 'conexion.php';
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Informacion del correo de gmail que enviara las confirmaciones
$mail_tienda = "";
$pass_mail_tienda = "";

//Obtener nombre y email del usuario para enviar confirmacion de su compra
$uid = $_SESSION['usr'];
$query = ("SELECT us_correo,us_nombre,us_apaterno FROM usuarios WHERE id_usuario = $uid");
$proceso = pg_query($conn, $query);
if (!$proceso) {
  echo "Hubo un error al acceder a la base de datos<br>";
}
$correo = pg_fetch_result($proceso, 0);
$nombre = pg_fetch_result($proceso, 1);
$apellido = pg_fetch_result($proceso, 2);
//Calcular fecha de entrega (10 dias a partir del dia que se efectua la compra)
$entrega = date('d-m-y',strtotime("+10 day"));
//Armar el correo (version texto plano)
$from = "SportIn <".$mail_tienda.">";
$to = $nombre.'_'.$apellido.'<'.$correo.'>';
$subject = "Compra realizada";
$body = "Felicidades,\nSu compra se ha realizado exitosamente\nSu pedido llegara aproximadamente el dia ".$entrega.".\nMuchas gracias por su compra";
$host = "ssl://smtp.gmail.com";
$port = "465";
$username = $mail_tienda;
$password = $pass_mail_tienda;
$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'port' => $port,
    'auth' => true,
    'username' => $username,
    'password' => $password));
$mail = $smtp->send($to, $headers, $body);
if (PEAR::isError($mail)) {
	unset($_SESSION['correo_ok']);
	$_SESSION['compra_oke'] = 1;
  	header("Location: index.php");
  	exit;
 } else {
 	unset($_SESSION['correo_ok']);
 	$_SESSION['compra_ok'] = 1;
  	header("Location: index.php");
  	exit;
 }
?>