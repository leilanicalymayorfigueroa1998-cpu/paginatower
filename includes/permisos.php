<?php

function tienePermiso($conexionBD, $idRol, $modulo, $accion) {

    $consulta = $conexionBD->prepare("SELECT COUNT(*)
        FROM rol_permiso rp
        INNER JOIN permisos p ON rp.id_permiso = p.id_permiso
        INNER JOIN modulos m ON p.id_modulo = m.id_modulo
        WHERE rp.id_rol = :rol
        AND m.nombre = :modulo
        AND p.accion = :accion
    ");

    $consulta->bindParam(':rol', $idRol);
    $consulta->bindParam(':modulo', $modulo);
    $consulta->bindParam(':accion', $accion);
    $consulta->execute();

    return $consulta->fetchColumn() > 0;
}

?>