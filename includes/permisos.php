<?php

function tienePermiso($conexionBD, $idRol, $modulo, $accion) {

    try {

        $consulta = $conexionBD->prepare("SELECT 1
            FROM rol_permiso rp
            INNER JOIN permisos p ON rp.id_permiso = p.id_permiso
            INNER JOIN modulos m ON p.id_modulo = m.id_modulo
            WHERE rp.id_rol = :rol
            AND m.nombre = :modulo
            AND p.accion = :accion
            LIMIT 1 ");

        $consulta->execute([
            ':rol'     => $idRol,
            ':modulo'  => $modulo,
            ':accion'  => $accion
        ]);

        return $consulta->fetchColumn() !== false;

    } catch (PDOException $e) {

        error_log("Error en permisos: " . $e->getMessage());
        return false;
    }
}


function verificarPermiso($conexionBD, $idRol, $modulo, $accion) {

    if (!tienePermiso($conexionBD, $idRol, $modulo, $accion)) {

        http_response_code(403);

        echo "
            <div style='
                font-family: Arial;
                padding: 20px;
                text-align: center;
                color: #b91c1c;
            '>
                <h2>403 - Acceso Denegado</h2>
                <p>No tienes permisos para acceder a este módulo.</p>
            </div>
        ";

        exit();
    }
}

?>