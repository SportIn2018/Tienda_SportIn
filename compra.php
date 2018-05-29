<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Carrito de compras</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
 <link rel="stylesheet" href="css/beta.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

</head>
<body style="background-color: #417c81">
<?php
//Abrir la session
session_start();
//Solo permitir acceso con peticion del carrito de compras o resultados de una venta solicitada
if (!$_POST['compra_accion'] AND !$_SESSION['compra_res']) {
  header("Location: index.php");
  exit;
}

//Incluir archivo php de conexiòn
include 'conexion.php';

//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Queries necesarias para desplegar categorias y los productos mas populares
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");
$q_prod = ("SELECT count(id_producto) AS ref FROM productos");

//Ejecutar queries llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);
$prod_ctrl = pg_query($conn, $q_prod);

//Informar si las queries se ejecutaron o no
if  (!$process) {
  echo "Hubo un error al acceder a la base de datos<br>";

}
if  (!$marca) {
  echo "Hubo un error al acceder a la base de datos<br>";

}
if  (!$talla) {
  echo "Hubo un error al acceder a la base de datos<br>";

}
if  (!$prod_ctrl) {
  echo "Hubo un error al acceder a la base de datos<br>";

} else{
  //Asignar resultado de consulta a variable
  $prod_ctrl = pg_fetch_result($prod_ctrl,0);
}


?>

<!--Barra superior-->
<div class="row" style="background-color: #0e484e">
  <div class="col text-right"><form action="catalogo.php"><input type="text" name="buscar"><input type="submit" value="Buscar"></form></div>
  <div class="col text-right">
  <?php
    if (!$_SESSION['usr']) {
  ?>
  <a href="login.php"><img src="img/login.png" height="25" width="25"> Iniciar Sesion</a></div>
  <?php
    } else {
  ?>
  <form action="autenticar.php" method="post" name="cerrar_sesion">
    <input type="text" style="display:none" name="logout" value="1">
    <a href="carrito.php"><img src="img/cart.png" height="30" width="30"> Tus Compras</a>
    <a href="home_usuario.php"><img src="img/user.png" height="25" width="25"> Cuenta</a>
    <a href="#" onclick="document.cerrar_sesion.submit();"><img src="img/logout.png" height="25" width="25"> Cerrar Sesion</a></div>
  </form>
  <?php
    }
  ?>
</div>

<div class="row barra_fix">
  <!--Barra lateral de categorias-->
  <div class="col-3 text-white text-center" style="background-color: #0e484e">
    <div><a href="index.php"><img src="img/logo.png" alt="logo" height="200" width="200"></a></div>
    <a href="catalogo.php">Catalogo de productos</a><br><br><h4>Categorias</h4>
    <?php 
      while ($row = pg_fetch_row($process)) {
      echo '<a href="catalogo.php?categoria='.$row[0].'">'.$row[1].'</a><br>';
    }?>
    <br><h4>Marcas</h4>
    <?php 
      while ($row = pg_fetch_row($marca)) {
      echo '<a href="catalogo.php?marca='.$row[0].'">'.$row[1].'</a><br>';
    }?>
    <br><h4>Tallas</h4>
    <?php 
      while ($row = pg_fetch_row($talla)) {
      echo '<a href="catalogo.php?talla='.$row[0].'">'.$row[1].'</a><br>';
    }?> 
  </div>
  <!--Contenido-->
  <div class="col">
    <div class="row-fluid" style="background-color: #FFFFFF">
      <div class="col" style="margin: 5px">
      <?php
      //Variable para recorrer el carrito
      $new_art = count($_SESSION['car_id_productos']);
      //Verificar que haya productos suficientes en inventario para autorizar la compra
      for ($i=0; $i < $new_art; $i++){
        //Preparar variables y consultas
        $pid = $_SESSION['car_id_productos'][$i];
        $query = ("SELECT prod_inventario FROM productos WHERE id_producto = $pid");
        $process = pg_query($conn, $query);
        //Informar si la query se ejecuto o no
        if  (!$process) {
          echo "Hubo un error al acceder a la base de datos<br>";
        }
        $res = pg_fetch_result($process, 0);
        //Si no hay productos disponibles activar variable de control
        if ($_SESSION['car_cantidad_productos'][$i]>$res) {
          $insuficientes = 1;
        }
      }
      if ($insuficientes) {
        $_SESSION['car_result'] = 5;
        header("Location: carrito.php");
        exit;
      }
      if ($_POST['compra_accion'] AND !$insuficientes) {
        $query = ("SELECT id_tipo_pago,tipo_pago_descripcion FROM c_tipos_pago");
        $process = pg_query($conn, $query);
        //Informar si la query se ejecuto o no
        if  (!$process) {
          echo "Hubo un error al acceder a la base de datos<br>";
        }
        echo '<h4>Confirmar compra</h4>Por favor confirme su desea realizar su compra por un total de $'.number_format($_POST['compra_valor'],2).'<br><br><h5>Elija un tipo de pago y de clic en comprar</h5>';
        echo '<form action="compra_procesos.php" method="post">Tipo de pago: <select name="tipo_pago" required>';
        while ($row = pg_fetch_row($process)) {
          echo '<option value="'.$row[0].'">'.$row[1].'</option><br>';
        }
        echo '<input type="submit" name="comprar" value="Comprar"></form><br>';
        echo '¿Aun no esta listo para comprar?<br><a href="carrito.php">Regresar al carrito</a>';
      }
      //Cerrar la conexion a la BD
      pg_close($conn);
      ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>