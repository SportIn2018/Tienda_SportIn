<?php
//Abrir la sesion
session_start();
//Incluir archivo php de conexiòn
include 'conexion.php';
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['categ_id']) {
	header("Location: index.php");
	exit;
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
//Obtener id de la categoria a eliminar
$cid = $_POST['categ_id'];
//Preparar y ejecutar consulta
$query = ("DELETE FROM c_categorias WHERE id_categoria = $cid");
$process = pg_query($conn, $query);
//Verificar si se ejecuto la consulta
if (!$process) {
	$_SESSION['del_categ'] = 1;
	header("Location: admin_categorias.php");
} else {
	$_SESSION['del_categ'] = 2;
	header("Location: admin_categorias.php");
}
?>