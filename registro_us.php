<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['usuario']) {
	$out = 1;
}
if (!$_POST['act_usuario']) {
	$out = 1;
}
if ($out) {
	header("Location: index.php");
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Sanitizar los formularios (quitar caracteres especiales o no pertenecientes al tipo de campo)
$nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
$apaterno = filter_var($_POST['apaterno'], FILTER_SANITIZE_STRING);
$amaterno = filter_var($_POST['amaterno'], FILTER_SANITIZE_STRING);
$direccion = filter_var($_POST['direccion'], FILTER_SANITIZE_STRING);
$telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);
$correo = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);

if ($_POST['usuario']) {
	$login = filter_var($_POST['login'], FILTER_SANITIZE_STRING);
	$password = md5($_POST['pass']);
}

//Validar formularios
if ($nombre) {
	if(!preg_match('/^()[A-ZÁÉÍÓÚÜÑa-záéíóúüñ][a-záéíóúüñ]+(\s[A-ZÁÉÍÓÚÜÑ]?[a-záéíóúüñ]+)*$/',$nombre)){
		$_SESSION['reg_err'] = 1;
	}
} else{
	$_SESSION['reg_err'] = 1;
}
if ($apaterno) {
	if (!preg_match('/^()[A-ZÁÉÍÓÚÜÑa-záéíóúüñ][a-záéíóúüñ]+(\s[A-ZÁÉÍÓÚÜÑ]?[a-záéíóúüñ]+)*$/',$apaterno)) {
		$_SESSION['reg_err'] = 1;
	}
} else{
	$_SESSION['reg_err'] = 1;
}
if ($amaterno) {
	if (!preg_match('/^()[A-ZÁÉÍÓÚÜÑa-záéíóúüñ][a-záéíóúüñ]+(\s[A-ZÁÉÍÓÚÜÑ]?[a-záéíóúüñ]+)*$/',$amaterno)) {
		$_SESSION['reg_err'] = 1;
	}
}
if ($telefono) {
	if (!preg_match('/[0-9]{8,12}/',$telefono)) {
		$_SESSION['reg_err'] = 1;
	}
} else{
	$_SESSION['reg_err'] = 1;
	$_SESSION['debug'] = 'G';
}
if ($correo) {
	if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_\-]?(\.?[a-zA-Z0-9_\-])+@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/',$correo)) {
		$_SESSION['reg_err'] = 1;
		$_SESSION['debug'] = 'H';
	}
} else{
	$_SESSION['reg_err'] = 1;
}

if ($_POST['usuario']) {
	//Verificar que usuario no exista
	$query = ("SELECT id_usuario FROM usuarios WHERE us_login = '$login'");
	$process = pg_query($conn, $query);
	$rep = pg_num_rows($process);

	if ($rep > 0) {
		$_SESSION['reg_err'] = 2;
	}
}

//Operacion agregar usuario
if ($_POST['usuario']) {
	//Insertar datos a db si el nombre de usuario no existe aun
	if (!$_SESSION['reg_err']) {
		//Asignar query a variable (tipo de usuario por defecto comprador)
		$query = ("INSERT INTO usuarios (id_tipo_usuario,us_nombre,us_apaterno,us_amaterno,us_direccion,us_correo,us_telefono,us_login,us_password) VALUES (3,'$nombre','$apaterno','$amaterno','$direccion','$correo','$telefono','$login','$password')");
		//Ejecutar query llamando la variable de conexiòn a la bd
		$process = pg_query($conn, $query);
		//Informar si la query se ejecuto o no
		if  (!$process) {
		   $_SESSION['reg_err'] = 3;
			header("Location: registro.php");
		}
		else {
		   $_SESSION['reg_ok'] = 1;
			header("Location: index.php");
		}
	} else {
		header("Location: registro.php");
	}
}

//Operacion agregar usuario
if ($_POST['act_usuario']) {
	//Insertar datos a db si el nombre de usuario no existe aun
	if (!$_SESSION['reg_err']) {
		//Obtener id de usuario actual para las queries
      	$uid = $_SESSION['usr'];
		//Asignar query a variable (tipo de usuario por defecto comprador)
		$query = ("UPDATE usuarios SET us_nombre = '$nombre',us_apaterno = '$apaterno',us_amaterno = '$amaterno',us_direccion = '$direccion',us_correo = '$correo',us_telefono = '$telefono' WHERE id_usuario = $uid");
		//Ejecutar query llamando la variable de conexiòn a la bd
		$process = pg_query($conn, $query);
		//Informar si la query se ejecuto o no
		if  (!$process) {
		   $_SESSION['act_msg'] = 2;
			header("Location: actualizar_usuario.php");
		}
		else {
		   $_SESSION['act_msg'] = 3;
			header("Location: actualizar_usuario.php");
		}
	} else {
		unset($_SESSION['reg_err']);
		$_SESSION['act_msg'] = 1;
		header("Location: actualizar_usuario.php");
	}
}

//Cerrar la conexion a la bd
pg_close($conn);
?>