<?php

$sql_ventas = "SELECT *,cli.nombre_cliente as nombre_cliente
                FROM tb_ventas as ve INNER JOIN tb_clientes as cli ON cli.id_cliente = ve.id_cliente where ve.id_venta = '$id_venta_get'";
$query_ventas = $pdo->prepare($sql_ventas);
$query_ventas->execute();
$ventas_datos = $query_ventas->fetchAll(PDO::FETCH_ASSOC);


foreach ($ventas_datos as $venta_dato) {
    $nro_venta = $venta_dato['nro_venta'];
    $id_cliente = $venta_dato['id_cliente'];
}