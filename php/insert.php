<?php
// Ajusta a true solo para depuración local. En producción pon false.
define('DEBUG', false);

require_once "connection.php";

if (!function_exists('Secure')) {
    function Secure($data) {
        return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$vehicle_name       = trim($_POST['vehicle_name'] ?? '');
$vehicle_brand      = trim($_POST['vehicle_brand'] ?? '');
$vehicle_plate      = trim($_POST['vehicle_plate'] ?? '');
$vehicle_combustion = trim($_POST['vehicle_combustion'] ?? '');
$vehicle_warranty   = (isset($_POST['vehicle_warranty']) && $_POST['vehicle_warranty'] === 'Sí') ? 1 : 0;

// LOS NOMBRES aquí deben coincidir exactamente con los del formulario (value="1")
$vehicle_cleanCar   = isset($_POST['vehicle_cleanCar']) ? 1 : 0;
$vehicle_oilChange  = isset($_POST['vehicle_oilChange']) ? 1 : 0;
$vehicle_brakeCheck = isset($_POST['vehicle_brakeCheck']) ? 1 : 0;
$vehicle_alignment  = isset($_POST['vehicle_alignment']) ? 1 : 0;

$errors = [];

// Validaciones básicas
if ($vehicle_name === '')  $errors[] = "El nombre del cliente es obligatorio.";
if ($vehicle_brand === '') $errors[] = "La marca es obligatoria.";
if ($vehicle_plate === '') $errors[] = "La matrícula es obligatoria.";

// Procesar imagen si existe
$image_path = null;
if (!empty($_FILES['vehicle_image']['name'])) {
    if ($_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['vehicle_image']['tmp_name'];
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['vehicle_image']['name']));
        if (!file_exists(__DIR__ . "/uploads")) mkdir(__DIR__ . "/uploads", 0777, true);
        $image_path = "uploads/" . uniqid() . "_" . $name;
        if (!move_uploaded_file($tmp, __DIR__ . "/" . $image_path)) {
            $errors[] = "Error al guardar la imagen.";
            $image_path = null;
        }
    } else {
        $errors[] = "Error en la carga de imagen (código: {$_FILES['vehicle_image']['error']}).";
    }
}

$success = false;

try {
    if (empty($errors)) {
        $pdo = Connection();
        // Aseguramos que la conexión lance excepciones
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // DEBUG: ver qué llega al servidor
        if (DEBUG) {
            echo "<pre>_POST:\n"; print_r($_POST); echo "</pre>";
        }

        // Verificar matrícula duplicada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE plate = ?");
        $stmt->execute([$vehicle_plate]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "La matrícula '" . Secure($vehicle_plate) . "' ya está registrada.";
        } else {
            // Preparar INSERT con binding por posición, forzando tipos en execute
            $sql = "INSERT INTO vehicles
                (clientName, brand, plate, combustion, warranty, cleanCar, oilChange, brakeCheck, alignment, image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            $params = [
                $vehicle_name,
                $vehicle_brand,
                $vehicle_plate,
                $vehicle_combustion,
                (int)$vehicle_warranty,
                (int)$vehicle_cleanCar,
                (int)$vehicle_oilChange,
                (int)$vehicle_brakeCheck,
                (int)$vehicle_alignment,
                $image_path
            ];

            $ok = $stmt->execute($params);

            if (!$ok) {
                $err = $stmt->errorInfo();
                throw new Exception("SQL Error: " . implode(" | ", $err));
            }

            $success = true;
        }
    }
} catch (Exception $e) {
    // Añadir mensaje al array de errores y, si DEBUG, mostrar stack
    $errors[] = "Error en la base de datos: " . $e->getMessage();
    if (DEBUG) {
        echo "<pre>Exception:\n" . $e->__toString() . "</pre>";
    }
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Resultado de inserción</title>
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body class="<?= $success ? 'result-page' : 'error-page' ?>">
<header>
  <div>
    <span class="material-symbols-outlined">car_gear</span>
    <h2>Gestión de Vehículos</h2>
  </div>
  <div>
    <a href="../html/insert.html">
      <span class="material-symbols-outlined">arrow_back</span>
      <h3>Volver</h3>
    </a>
  </div>
</header>
<main>
<?php if (!empty($errors)): ?>
  <div class="main-container">
    <h2 class="error-title">Error al registrar el vehículo</h2>
    <ul class="error-list">
      <?php foreach ($errors as $err): ?>
        <li><?= Secure($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php elseif ($success): ?>
  <div class="result-card">
    <div class="header-status">
      <span class="material-symbols-outlined status-icon">done</span>
      <p class="status-text">Vehículo insertado correctamente</p>
    </div>

    <div class="data-row"><span class="label">Cliente</span><span class="value"><?= Secure($vehicle_name) ?></span></div>
    <div class="data-row"><span class="label">Marca</span><span class="value"><?= Secure($vehicle_brand) ?></span></div>
    <div class="data-row"><span class="label">Matrícula</span><span class="value"><?= Secure($vehicle_plate) ?></span></div>
    <div class="data-row"><span class="label">Combustión</span><span class="value"><?= Secure($vehicle_combustion) ?></span></div>
    <div class="data-row"><span class="label">Garantía</span><span class="value"><?= $vehicle_warranty ? 'Sí' : 'No' ?></span></div>

    <div class="data-row services-row">
      <span class="label">Servicios</span>
      <span class="value">
        <?= implode(', ', array_filter([
            $vehicle_cleanCar ? 'Lavado' : '',
            $vehicle_oilChange ? 'Cambio de aceite' : '',
            $vehicle_brakeCheck ? 'Revisión de frenos' : '',
            $vehicle_alignment ? 'Alineación' : ''
        ])) ?: 'Ninguno' ?>
      </span>
    </div>

    <?php if ($image_path): ?>
    <div class="data-row image-row">
      <span class="label">Imagen</span>
      <img src="<?= Secure($image_path) ?>" alt="Imagen del vehículo" class="vehicle-image-preview">
    </div>
    <?php endif; ?>

    <div class="actions">
      <a href="../html/insert.html" class="btn-secondary">
        <span class="material-symbols-outlined">add</span> Nuevo vehículo
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
