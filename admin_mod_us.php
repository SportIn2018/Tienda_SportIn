<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();
//Evita acceso sin peticion
if (!$_POST['adm_usr_op']) {
	header("Location: index.php");
	exit;
}

//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Tares de acuerdo a las opciones seleccionadas
switch ($_POST['adm_usr_op']) {
	//Eliminar usuario
	case 0:
		$query = ("DELETE FROM usuarios WHERE id_usuario = '$_POST[adm_usr_id]'");
		$process = pg_query($conn, $query);
		if (!$process) {
			$_SESSION['adm_usr_est'] = 1;
			header("Location: admin_usuarios.php");
			exit;
		}
		$_SESSION['adm_usr_est'] = 2;
		header("Location: admin_usuarios.php");
		break;
	//Cambiar permisos usuario a administrador
	case 1:
		$query = ("UPDATE usuarios SET id_tipo_usuario = 1 WHERE id_usuario = '$_POST[adm_usr_id]'");
		$process = pg_query($conn, $query);
		if (!$process) {
			$_SESSION['adm_usr_est'] = 1;
			header("Location: admin_usuarios.php");
			exit;
		}
		$_SESSION['adm_usr_est'] = 2;
		header("Location: admin_usuarios.php");
		break;
	//Cambiar permisos usuario a vendedor
	case 2:
		$query = ("UPDATE usuarios SET id_tipo_usuario = 2 WHERE id_usuario = '$_POST[adm_usr_id]'");
		$process = pg_query($conn, $query);
		if (!$process) {
			$_SESSION['adm_usr_est'] = 1;
			header("Location: admin_usuarios.php");
			exit;
		}
		$_SESSION['adm_usr_est'] = 2;
		header("Location: admin_usuarios.php");
		break;
	//Cambiar permisos usuario a comprador
	case 3:
		$query = ("UPDATE usuarios SET id_tipo_usuario = 3 WHERE id_usuario = '$_POST[adm_usr_id]'");
		$process = pg_query($conn, $query);
		if (!$process) {
			$_SESSION['adm_usr_est'] = 1;
			header("Location: admin_usuarios.php");
			exit;
		}
		$_SESSION['adm_usr_est'] = 2;
		header("Location: admin_usuarios.php");
		break;
	//Eliminar usuario activo
	case 4:
		$query = ("DELETE FROM usuarios WHERE id_usuario = '$_POST[adm_usr_id]'");
		$process = pg_query($conn, $query);
		if (!$process) {
			$_SESSION['adm_usr_est'] = 1;
			header("Location: home_usuario.php");
			exit;
		}
		$_SESSION['adm_usr_est'] = 2;
		unset($_SESSION['usr']);
		unset($_SESSION['usr_typ']);
		header("Location: index.php");
		exit;
		break;
}
?>