<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();
//Si no se llego a esta pagina a traves de una peticion mandar a inicio
if (!$_POST['descuento']) {
	header("Location: index.php");
	exit;
}

//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Asignacion de descuento por categoria
if ($_POST['categoria']) {
	//Asignacion de variables para la query
	$did = $_POST['descuento'];
	$cid = $_POST['categoria'];
	//Creacion de query
	$query = ("UPDATE productos SET id_descuento = $did WHERE id_categoria = $cid");
	//Ejecucion de query
	$process = pg_query($conn, $query);
}

//Asignacion de descuento por marca
if ($_POST['marca']) {
	//Asignacion de variables para la query
	$did = $_POST['descuento'];
	$mid = $_POST['marca'];
	//Creacion de query
	$query = ("UPDATE productos SET id_descuento = $did WHERE id_marca = $mid");
	//Ejecucion de query
	$process = pg_query($conn, $query);
}

//Verificar la operacion
if (!$process) {
	$_SESSION['act_desc'] = 1;
	header("Location: admin_descuentos.php");
	exit;
} else {
	$_SESSION['act_desc'] = 2;
	header("Location: admin_descuentos.php");
	exit;
}
//Cerrar la conexion a la BD
pg_close($conn);
?>