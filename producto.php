<?php 
//Incluir archivo php de conexiòn
include 'conexion.php';
//Abrir la sesion
session_start();
//Asignar funcion de conectar a una variable para conectar a la bd
$conn = conectar();
$id = $_GET["id"];

//Query de informacion del producto
//0=Categoria,1=marca,2=talla,3=descuento,4=id,5=nombre,6=descripcion,7=ruta_imagen,8=precio,9=inventario,10=id_marca,11=id_categoria,12=id_talla
$query = ("SELECT c.categ_descripcion,m.marca_descripcion,t.talla_descripcion,d.desc_porcentaje,p.id_producto,p.prod_nombre,p.prod_descripcion,p.prod_ruta_img,p.prod_precio,p.prod_inventario,p.id_marca,p.id_categoria,p.id_talla FROM productos p, c_categorias c, c_marcas m, c_tallas t, descuentos d WHERE p.id_categoria=c.id_categoria AND p.id_marca=m.id_marca AND p.id_talla=t.id_talla AND p.id_descuento=d.id_descuento AND p.id_producto='$id'");

//Ejecutar query llamando la variable de conexiòn a la bd
$process = pg_query($conn, $query);

$prod = pg_fetch_array($process);
//Informar si la query se ejecuto o no
if  (!$process) {
   $ok = 0;
}
else{
  $ok = 1;
}

if (!$prod[5]){
  $ok = 0; 
}

if ($prod[9] > 0) {
  $cantidad = 1;
}

//Queries necesarias para desplegar categorias y los productos mas populares
$q_categ = ("SELECT id_categoria,categ_descripcion FROM c_categorias");
$q_marca = ("SELECT id_marca,marca_descripcion FROM c_marcas");
$q_talla = ("SELECT id_talla,talla_descripcion FROM c_tallas");
$q_prod = ("SELECT count(id_producto) AS ref FROM productos");
$q_vendidos = ("SELECT count(id_producto) AS ref FROM venta_producto");

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

//Cerrar la conexion a la BD
pg_close($conn);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title><?php echo $prod[5];?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
 <link rel="stylesheet" href="css/beta.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

</head>
<body style="background-color: #417c81">
  
<!--Barra superior-->
<div class="row" style="background-color: #0e484e">
  <div class="col text-right"><form action=""><input type="text" name="buscar"><input type="submit" value="Buscar"></form></div>
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
    <div class="row" style="background-color: #FFFFFF">
      <?php
        if ($ok == 1) {
          echo '<div class="col-4">';
          echo '<img src="'.$prod[7].'"height="300px" width="300px" border="1"></div>';
          echo '<div class="col-5">';
          echo '<h2>'.$prod[5].'</h2>';
          echo 'Categoria: <a href="catalogo.php?categoria='.$prod[11].'" >'.$prod[0].'</a><br>';
          echo 'Marca: <a href="catalogo.php?marca='.$prod[10].'" >'.$prod[1].'</a><br>';
          echo 'Talla: <a href="catalogo.php?marca='.$prod[12].'" >'.$prod[2].'</a><br>';
          echo 'Disponibles: '.$prod[9];
          echo '<br><br><h4>Descripción:</h4>'.$prod[6].'</div>';
          
          ?>
          <div class="col-2">
            <?php
            if (!$cantidad) {
              echo '<br><h4>$'.$prod[8].'</h4>';
              echo '<h4 style="color:red">Producto agotado</h4>';
            } else{
              if ($prod[3] > 0) {
                echo '<br><h4><strike>$'.number_format($prod[8], 2).'</strike></h4>Oferta de<br>';
                echo ($prod[3]*100).'% de descuento<br><h4>$'.number_format(($prod[8]*(1-$prod[3])), 2).'</h4>';
              } else {
                echo '<br><h4>$'.number_format($prod[8], 2).'</h4>';
              }
              if (!$_SESSION['usr']) {
                echo 'Inicia sesion<br>para comprar<br><br>';
              } else {
            ?>
            <form action="procesar_carrito.php" method="post">
              <input type="text" style="display:none" name="car_accion" value="1">
              Cantidad: <select name="cantidad" required>
                <?php
                for ($i=1; $i <= $prod[9]; $i++) { 
                  echo '<option value="'.$i.'">'.$i.'</option>';
                }
                ?>
              </select><br><br>
              <?php
              echo '<input type="text" style="display:none" name="id_producto" value="'.$prod[4].'">';
              echo '<input type="text" style="display:none" name="nom_producto" value="'.$prod[5].'">';
              ?>
              <input type="submit" value="Agregar al carrito">
            </form>
            <?php
              }
            }
            ?>
            <br><h6>Comparte este producto con tus amigos:</h6>
            <?php echo '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fsportin.com%2Fproducto.php?id='.$id.'&amp;src=sdkpreparse"><img src="img/fb_icon.png" height="50px" width="50px"></a><br>';
              echo '<a target="_blank" href="https://twitter.com/share?text=Mira%20lo%20encontre%20en%20%23SportIn%20aqui&url=https://sportin.com/producto.php?id='.$id.'"><img src="img/tw_icon.png" height="50px" width="50px"></a>';?>
          </div>
          <?php
        } else {
          echo '<div class="col text-center">';
          echo '<h3>Error: El producto no existe</h3></div>';
        }
        ?>
    </div>
    <div class="row" style="background-color: #FFFFFF"><div class="col text-center"><a href="catalogo.php">Volver al catalogo</a></div></div>
  </div>
</div>
</body>
</html>