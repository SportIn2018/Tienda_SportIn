<?php
//Abrir la session
session_start();

//Acciones de acuerdo a la peticion
if (!$_POST['comprar']) {
        header("Location: index.php");
        exit;
  } else{
    //Incluir archivo php de conexiòn
    include 'conexion.php';
    //Asignar funcion de conectar a una variable para conectar a la bd
    $conn = conectar();
    //Obtener id del usuario para relacionarlo a la transaccion
    $id_usr = $_SESSION['usr'];
    $pago = $_POST['tipo_pago'];
    //Query para la insercion de datos de la venta
    $query = ("INSERT INTO ventas(id_usuario,id_tipo_pago) VALUES('$id_usr','$pago') RETURNING id_venta");
    $venta = pg_fetch_result(pg_query($conn, $query), 0);
    //Recorrer los productos del carrito para agregarlos
    if  (!$venta) {
      echo "Hubo un error al acceder a la base de datos<br>";

    } else{
      $new_art = count($_SESSION['car_id_productos']);
      for ($i=0; $i < $new_art; $i++) {
        $id_ins = (int)$_SESSION['car_id_productos'][$i];
        $cnt_ins = (int)$_SESSION['car_cantidad_productos'][$i];
        //Query para la insercion de datos
        $query = ("INSERT INTO venta_producto(id_venta,id_producto,cantidad) VALUES($venta,$id_ins,$cnt_ins)");
        $proceso = pg_query($conn, $query);
        if (!$proceso) {
          echo "Hubo un error al acceder a la base de datos<br>";
          break;
        }
        //Query para la actualizacion de inventario
        $query = ("UPDATE productos SET prod_inventario = (prod_inventario - $cnt_ins) WHERE id_producto = $id_ins");
        $proceso = pg_query($conn, $query);
        if (!$proceso) {
          echo "Hubo un error al acceder a la base de datos<br>";
          break;
        }
      }
    if ($proceso) {
          //Retirar los arreglos de productos y cantidades de memoria
          unset($_SESSION['car_id_productos']);
          unset($_SESSION['car_productos']);
          unset($_SESSION['car_cantidad_productos']);
          unset($_SESSION['car_precio_productos']);
          //POR HACER: Mandar correo de confirmacion y desplegar pantalla confirmando la compra y la informacion de la misma
          $_SESSION['correo_ok'] = 1;
          header("Location: correo.php");
          exit;
        }
    }
}
?>