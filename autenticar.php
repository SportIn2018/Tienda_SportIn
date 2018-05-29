<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
session_start();

//Cerrar sesion si se envio esa peticion
if ($_POST['logout']) {
	unset($_SESSION['usr']);
	unset($_SESSION['usr_typ']);
	header("Location: index.php");
	exit;
}

//Evitar acceso si no hubo peticion
if (!$_POST['login']) {
	header("Location: index.php");
	exit;
}

//Sanitizar los formularios (quitar caracteres especiales o no pertenecientes al tipo de campo)
$usuario = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
$pass = md5($_POST['pass']);

//Asignar query a variable
$query = ("SELECT us_password,id_usuario,id_tipo_usuario FROM usuarios WHERE us_login = '$usuario'");

//Ejecutar query llamando la variable de conexiòn a la bd
$process = pg_query($conn, $query);
//Informar si la query se ejecuto o no
if  (!$process) {
	$_SESSION['usr_err']=2;
}
else {
	//Si funciono la query comparar la contraseña
	$result = pg_fetch_result($process, 0);
	$id = pg_fetch_result($process, 1);
	$typ = pg_fetch_result($process, 2);

	if ($result == $pass){
		//Si la contraseña es correcta redirigir a menu de lo contrario a inicio
		$_SESSION['usr']=$id;
		$_SESSION['usr_typ']=$typ;
		header("Location: index.php");
		exit;
	}
	else {
		$_SESSION['usr_err']=1;
		header("Location: login.php");
		exit;
	}
}

//Cerrar la conexion a la bd
pg_close($conn);
?>