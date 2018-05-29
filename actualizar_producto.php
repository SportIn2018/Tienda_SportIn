<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Agregar producto</title>
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
//Evita el acceso a esta pagina sin autentificarse y a los compradores
if (!$_SESSION['usr'] OR $_SESSION['usr_typ'] == 3) {
  header("Location: index.php");
}
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();

//Queries necesarias para desplegar categorias
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");

//Ejecutar queries llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);

if  (!$process) {
  echo "Hubo un error al acceder a la base de datos<br>";

}

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
  <div class="col" style="background-color: #FFFFFF">
      <?php
      if ($_SESSION['prod_msg']) {
          switch ($_SESSION['prod_msg']) {
            case 1:
              echo '<div class="row" style="margin: 2px">No se pudo actualizar el producto: El precio tiene que ser positivo y mayor a cero</div>';
              unset($_SESSION['prod_msg']);
              break;
            case 2:
              echo '<div class="row" style="margin: 2px">No se pudo actualizar el producto: La cantidad inicial tiene que ser positiva y mayor a cero</div>';
              unset($_SESSION['prod_msg']);
              break;
            case 3:
              echo '<div class="row" style="margin: 2px"><div class="row" style="margin: 2px">No se pudo actualizar el producto: Hubo un error con la base de datos, intentelo mas tarde</div>';
              unset($_SESSION['prod_msg']);
              break;
            case 4:
              echo '<div class="row" style="margin: 2px">El producto se ha actualizado exitosamente</div>';
              unset($_SESSION['prod_msg']);
              break;
          }
        }
        if ($_SESSION['img_err']) {
          echo '<div class="row" style="margin: 2px">No se puderon agregar las imagenes</div>';
          unset($_SESSION['img_err']);
        }
      ?>
    <div class="row" style="margin: 2px">
      <h2>Actualizar producto</h2>
      <?php
        $conn = conectar();
        $process = pg_query($conn, $q_categ);
        $marca = pg_query($conn, $q_marca);
        $talla = pg_query($conn, $q_talla);

        //Obtener id de producto para las queries
        if ($_SESSION['pid']) {
          $pid = $_SESSION['pid'];
          unset($_SESSION['pid']);
        } else {
          $pid = $_POST['prod_id'];
        }
        //Obtener datos actuales del producto
        $query = ("SELECT prod_nombre,id_categoria,id_marca,id_talla,prod_descripcion,prod_precio,prod_inventario,prod_ruta_img FROM productos WHERE id_producto = $pid");
        //Ejecutar consulta
        $prod_act = pg_query($conn, $query);
        //Cerrar la conexion a la BD
        pg_close($conn);
        //Comprobar si la consulta funciono
        if (!$prod_act) {
          echo 'No se puede realizar la operacion, hubo un error con la base de datos';
        } else {
          $data = pg_fetch_array($prod_act, 0);
      ?>
    </div><div class="row" style="margin: 2px">
      <form enctype="multipart/form-data" action="act_prod.php" method="post">
      <!--Indicar actualizar producto-->
      <input type="text" style="display:none" name="act_producto" value="1">
      <input type="text" style="display:none" name="id_prod" value=<?php echo '"'.$pid.'"';?>>
      Nombre: <input type="text" name="nombre" value=<?php echo '"'.$data[0].'"';?> required><br><br>
      Categoria: <select name="categoria" required>
      <?php
        while ($row = pg_fetch_row($process)) {
          if ($row[0] == $data[1]) {
            echo '<option value="'.$row[0].'" selected="selected">'.$row[1].'</option>';
          } else {
            echo '<option value="'.$row[0].'">'.$row[1].'</option>';
          }
        }
      ?>
      </select><br><br>

      Marca: <select name="marca" required>
      <?php
        while ($row = pg_fetch_row($marca)) {
          if ($row[0] == $data[2]) {
            echo '<option value="'.$row[0].'" selected="selected">'.$row[1].'</option>';
          } else {
          echo '<option value="'.$row[0].'">'.$row[1].'</option>';
          }
        }
      ?>
      </select><br><br>

      Talla: <select name="talla" required>
      <?php
        while ($row = pg_fetch_row($talla)) {
          if ($row[0] == $data[3]) {
            echo '<option value="'.$row[0].'" selected="selected">'.$row[1].'</option>';
          } else {
          echo '<option value="'.$row[0].'">'.$row[1].'</option>';
          }
        }
      ?>
      </select><br><br>

      Descripción: <input type="text" name="descripcion" value=<?php echo '"'.$data[4].'"';?> required><br><br>
      Precio: <input type="text" name="precio" value=<?php echo '"'.$data[5].'"';?> required><br><br>
      Cantidad: <input type="text" name="cantidad" value=<?php echo '"'.$data[6].'"';?> required><br><br>
      Imagen Actual: 
      <img src=<?php echo '"'.$data[7].'"';?> height="100px" width="100px"><br><br>
      Nueva Imagen: <input type="file" name="file"></input><br><br>
      Imagen Banner: 
      <?php
      $temp = explode("/", basename($data[7]));
      $banner = 'files/B-'.$temp[0];
      if ($banner == "files/B-default.jpg") {
        $banner = "img//B-default.jpg";
      }
      ?>
      <img src=<?php echo '"'.$banner.'"';?> height="100px" width="175px"><br><br>
      <input type="file" name="file_banner"></input><br><br>

      <input type="submit" value="Enviar">
      </form>
      <?php
      }
      ?>
    </div>
  </div>
</div>
</body>
</html>