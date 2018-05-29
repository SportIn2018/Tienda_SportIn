<?php
//Abrir la session
session_start();
//Verificar que no haya articulos repetidos
$new_art = count($_SESSION['car_id_productos']);
for ($i=0; $i < $new_art; $i++) {
  if ($_POST['id_producto'] AND $_SESSION['car_id_productos'][$i] == $_POST['id_producto']) {
    $repetido = 1;
  }
}
//Acciones de acuerdo a la peticion
if (!$_POST['car_accion']) {
        header("Location: index.php");
      } else{
        //Determinar acción a realizar dependiendo la variable recibida para el carrito
        switch($_POST['car_accion']){
            //1: Agregar articulo
            case 1:
              if (!$repetido) {
                //Agregar producto id y nombre en el carrito
                $_SESSION['car_id_productos'][$new_art] = $_POST['id_producto'];
                $_SESSION['car_productos'][$new_art] = $_POST['nom_producto'];
                $_SESSION['car_cantidad_productos'][$new_art] = $_POST['cantidad'];
                $_SESSION['car_result'] = 1;
                header("Location: carrito.php");
                break;
              } else{
                $_SESSION['car_result'] = 2;
                header("Location: carrito.php");
                break;
              }
            //2: Quitar articulo (proviene de pagina de mostrar carrito)
            case 2:
              //Obtener id del elemento a quitar
              $del_art = $_POST['id_elemento'];
              //Quitar producto por id y nombre en el carrito
              unset($_SESSION['car_id_productos'][$del_art]);
              unset($_SESSION['car_productos'][$del_art]);
              //Reordenar arreglos
              $_SESSION['car_id_productos'] = array_values($_SESSION['car_id_productos']);
              $_SESSION['car_productos'] = array_values($_SESSION['car_productos']);
              //Quitar cantidad del producto en el carrito
              unset($_SESSION['car_cantidad_productos'][$del_art]);
              //Reordenar arreglo
              $_SESSION['car_cantidad_productos'] = array_values($_SESSION['car_cantidad_productos']);
              $_SESSION['car_result'] = 4;
              header("Location: carrito.php");
              break;
            //3: Vaciar el carrito de compras (proviene de pagina de mostrar carrito)
            case 3:
              //Retirar los arreglos de productos y cantidades de memoria
              unset($_SESSION['car_id_productos']);
              unset($_SESSION['car_productos']);
              unset($_SESSION['car_cantidad_productos']);
              $_SESSION['car_result'] = 3;
              header("Location: carrito.php");
              break;
        }
      }
?>