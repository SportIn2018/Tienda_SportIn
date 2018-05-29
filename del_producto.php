<?php
//Abrir la sesion
session_start();
//Incluir archivo php de conexiòn
include 'conexion.php';
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['prod_id']) {
	header("Location: index.php");
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Obtener id de producto a eliminar
$pid = $_POST['prod_id'];
//Preparar y ejecutar consulta
$query = ("DELETE FROM productos WHERE id_producto = $pid");
$process = pg_query($conn, $query);
//Verificar si se ejecuto la consulta
if (!$process) {
	$_SESSION['adm_prod_est'] = 1;
	header("Location: admin_productos.php");
} else {
	$_SESSION['adm_prod_est'] = 2;
	header("Location: admin_productos.php");
}
?>