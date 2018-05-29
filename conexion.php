<?php
//Declarar funcion que devuelva la conexion a la bd
function conectar(){
	$conn = pg_connect("host=127.0.0.1 port=5432 dbname=tienda user=tiendaadmin password=hola123");
	return $conn;
}
?>