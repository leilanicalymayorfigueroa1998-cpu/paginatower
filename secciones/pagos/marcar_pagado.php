<?php

include("../../bd.php");
include("../../services/PagoService.php");

if (isset($_GET['id'])) {

    $pagoService = new PagoService($conexionBD);
    $pagoService->marcarComoPagado($_GET['id']);

    header("Location: index.php");
    exit();
}

?>