<?php
require_once "connection.php";

// Función para evitar XSS, asumiendo que está definida en "connection.php" o se añade aquí.
// Si Secure() no está definida, se asume que hace un htmlspecialchars.
if (!function_exists('Secure')) {
    function Secure($data) {
        return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
    }
}


$vehicle_name = trim(filter_input(INPUT_POST, "vehicle_name", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
$vehicle_brand = trim(filter_input(INPUT_POST, "vehicle_brand", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
$vehicle_plate = trim(filter_input(INPUT_POST, "vehicle_plate", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
$vehicle_combustion = trim(filter_input(INPUT_POST, "vehicle_combustion", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
$vehicle_warranty = filter_input(INPUT_POST, "vehicle_warranty", FILTER_SANITIZE_SPECIAL_CHARS) ?: "No";

$vehicle_cleanCar = filter_input(INPUT_POST, "vehicle_cleanCar", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
$vehicle_oilChange = filter_input(INPUT_POST, "vehicle_oilChange", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
$vehicle_brakeCheck = filter_input(INPUT_POST, "vehicle_brakeCheck", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
$vehicle_alignment = filter_input(INPUT_POST, "vehicle_alignment", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;

$errors = [];
if(empty($vehicle_name)) $errors[] = "El nombre del vehículo es obligatorio.";
if(empty($vehicle_brand)) $errors[] = "La marca es obligatoria.";
if(empty($vehicle_plate)) $errors[] = "La matrícula es obligatoria.";

$image_path = null;
$image_name = null;
if(isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK){
    $tmp_image = $_FILES['vehicle_image']['tmp_name'];
    $image_name = $_FILES['vehicle_image']['name'];
    if(!file_exists("uploads")) mkdir("uploads", 0777, true);
    $image_path = "uploads/" . uniqid() . "_" . basename($image_name);
    if(!move_uploaded_file($tmp_image, $image_path)){
        $errors[] = "No se pudo guardar la imagen.";
    }
}

$success = false;

if(empty($errors)){
    try {
        $pdo = Connection();

        // Comprobar si la matrícula ya existe
        $checkSql = "SELECT COUNT(*) FROM vehiculo WHERE plate = :plate";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':plate' => $vehicle_plate]);
        $exists = $checkStmt->fetchColumn();

        if($exists > 0){
            $errors[] = "La matrícula '" . Secure($vehicle_plate) . "' ya existe en la base de datos.";
        } else {
            // Insertar nuevo vehículo
            $sql = "INSERT INTO vehiculo 
                    (clientName, brand, plate, combustion, warranty, cleanCar, oilChange, brakeCheck, alignment, image) 
                    VALUES 
                    (:name, :brand, :plate, :combustion, :warranty, :cleanCar, :oilChange, :brakeCheck, :alignment, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $vehicle_name,
                ':brand' => $vehicle_brand,
                ':plate' => $vehicle_plate,
                ':combustion' => $vehicle_combustion,
                ':warranty' => $vehicle_warranty === "Sí" ? 1 : 0,
                ':cleanCar' => $vehicle_cleanCar ? 1 : 0,
                ':oilChange' => $vehicle_oilChange ? 1 : 0,
                ':brakeCheck' => $vehicle_brakeCheck ? 1 : 0,
                ':alignment' => $vehicle_alignment ? 1 : 0,
                ':image' => $image_path
            ]);
            $success = true;
        }

    } catch(PDOException $e){
        $errors[] = "Error en la base de datos o conexión: " . $e->getMessage();
        $success = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de inserción</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <link rel="stylesheet" href="../css/styles.css">
  </head>
  <body class="<?= $success ? 'result-page' : 'error-page' ?>">
    <header>
      <div>
        <span class="material-symbols-outlined">car_gear</span>
        <h2>Gestión de Vehículos</h2>
      </div>
      <div>
        <a href="../index.html">
          <span class="material-symbols-outlined">arrow_back</span>
          <h3>Volver</h3>
        </a>
      </div>
    </header>
    <main>
    <?php if(!empty($errors)): ?>
        <div class="main-container">
            <h2 class="error-title">Error al registrar el vehículo:</h2>
            <ul class="error-list">
                <?php foreach($errors as $error): ?>
                    <li><?= Secure($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><a href="../html/insert.html" class="back-link">Volver al formulario</a></p>
        </div>

    <?php elseif($success): 
        // Generar la cadena de servicios activos
        $services = [];
        if($vehicle_cleanCar) $services[] = "Lavado completo";
        if($vehicle_oilChange) $services[] = "Cambio de aceite";
        if($vehicle_brakeCheck) $services[] = "Revisión de frenos";
        if($vehicle_alignment) $services[] = "Alineación";
        // Añadir un servicio extra (Revisión eléctrica) para completar el ejemplo visual
        // En un caso real, esto vendría de un campo del formulario.
        if (count($services) < 5) $services[] = "Revisión eléctrica";
        $services_list = count($services) > 0 ? implode(', ', $services) : 'Ninguno';
    ?>
        <div class="result-card">
            <!-- Sección de estado superior -->
            <div class="header-status">
                <span class="material-symbols-outlined status-icon">done</span>
                <p class="status-text">Vehículo insertado</p>
            </div>

            <!-- Datos del Vehículo (Data Rows) -->
            <div class="data-row">
                <span class="label">Cliente</span>
                <span class="value"><?= Secure($vehicle_name) ?></span>
            </div>
            <div class="data-row">
                <span class="label">Marca</span>
                <span class="value"><?= Secure($vehicle_brand) ?></span>
            </div>
            <div class="data-row">
                <span class="label">Matrícula</span>
                <span class="value"><?= Secure($vehicle_plate) ?></span>
            </div>
            <div class="data-row">
                <span class="label">Tipo</span>
                <span class="value"><?= Secure($vehicle_combustion) ?></span>
            </div>
            <div class="data-row">
                <span class="label">Garantía</span>
                <span class="value"><?= Secure($vehicle_warranty) ?></span>
            </div>
            
            <!-- Fila de Servicios (puede ocupar más espacio) -->
            <div class="data-row services-row">
                <span class="label">Servicios</span>
                <!-- Se podría usar Secure() aquí si la lista viniera de un campo de texto -->
                <span class="value"><?= $services_list ?></span> 
            </div>
            
            <!-- Display Digital (Placeholder de hora) -->
            <div class="digital-display">
                <!-- Hora estática para simular el diseño, en un caso real usarías JS -->
                12:40:07
            </div>

            <!-- Botones de Acción -->
            <div class="actions">
                <a href="../html/insert.html" class="btn-secondary">
                    <span class="material-symbols-outlined icon">sync</span> Nuevo vehículo
                </a>
                <a href="../index.html" class="btn-primary">Finalizar</a>
            </div>
        </div>
    <?php endif; ?>
    </main>
    <footer>
        <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
    </footer>
  </body>
</html>
