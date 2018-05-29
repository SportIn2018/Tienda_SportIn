<?php
//Abrir la sesion
session_start();
//Incluir archivo php de conexiòn
include 'conexion.php';
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['marca_id']) {
	header("Location: index.php");
	exit;
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Obtener id de la marca a eliminar
$mid = $_POST['marca_id'];
//Preparar y ejecutar consulta
$query = ("DELETE FROM c_marcas WHERE id_marca = $mid");
$process = pg_query($conn, $query);
//Verificar si se ejecuto la consulta
if (!$process) {
	$_SESSION['del_marca'] = 1;
	header("Location: admin_marcas.php");
} else {
	$_SESSION['del_marca'] = 2;
	header("Location: admin_marcas.php");
}
?>