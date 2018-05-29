<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Administrar descuentos</title>
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
//Evita el acceso a esta pagina sin autentificarse o no contar con permisos de administrador
if (!$_SESSION['usr'] OR $_SESSION['usr'] != 1) {
  header("Location: index.php");
}

//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Queries necesarias para desplegar categorias y los productos mas populares
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");

//Queries para la asignacion de descuentos
$q_desc = ("SELECT id_descuento,desc_porcentaje FROM descuentos");

//Ejecutar queries llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);

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
          //Ejecutar queries para los formularios
          $categ = pg_query($conn, $q_categ);
          $marca = pg_query($conn, $q_marca);
          $desc = pg_query($conn, $q_desc);
          $desc2 = pg_query($conn, $q_desc);
          //Cerrar la conexion a la BD
          pg_close($conn);
          //Comprobar que las queries se hayan ejecutado
          if (!$categ) {
            echo '<div class="row" style="margin: 5px">Hubo un error al acceder a la base de datos</div>';
          }
          if (!$marca) {
            echo '<div class="row" style="margin: 5px">Hubo un error al acceder a la base de datos</div>';
          }
          if (!$desc) {
            echo '<div class="row" style="margin: 5px">Hubo un error al acceder a la base de datos</div>';
          }
          if (!$desc2) {
            echo '<div class="row" style="margin: 5px">Hubo un error al acceder a la base de datos</div>';
          }
          //Mensajes sobre estado de operaciones solicitadas
          if ($_SESSION['act_desc']) {
            switch ($_SESSION['act_desc']) {
              case 1:
                echo 'No pudo realizarse la operación, hubo un problema al acceder a la base de datos';
                unset($_SESSION['act_desc']);
                break;
              case 2:
                echo 'La operación se ha realizado exitosamente';
                unset($_SESSION['act_desc']);
                break;
            }
          }
          echo '<h4>Administrar descuentos</h4>';
          //Revisar si hay categorias
          $hay_categ = pg_num_rows($categ);
          //Revisar si hay marcas
          $hay_marca = pg_num_rows($marca);
          //Ver condiciones del proceso
          if ($hay_categ == 0 AND $hay_categ == 0) {
            echo '<h5>Faltan categorias o marcas<br>Para asignar descuentos debe haber al menos una marca y categoria registradas</h5>';
          } else {
            //Asignar descuento por categoria
            echo '<h4>Asignar descuento por categoria</h4>';
            //Generar formulario de descuento por categoria
            ?>
            <form action="act_desc.php" method="post">
              Categoria: <select name="categoria" required>
              <?php
                while ($row = pg_fetch_row($categ)) {
                  echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                }
              ?>
              </select><br><br>
              Descuento: <select name="descuento" required>
              <?php
                while ($row = pg_fetch_row($desc)) {
                  echo '<option value="'.$row[0].'">'.($row[1]*100).'%</option>';
                }
              ?>
              </select><br><br>
              <input type="submit" value="Enviar">
            </form><br>
            <?php
            //Asignar descuento por marca
            echo '<h4>Asignar descuento por marca</h4>';
            //Generar formulario de descuento por marca
            ?>
            <form action="act_desc.php" method="post">
              Categoria: <select name="marca" required>
              <?php
                while ($row = pg_fetch_row($marca)) {
                  echo '<option value="'.$row[0].'">'.$row[1].'</option>';
                }
              ?>
              </select><br><br>
              Descuento: <select name="descuento" required>
              <?php
                while ($row = pg_fetch_row($desc2)) {
                  echo '<option value="'.$row[0].'">'.($row[1]*100).'%</option>';
                }
              ?>
              </select><br><br>
              <input type="submit" value="Enviar">
            </form>
            <?php
          }
            ?>
        <br><br>
      </div>
    </div>
  </div>
</div>
</body>
</html>