<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Inicio</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
 <link rel="stylesheet" href="css/beta.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

</head>
<body style="background-color: #417c81">
<?php
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();

//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Queries necesarias para desplegar categorias y los productos mas populares
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");
$q_prod = ("SELECT count(id_producto) AS ref FROM productos");
$q_vendidos = ("SELECT count(DISTINCT(id_producto)) AS ref FROM venta_producto");

//Ejecutar queries llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);
$pop_ctrl = pg_query($conn, $q_vendidos);
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
if  (!$pop_ctrl) {
 echo "Hubo un error al acceder a la base de datos<br>";

} else{
  //Asignar resultado de consulta a variable
  $pop_ctrl = pg_fetch_result($pop_ctrl,0);
}
if  (!$prod_ctrl) {
  echo "Hubo un error al acceder a la base de datos<br>";

} else{
  //Asignar resultado de consulta a variable
  $prod_ctrl = pg_fetch_result($prod_ctrl,0);
}

//Determinar de acuerdo al numero de productos vendidos y numero de productos en general si llenar el carrusel con los mas vendidos, los unicos en caso de ser 3 y si no hay suficientes poner el carrusel vacio
if ($pop_ctrl < 3) {
    if ($prod_ctrl < 3) {
        $pd_img[0] = "img/default.jpg";
        $pd_img[1] = $pd_img[0];
        $pd_img[2] = $pd_img[0];
        $pd_nom[0] = "Productos_insuficientes";
        $pd_nom[1] = $pd_nom[0];
        $pd_nom[2] = $pd_nom[0];
    }
    else{
        $q_carrusel = ("SELECT prod_ruta_img,prod_nombre,id_producto FROM productos LIMIT 3");
        $carr_ctrl = pg_query($conn, $q_carrusel);
        $i = 0;
        while ($p_row = pg_fetch_row($carr_ctrl)) {
            $pd_img[$i] = $p_row[0];
            $pd_nom[$i] = $p_row[1];
            $pd_id[$i] = $p_row[2];
            $i = $i + 1;
        }
    }
} else {
    if ($prod_ctrl < 3) {
        $pd_img[0] = "img/default.jpg";
        $pd_img[1] = $pd_img[0];
        $pd_img[2] = $pd_img[0];
        $pd_nom[0] = "Productos_insuficientes";
        $pd_nom[1] = $pd_nom[0];
        $pd_nom[2] = $pd_nom[0];
    }
    else{
        $q_carrusel = ("SELECT p.prod_ruta_img,p.prod_nombre,p.id_producto,count(v.id_producto) AS total FROM venta_producto v,productos p WHERE p.id_producto=v.id_producto GROUP BY p.id_producto ORDER BY total DESC LIMIT 3");
        $carr_ctrl = pg_query($conn, $q_carrusel);
        $i = 0;
        while ($p_row = pg_fetch_row($carr_ctrl)) {
            $temp = explode("/", basename($p_row[0]));
            $pd_img[$i] = 'files/B-'.$temp[0];
            $pd_nom[$i] = $p_row[1];
            $pd_id[$i] = $p_row[2];
            $i = $i + 1;
        }
    }
}
//Cerrar la conexion a la BD
pg_close($conn);
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
      <div class="row">
        <div class="col text-center text-white">
            <?php
            if ($_SESSION['reg_ok']) {
              echo '<h5>El registro de usuario se completado exitosamente, ahora puede iniciar sesión</h5><br>';
              unset($_SESSION['reg_ok']);
            }
            if ($_SESSION['compra_oke']) {
              echo '<h5>Se compra se ha efectuado exitosamente<br>no se ha podido enviar el correo de confirmación, contactenos para revisar el problema.</h5><br>';
              unset($_SESSION['compra_ok']);
            }
            if ($_SESSION['compra_ok']) {
              echo '<h5>Se compra se ha efectuado exitosamente<br>se le ha enviado un correo confirmando su compra.</h5><br>';
              unset($_SESSION['compra_ok']);
            }
            if ($_SESSION['adm_usr_est'] == 2) {
              echo '<h5>Se ha eliminado su cuenta exitosamente.</h5><br>';
              unset($_SESSION['adm_usr_est']);
            }
            ?>
            <h3>Nuestros productos mas populares</h3>
        </div>
      </div>
    <div class="row-fluid">
      <!--Carrusel empieza aqui-->
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <a href=<?php echo '"producto.php?id='.$pd_id[0].'"'; ?>><img class="d-block w-100" src=<?php echo '"'.$pd_img[0].'"'; ?> alt=<?php echo '"'.$pd_nom[0].'"'; ?> width="350" height="350"></a>
          </div>
          <div class="carousel-item">
            <a href=<?php echo '"producto.php?id='.$pd_id[1].'"'; ?>><img class="d-block w-100" src=<?php echo '"'.$pd_img[1].'"'; ?> alt=<?php echo '"'.$pd_nom[1].'"'; ?> width="350" height="350"></a>
          </div>
          <div class="carousel-item">
            <a href=<?php echo '"producto.php?id='.$pd_id[2].'"'; ?>><img class="d-block w-100" src=<?php echo '"'.$pd_img[2].'"'; ?> alt=<?php echo '"'.$pd_nom[2].'"'; ?> width="350" height="350"></a>
          </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
      <!--Carrusel termina aqui-->
    </div>
  </div>
</div>
</body>
</html>