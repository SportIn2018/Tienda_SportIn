<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['act_pass']) {
	header("Location: index.php");
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Hashear contraseñas
$old = md5($_POST['pass']);
$new = md5($_POST['new_pass']);

//Obtener id de usuario actual para las queries
$uid = $_SESSION['usr'];

//Veridicar contraseña vieja
$query = ("SELECT us_password FROM usuarios WHERE id_usuario = $uid");
$process = pg_query($conn, $query);
$pass = pg_fetch_result($process, 0);

//Realizar actualizacion de contraseña si la contraseña vieja es correcta
if ($old == $pass) {
	$query = ("UPDATE usuarios SET us_password = '$new' WHERE id_usuario = $uid AND us_password = '$old'");
	$process = pg_query($conn, $query);

	//Verificar la operacion
	if (!$process) {
		$_SESSION['contra'] = 1;
		header("Location: cambio_contra.php");
	} else {
		$_SESSION['contra'] = 2;
		header("Location: cambio_contra.php");
	}
} else {
	$_SESSION['contra'] = 3;
	header("Location: cambio_contra.php");
}
?>