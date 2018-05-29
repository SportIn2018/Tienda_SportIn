<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<title>Administración de Usuarios</title>
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

//Query para la tabla de usuarios
$q_usuarios = ("SELECT id_usuario,us_nombre,us_apaterno,us_correo,us_login,tipo_us_descripcion FROM usuarios u,c_tipos_usuario t WHERE u.id_tipo_usuario=t.id_tipo_usuario");

//Ejecutar queries llamando la variable de conexiòn a la bd
$process = pg_query($conn, $q_categ);
$marca = pg_query($conn, $q_marca);
$talla = pg_query($conn, $q_talla);
$usuario = pg_query($conn, $q_usuarios);

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
if  (!$usuario) {
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
  <div class="col">
    <div class="row-fluid" style="background-color: #FFFFFF">
      <div class="col" style="margin: 5px">
      <h4>Administracion de usuarios</h4>
        <?php
        //Mensajes sobre estado de operaciones solicitadas
        if ($_SESSION['adm_usr_est']) {
          switch ($_SESSION['adm_usr_est']) {
            case 1:
              echo 'No pudo realizarse la operación, hubo un problema al acceder a la base de datos';
              unset($_SESSION['adm_usr_est']);
              break;
            case 2:
              echo 'La operación se ha realizado exitosamente';
              unset($_SESSION['adm_usr_est']);
              break;
          }
        }
        //Despliega opciones de usuario en caso de seleccionarse uno
        if ($_POST['adm_usr_id']) {
          //Asignar funcion de conectar a una variable para conectar a la bd
          $conn = conectar();
          echo "Opciones para id: ".$_POST['adm_usr_id'];
          //Query para mostrar la informacion basica del usuario seleccionado
          $q_usr = ("SELECT id_usuario,us_nombre,us_apaterno,us_correo,us_login,tipo_us_descripcion,u.id_tipo_usuario FROM usuarios u,c_tipos_usuario t WHERE u.id_tipo_usuario=t.id_tipo_usuario AND id_usuario='$_POST[adm_usr_id]'");
          //Ejecutar query
          $usr = pg_query($conn, $q_usr);
          //Comprobar si se ejecuto la query
          if  (!$usr) {
            echo "Hubo un error al acceder a la base de datos<br>";
          }
          //Cerrar la conexion a la BD
          pg_close($conn);

          $usr = pg_fetch_array($usr);
          //Desplegar informacion del usuario
          echo '<br>ID: '.$usr[0];
          echo '<br>Nombre de usuario: '.$usr[4];
          echo '<br>Nombre completo: '.$usr[1].' '.$usr[2];
          echo '<br>Correo electrónico: '.$usr[3];
          echo '<br>Tipo de usuario: '.$usr[5];
          echo '<br><br><h5>Opciones</h5>';
          
          //El usuario 1 (admin por defecto) no puede ser eliminado para evitar quedar fuera de la administracion del sistema
          if ($usr[0] != 1) {
            //Opciones
            switch ($usr[6]) {
              case 1:
              echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="2"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Vendedor</button></form>';
                echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="3"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Comprador</button></form>';
                break;
              case 2:
              echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="1"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Administrador</button></form>';
                echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="3"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Comprador</button></form>';
                break;
              case 3:
              echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="1"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Administrador</button></form>';
                echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="2"><button name="adm_usr_id" value="'.$usr[0].'">Cambiar tipo a Vendedor</button></form>';
                break;
            }
            echo '<form action="admin_mod_us.php" method="post"><input type="text" style="display:none" name="adm_usr_op" value="0"><button name="adm_usr_id" value="'.$usr[0].'">Eliminar</button></form>';
          } else {
            echo "No hay opciones disponibles para este usuario";
          }
          //Enlace para volver al menu de administracion de usuarios
          echo '<br><a href="admin_usuarios.php">Regresar</a>';
          } else{
            echo '<table class="table-bordered"><tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Correo</th><th>Tipo</th></tr>';
            while ($row = pg_fetch_row($usuario)) {
            echo '<tr><td>'.$row[0].'</td><td>'.$row[4].'</td><td>'.$row[1].' '.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[5].'</td><td><form action="admin_usuarios.php" method="post"><button name="adm_usr_id" value="'.$row[0].'">Opciones</button></form></td></tr>';
          }
          echo '</table>';
        }
        ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>