<?php
// listado_almacen_1.php — versión revisada y optimizada
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errores_listado.log');

header('Content-Type: application/json; charset=utf-8');

// Limpiar buffers si existen
if (ob_get_level() > 0) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

$configPath = __DIR__ . '/../../config.php';
if (!file_exists($configPath)) {
    error_log("listado_almacen_1.php: archivo de config no encontrado en $configPath");
    echo json_encode([
        "draw" => 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Configuración no encontrada."
    ]);
    exit;
}

include $configPath;

if (!isset($pdo) || !$pdo) {
    error_log("listado_almacen_1.php: \$pdo no definido o no válido");
    echo json_encode([
        "draw" => 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error de conexión."
    ]);
    exit;
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $request = $_REQUEST;

    $draw        = intval($request['draw'] ?? 0);
    $row         = intval($request['start'] ?? 0);
    $rowperpage  = intval($request['length'] ?? 10);
    $searchValue = trim($request['search']['value'] ?? '');
    $orderColumnIndex = intval($request['order'][0]['column'] ?? 0);
    $orderColumnName  = $request['columns'][$orderColumnIndex]['data'] ?? 'descripcion';
    $orderDirection   = (strtolower($request['order'][0]['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

    // Columnas válidas para orden y filtro
    $validColumns = [
        'id_producto',
        'imagen',
        'codigo',
        'descripcion',
        'costo_minorista',
        'porcentaje_minorista',
        'precio_minorista',
        'nombre_marca',
        'nombre_categoria'
    ];
    if (!in_array($orderColumnName, $validColumns)) {
        $orderColumnName = 'descripcion';
    }

    $sql_base = "
        FROM tbp_almacen a
        LEFT JOIN tbp_marca m ON a.id_marca = m.id_marca
        LEFT JOIN tbp_categoria c ON a.id_categoria = c.id_categoria
    ";

    // Total sin filtros
    $totalRecords = (int)$pdo->query("SELECT COUNT(*) $sql_base")->fetchColumn();

    // Mapear alias visibles a columnas reales
    $columnMap = [
        'nombre_marca' => 'm.nombre',
        'nombre_categoria' => 'c.nombre_categoria'
    ];

    $sql_filter_parts = [];
    $params = [];

    // 🔍 Filtro global
    if ($searchValue !== '') {
        $sql_filter_parts[] = "("
            . "a.codigo LIKE :search_global "
            . "OR a.descripcion LIKE :search_global "
            . "OR m.nombre LIKE :search_global "
            . "OR c.nombre_categoria LIKE :search_global"
            . ")";
        $params[':search_global'] = "%$searchValue%";
    }

    // 🔍 Filtros por columna específica
    if (isset($request['columns']) && is_array($request['columns'])) {
        foreach ($request['columns'] as $col) {
            $colName = $col['data'] ?? '';
            $colSearch = trim($col['search']['value'] ?? '');

            if ($colSearch !== '' && in_array($colName, $validColumns)) {
                if ($colName === 'imagen') continue; // no filtrar por imagen

                $filterColumn = $columnMap[$colName] ?? "a.$colName";
                $paramKey = ":search_col_" . $colName;

                $sql_filter_parts[] = "$filterColumn LIKE $paramKey";
                $params[$paramKey] = "%$colSearch%";
            }
        }
    }

    // WHERE final
    $sql_filter = count($sql_filter_parts) > 0
        ? ' WHERE ' . implode(' AND ', $sql_filter_parts)
        : '';

    // Total filtrado
    $stmt = $pdo->prepare("SELECT COUNT(*) $sql_base $sql_filter");
    $stmt->execute($params);
    $totalRecordwithFilter = (int)$stmt->fetchColumn();

    // Consulta principal paginada
    $sql_data = "
        SELECT 
            a.id_producto,
            a.imagen,
            a.codigo,
            a.descripcion,
            a.costo_minorista,
            a.porcentaje_minorista,
            a.precio_minorista,
            m.nombre AS nombre_marca,
            c.nombre_categoria AS nombre_categoria
        $sql_base
        $sql_filter
        ORDER BY $orderColumnName $orderDirection
       
    ";

    $stmt = $pdo->prepare($sql_data);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':offset', $row, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $rowperpage, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecordwithFilter,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error en listado_almacen_1.php: " . $e->getMessage());
    echo json_encode([
        "draw" => $draw ?? 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en servidor, revisa logs."
    ]);
}






