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
//Evita el acceso a esta pagina sin autentificarse
if (!$_SESSION['usr']) {
  header("Location: index.php");
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

//Verificar que no haya articulos repetidos
$new_art = count($_SESSION['car_id_productos']);
for ($i=0; $i < $new_art; $i++) {
  if ($_POST['id_producto'] AND $_SESSION['car_id_productos'][$i] == $_POST['id_producto']) {
    $repetido = 1;
  }
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
        if (!$_POST['car_accion']) {
          if ($_SESSION['car_result']) {
            switch($_SESSION['car_result']){
              case 1:
                echo '<h4>Producto agregado</h4>';
                unset($_SESSION['car_result']);
                break;
              case 2:
                echo '<h4 style="color:red">No se puede agregar el mismo producto 2 veces</h4>';
                unset($_SESSION['car_result']);
                break;
              case 3:
                echo '<h4 style="color:blue">Se ha vaciado el carrito de compras</h4>';
                unset($_SESSION['car_result']);
                break;
              case 4:
                echo '<h4 style="color:blue">Se quitado el articulo del carrito de compras</h4>';
                unset($_SESSION['car_result']);
                break;
              case 5:
                echo $_SESSION['debug'][0].'<br>'.$_SESSION['debug'][1].'<br><h4 style="color:red">Durante tu compra alguien adquirio algun producto que habias seleccionado<br>Ya no es posible realizar su compra</h4>';
                unset($_SESSION['debug']);
                unset($_SESSION['car_result']);
                break;
            }
          }
          echo "<h3>Carrito de Compras</h3>";
          //Tevisar si hay productos agregados al carrito y de ser asi utiizar la informacion del mismo y le bd para calcular el total
          if (!$_SESSION['car_productos'] OR count($_SESSION['car_productos']) == 0) {
            echo '<h4>Aun no tienes productos en el carrito</h4>';
          } else{
              echo '<table class="table-bordered"><tr><th>ID</th><th>Articulo</th><th>Precio</th><th>Descuento</th><th>IVA</th><th>Cantidad</th><th>Subtotal</th></tr>';
            for ($i=0; $i < $new_art; $i++) {
              $pid = $_SESSION['car_id_productos'][$i];
              $query = ("SELECT prod_precio,desc_porcentaje FROM productos p,descuentos d WHERE p.id_descuento=d.id_descuento AND id_producto = $pid");
              $process = pg_query($conn, $query);
              if  (!$process) {
                echo 'Hubo un error al acceder a la base de datos<br>';
              }
              //Envio de 150 pesos
              $envio = 150;
              //Pasar a variables temporales los precios y descuentos para hacer el calculo
              $precio = pg_fetch_result($process, 0, 0);
              $descuento = pg_fetch_result($process, 0, 1);
              $iva = $precio*0.15;
              //Subtotal del producto en turno
              $subtotal = (($precio*(1-$descuento))+($precio*0.15))*$_SESSION['car_cantidad_productos'][$i];
              //Generar fila de la tabla del producto
              echo '<tr><td>'.$_SESSION['car_id_productos'][$i].'</td>';
              echo '<td>'.$_SESSION['car_productos'][$i].'</td>';
              echo '<td>$'.number_format($precio,2).'</td>';
              echo '<td>'.($descuento*100).'%</td>';
              echo '<td>$'.number_format($iva,2).'</td>';
              echo '<td>'.$_SESSION['car_cantidad_productos'][$i].'</td>';
              echo '<td>$'.number_format($subtotal,2).'</td>';
              echo '<td><form action="procesar_carrito.php" method="post"><input type="text" style="display:none" name="id_elemento" value="'.$i.'"><button name="car_accion" value="2">Quitar</button></form></td></tr>';
              //Ir sumando todo para obtener el total de la compra
              $total = $total + $subtotal;
            }
            //Si el total de compras es menor a 500 entonces se cobra el envio
            if ($total < 500) {
              echo '<tr><td></td><td></td><td></td><td></td><td></td><td>Envio</td><td>$'.number_format($envio,2).'</td></tr>';
              $total = $total + $envio;
            }
            echo '<tr><td></td><td></td><td></td><td></td><td></td><td>Total</td><td>$'.number_format($total,2).'</td></tr></table>';
          }
          if ($new_art AND $new_art > 0) {
            ?>
            <br>
            <form action="procesar_carrito.php" method="post">
                  <input type="text" style="display:none" name="car_accion" value="3">
                  <input type="submit" value="Vaciar el carrito">
            </form>
            <form action="compra.php" method="post">
                  <input type="text" style="display:none" name="compra_accion" value="1">
                  <?php
                  echo '<input type="text" style="display:none" name="compra_valor" value="'.$total.'">';
                  ?>
                  <input type="submit" value="Comprar">
            </form>
            <br><br>
            <?php
          }
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