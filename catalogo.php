<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Productos</title>
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

//Queries necesarias para filtrar los productos
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");

//Si hay peticion de categorias utilizar la query correspondiente (Queries para contar los productos y determinar la cantidad de paginas para ordenarlos)
if ($_GET['buscar']){
    $q_catal = ("SELECT DISTINCT p.id_producto FROM productos p,c_categorias c,c_marcas m WHERE p.id_categoria=c.id_categoria AND p.id_marca = m.id_marca AND prod_nombre ILIKE '%$_GET[buscar]%' OR c.categ_descripcion ILIKE '%$_GET[buscar]%' OR m.marca_descripcion ILIKE '%$_GET[buscar]%'");
    $filtro_tipo = 'Busqueda';
    $filtro_nom = $_GET['buscar'];
    $filtro_ok = 1;
} else{
    if (!$_GET['categoria']){
      if ($_GET['marca']) {
        $q_catal = ("SELECT id_producto FROM productos WHERE id_marca = '$_GET[marca]'");
      } else {
        if ($_GET['talla']){
          $q_catal = ("SELECT id_producto FROM productos WHERE id_talla = '$_GET[talla]'");
        } else{
          $q_catal = ("SELECT id_producto FROM productos");
        } 
      }
    } else{
      $q_catal = ("SELECT id_producto FROM productos WHERE id_categoria = '$_GET[categoria]'");
    }  
}

//Ejecutar query llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);
$catalogo = pg_query($conn, $q_catal);

//Informar si las queries se ejecutaron o no
if  (!$process) {
   echo "Hubo un error al acceder a la base de datos<br>";

}
if  (!$marca) {
  echo "Hubo un error al acceder a la base de datos<br>";

}
if  (!$catalogo) {
   echo "Hubo un error al acceder a la base de datos<br>";
}

//Obtener el numero de productos correspondiente a la consulta para calcular el numero de paginas de orden
$num_prod = pg_num_rows($catalogo);

//Numero de productos que se van a mostrar por pagina
$pgprod = 6;

//Determinar la pagina de orden que se va a desplegar
if ($_GET['pag']) {
  if (is_numeric($_GET['pag']) AND $_GET['pag'] > 0) {
    $pag = $_GET['pag'];
  } else {
    $pag = 1;
  }
} else{
  $pag = 1;
}

//Calcular total de paginas para el resultado
$mod = $num_prod%$pgprod;
if ($mod > 0) {
  $ext = 1;
} else {
  $ext = 0;
}
$pag_total = (($num_prod-$mod)/$pgprod)+$ext;

//Si se sobrepasa el nuemro de paginas probables desde el GET se regresa al contenido de la pagina uno
if ($_GET['pag'] > $pag_total) {
  $pag = 1;
}

//Valor de offset para obtener solo los resultados de la pagina de contenido activa
$pag_offset = (($pag*$pgprod)-$pgprod);

//Seleccionar la query de acuerdo a si hay peticion de categorias o no
if ($_GET['buscar']){
    $q_catal = ("SELECT DISTINCT p.id_producto,p.prod_ruta_img,p.prod_nombre,p.prod_precio FROM productos p,c_categorias c,c_marcas m WHERE p.id_categoria=c.id_categoria AND p.id_marca = m.id_marca AND prod_nombre ILIKE '%$_GET[buscar]%' OR c.categ_descripcion ILIKE '%$_GET[buscar]%' OR m.marca_descripcion ILIKE '%$_GET[buscar]%' LIMIT '$pgprod' OFFSET '$pag_offset'");
    $filtro_tipo = 'Busqueda';
    $filtro_nom = $_GET['buscar'];
    $filtro_ok = 1;
} else{
    if (!$_GET['categoria']){
      if ($_GET['marca']) {
        $q_catal = ("SELECT id_producto,prod_ruta_img,prod_nombre,prod_precio FROM productos WHERE id_marca = '$_GET[marca]' LIMIT '$pgprod' OFFSET '$pag_offset'");
      } else {
        if ($_GET['talla']){
          $q_catal = ("SELECT id_producto,prod_ruta_img,prod_nombre,prod_precio FROM productos WHERE id_talla = '$_GET[talla]' LIMIT '$pgprod' OFFSET '$pag_offset'");
        } else{
          $q_catal = ("SELECT id_producto,prod_ruta_img,prod_nombre,prod_precio FROM productos LIMIT '$pgprod' OFFSET '$pag_offset'");
        } 
      }
    } else{
      $q_catal = ("SELECT id_producto,prod_ruta_img,prod_nombre,prod_precio FROM productos WHERE id_categoria = '$_GET[categoria]' LIMIT '$pgprod' OFFSET '$pag_offset'");
    }  
}

//Ejecutar la query de consulta de datos de los productos
$catalogo = pg_query($conn, $q_catal);

//Informar si las query se ejecuto o no
if  (!$catalogo) {
   echo "Hubo un error al acceder a la base de datos<br>";

}
//Cerrarla conexion a la BD
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
      if ($row[0] == $_GET['categoria']) {
        $filtro_nom = $row[1];
        $filtro_tipo = 'Categoria';
        $filtro_ok = 1;
      }
    }?>
    <br><h4>Marcas</h4>
    <?php 
      while ($row = pg_fetch_row($marca)) {
      echo '<a href="catalogo.php?marca='.$row[0].'">'.$row[1].'</a><br>';
      if ($row[0] == $_GET['marca']) {
        $filtro_nom = $row[1];
        $filtro_tipo = 'Marca';
        $filtro_ok = 1;
      }
    }?>
    <br><h4>Tallas</h4>
    <?php 
      while ($row = pg_fetch_row($talla)) {
      echo '<a href="catalogo.php?talla='.$row[0].'">'.$row[1].'</a><br>';
      if ($row[0] == $_GET['talla']) {
        $filtro_nom = $row[1];
        $filtro_tipo = 'Tallas';
        $filtro_ok = 1;
      }
    }?>
  </div>
  <!--Contenido-->
  <div class="col">
      <div class="row">
        <div class="col text-center text-white">
            <?php
            if (!$filtro_ok){
                echo '<h3>Catalogo de productos</h3>';
            } else{
                echo '<h3>'.$filtro_tipo.': '.$filtro_nom.'</h3>';
            }
            ?>
        </div>
      </div>
      <div class="row">
        <?php
        while ($row = pg_fetch_row($catalogo)) {
          $no_prod = $no_prod + 1;
          echo '<div class="col-4 text-center text-white"><a href="producto.php?id='.$row[0].'"><img src="'.$row[1].'" height="150px" width="150px"></a><br>'.$row[2].'</a><br>$'.number_format($row[3], 2).' M.N.</div>';
        }
        if (!$no_prod){
          echo '<div class="col text-center text-white"><h4>No se encontraron productos</h4></div>';
        }
        ?>
      </div>
      <div class="row" style="background-color: #FFFFFF">
        <div class="col text-center">
          <?php
          if ($pag>1) {
            if ($_GET['categoria']) {
              echo '<a href="catalogo.php?categoria='.$_GET['categoria'].'&pag=1"> < </a>';
            } else if ($_GET['marca']) {
              echo '<a href="catalogo.php?marca='.$_GET['marca'].'&pag=1"> < </a>';
            } else if ($_GET['talla']) {
              echo '<a href="catalogo.php?talla='.$_GET['talla'].'&pag=1"> < </a>';
            } else if ($_GET['buscar']) {
              echo '<a href="catalogo.php?buscar='.$_GET['buscar'].'&pag=1"> < </a>';
            } else{
              echo '<a href="catalogo.php?pag=1"> < </a>';
            }           
          }
          for ($i=1; $i <= $pag_total ; $i++) {
            if ($_GET['categoria']) {
              echo '<a href="catalogo.php?categoria='.$_GET['categoria'].'&pag='.$i.'">'.$i.' </a>';
            } else if ($_GET['marca']) {
              echo '<a href="catalogo.php?marca='.$_GET['marca'].'&pag='.$i.'">'.$i.' </a>';
            } else if ($_GET['talla']) {
              echo '<a href="catalogo.php?talla='.$_GET['talla'].'&pag='.$i.'">'.$i.' </a>';
            } else if ($_GET['buscar']) {
              echo '<a href="catalogo.php?buscar='.$_GET['buscar'].'&pag='.$i.'">'.$i.' </a>';
            } else{
              echo '<a href="catalogo.php?pag='.$i.'">'.$i.' </a>';
            }
          }
          if ($pag<$pag_total) {
            if ($_GET['categoria']) {
              echo '<a href="catalogo.php?categoria='.$_GET['categoria'].'&pag='.$pag_total.'"> > </a>';
            } else if ($_GET['marca']) {
              echo '<a href="catalogo.php?marca='.$_GET['marca'].'&pag='.$pag_total.'"> > </a>';
            } else if ($_GET['talla']) {
              echo '<a href="catalogo.php?talla='.$_GET['talla'].'&pag='.$pag_total.'"> > </a>';
            } else if ($_GET['buscar']) {
              echo '<a href="catalogo.php?buscar='.$_GET['buscar'].'&pag='.$pag_total.'"> > </a>';
            } else{
              echo '<a href="catalogo.php?pag='.$pag_total.'"> > </a>';
            }
          }
          ?>
        </div>
      </div>
  </div>
</div>
</body>
</html>

