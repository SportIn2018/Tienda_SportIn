<?php
//Abrir la sesion
session_start();
//Evita el acceso a esta pagina sin autentificarse y a los compradores
if (!$_SESSION['usr'] OR $_SESSION['usr_typ'] == 3) {
  header("Location: index.php");
  exit;
}
//Incluir la conexion a la BD
include 'conexion.php';
//Conectar a la BD
$conn = conectar();

//Revisar si no hay peticion
if (!$_POST['categoria']) {
  header("Location: index.php");
  exit;
}

//Crear y sanitizar variables
$nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);

//Preparar query minima para agregar categoria
$query = ("INSERT INTO c_categorias(categ_descripcion) VALUES('$nombre')");

//Ejecutar query llamando la variable de conexiòn a la bd
$process = pg_query($conn, $query);

//Cerrar la conexion a la BD
pg_close($conn);

//Verificar si fallo el proceso
if(!$process){
  $_SESSION['categ_msg'] = 1;
  header("Location: agregar_categoria.php");
  exit;
} else{
  $_SESSION['categ_msg'] = 2;
  header("Location: agregar_categoria.php");
  exit;
}
?>