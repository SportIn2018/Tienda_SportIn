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
//Revisar si no hay peticion;
if (!$_POST['producto']) {
  header("Location: index.php");
  exit;
}
//Obtener el id siguiente para el nombre de la imagen
$query = ("SELECT id_producto FROM productos ORDER BY id_producto DESC LIMIT 1");
$process = pg_query($conn, $query);
$productos = pg_fetch_result($process, 0);

//Crear y sanitizar variables
$nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
$categoria = $_POST['categoria'];
$marca = $_POST['marca'];
$talla = $_POST['talla'];
$descripcion = filter_var($_POST['descripcion'], FILTER_SANITIZE_STRING);
$precio = filter_var($_POST['precio'], FILTER_SANITIZE_NUMBER_FLOAT);
$cantidad = filter_var($_POST['cantidad'], FILTER_SANITIZE_NUMBER_INT);

//Comprobar precio y cantidad positivos y mayores a cero
if ($precio <= 0) {
  $_SESSION['prod_msg'] = 1;
  header("Location: agregar_producto.php");
  exit;
}
if ($cantidad <= 0) {
  $_SESSION['prod_msg'] = 2;
  header("Location: agregar_producto.php");
  exit;
}
//Preparar query minima para agregar producto
$query = ("INSERT INTO productos(id_categoria,id_marca,id_talla,prod_nombre,prod_descripcion,prod_precio,prod_inventario) VALUES('$categoria','$marca','$talla','$nombre','$descripcion','$precio','$cantidad')");

//Definir constantes de unidades de datos para el control del tamaño de los archivos a subir
define('KB', 1024);
define('MB', 1048576);
//Variable con los tipos de archivo permitidos
$allowedExts = array("gif", "jpeg", "jpg", "png");
//Variable temporal para separar nombre y extensión del archivo a subir
$temp = explode(".", basename( $_FILES['file']['name']));
$temp = explode(".", basename( $_FILES['file_banner']['name']));
//Variable con la extension del archivo para la validación y el renombramiento del archivo
$extension = end($temp);
if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 5*MB) && in_array($extension, $allowedExts)){
  if(!empty($_FILES['file'])){
    //Definir ruta donde se guardan los archivos subidos
    $ruta = "files/";
    //Renombrar archivo
    $newfilename = "SKU".($productos+1).'.'.$extension;
    //Armar la ruta y nombre de archivo donde se guardara en el servidor
    $ruta = $ruta . $newfilename;
    $query_img = ("INSERT INTO productos(id_categoria,id_marca,id_talla,prod_nombre,prod_descripcion,prod_precio,prod_inventario,prod_ruta_img) VALUES('$categoria','$marca','$talla','$nombre','$descripcion','$precio','$cantidad','$ruta')");
    if(move_uploaded_file($_FILES['file']['tmp_name'], $ruta)){
      //Ejecutar query llamando la variable de conexiòn a la bd
      $process = pg_query($conn, $query_img);
    } else{
      //Ejecutar query llamando la variable de conexiòn a la bd
      $process = pg_query($conn, $query);
      $_SESSION['img_err'] = 1;
    }
  } else{
          $process = pg_query($conn, $query);
  }

} else{
  if(!empty($_FILES['file'])){
    $process = pg_query($conn, $query);
    $_SESSION['img_err'] = 1;
  }
}
//Insercion del banner
if ((($_FILES["file_banner"]["type"] == "image/gif") || ($_FILES["file_banner"]["type"] == "image/jpeg") || ($_FILES["file_banner"]["type"] == "image/jpg") || ($_FILES["file_banner"]["type"] == "image/pjpeg") || ($_FILES["file_banner"]["type"] == "image/x-png") || ($_FILES["file_banner"]["type"] == "image/png")) && ($_FILES["file_banner"]["size"] < 5*MB) && in_array($extension, $allowedExts)){
  if(!empty($_FILES['file_banner'])){
    //Definir ruta donde se guardan los archivos subidos
    $path = "files/";
    //Renombrar archivo
    $newfilename = "B-SKU".($productos+1).'.'.$extension;
    //Armar la ruta y nombre de archivo donde se guardara en el servidor
    $path = $path . $newfilename;
    if(!move_uploaded_file($_FILES['file_banner']['tmp_name'], $path)){
      $_SESSION['img_err'] = 1;
    }
  }
} else{
  if(!empty($_FILES['file_banner'])){
    $_SESSION['img_err'] = 1;
  }
}

//Cerrar la conexion a la BD
pg_close($conn);

//Verificar si fallo el proceso
if(!$process){
  $_SESSION['prod_msg'] = 3;
  unset($_SESSION['img_err']);
  header("Location: agregar_producto.php");
  exit;
} else{
  $_SESSION['prod_msg'] = 4;
  header("Location: agregar_producto.php");
  exit;
}
?>