<?php
// listado_de_almacen.php (versión con filtro por columna corregido)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errores_listado.log');

header('Content-Type: application/json; charset=utf-8');

if (ob_get_level() > 0) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

$configPath = __DIR__ . '/../../config.php';
if (!file_exists($configPath)) {
    error_log("listado_de_almacen.php: archivo de config no encontrado en $configPath");
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
    error_log("listado_de_almacen.php: \$pdo no definido");
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

    $draw       = intval($request['draw'] ?? 0);
    $row        = intval($request['start'] ?? 0);
    $rowperpage = intval($request['length'] ?? 10);
    $searchValue = trim($request['search']['value'] ?? '');
    $orderColumnIndex = intval($request['order'][0]['column'] ?? 0);
    $orderColumnName  = $request['columns'][$orderColumnIndex]['data'] ?? 'descripcion';
    $orderDirection   = (strtolower($request['order'][0]['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

    $validColumns = [
        'id_producto',
        'imagen',
        'codigo',
        'descripcion',
        'costo_mayorista',
        'porcentaje_mayorista',
        'precio_mayorista',
        'nombre_marca',
        'nombre_proveedor',
        'nombre_categoria'
    ];
    if (!in_array($orderColumnName, $validColumns)) {
        $orderColumnName = 'descripcion';
    }

    $sql_base = "
        FROM tb_almacen a
        LEFT JOIN tb_marca m ON a.id_marca = m.id_marca
        LEFT JOIN tb_proveedor p ON a.id_proveedor = p.id_proveedor
        LEFT JOIN tb_categoria c ON a.id_categoria = c.id_categoria
    ";

    // Total sin filtro
    $totalRecords = (int)$pdo->query("SELECT COUNT(*) AS total $sql_base")->fetchColumn();

    // Mapear columnas alias a columnas reales para filtro
    $columnMap = [
        'nombre_marca' => 'm.nombre',
        'nombre_proveedor' => 'p.razon_social',
        'nombre_categoria' => 'c.nombre_categoria',
    ];

    // Construir filtros SQL
    $sql_filter_parts = [];
    $params = [];

    // Filtro global
    if ($searchValue !== '') {
        $sql_filter_parts[] = "(a.codigo LIKE :search_global
            OR a.descripcion LIKE :search_global
            OR m.nombre LIKE :search_global
            OR p.razon_social LIKE :search_global
            OR c.nombre_categoria LIKE :search_global)";
        $params[':search_global'] = "%$searchValue%";
    }

    // Filtros por columna
    if (isset($request['columns']) && is_array($request['columns'])) {
        foreach ($request['columns'] as $col) {
            $colName = $col['data'] ?? '';
            $colSearch = trim($col['search']['value'] ?? '');

            if ($colSearch !== '' && in_array($colName, $validColumns)) {
                if ($colName === 'imagen') continue; // no filtrar imagen

                $filterColumn = $columnMap[$colName] ?? $colName;
                $paramKey = ":search_col_" . $colName;

                $sql_filter_parts[] = "$filterColumn LIKE $paramKey";
                $params[$paramKey] = "%$colSearch%";
            }
        }
    }

    // Construir WHERE final
    $sql_filter = '';
    if (count($sql_filter_parts) > 0) {
        $sql_filter = ' WHERE ' . implode(' AND ', $sql_filter_parts);
    }

    // Total con filtro
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total $sql_base $sql_filter");
    $stmt->execute($params);
    $totalRecordwithFilter = (int)$stmt->fetchColumn();

    // Consulta datos paginados con filtro y orden
    $sql_data = "
        SELECT 
            a.id_producto,
            a.imagen,
            a.codigo,
            a.descripcion,
            a.costo_mayorista,
            a.porcentaje_mayorista,
            a.precio_mayorista,
            m.nombre AS nombre_marca,
            p.razon_social AS nombre_proveedor,
            c.nombre_categoria AS nombre_categoria
        $sql_base
        $sql_filter
        ORDER BY $orderColumnName $orderDirection
        LIMIT :offset, :limit
    ";

    $stmt = $pdo->prepare($sql_data);

    // Bind parámetros búsqueda
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
    error_log("Error en listado_de_almacen.php: " . $e->getMessage());
    echo json_encode([
        "draw" => $draw ?? 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "Error en servidor, revisa logs."
    ]);
}

