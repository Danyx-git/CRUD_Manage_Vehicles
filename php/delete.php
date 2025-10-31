<?php
require_once "connection.php";
$conn = Connection();

// Si se confirma la eliminación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"])) {
    $id = intval($_POST["id"]);

    // Buscar la imagen
    $stmt = $conn->prepare("SELECT image FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        // Eliminar el vehículo
        $delete = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
        $delete->execute([$id]);

        // Eliminar imagen si existe
        if (!empty($vehicle["image"]) && file_exists($vehicle["image"])) {
            unlink($vehicle["image"]);
        }
    }

    // Mostrar pantalla de éxito
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Vehículo eliminado</title>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
      <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
      <header>
        <div>
          <span class="material-symbols-outlined">car_gear</span>
          <h2>Gestión de Vehículos</h2>
        </div>
        <div>
          <a href="./selectDelete.php">
            <span class="material-symbols-outlined">arrow_back</span>
            <h3>Volver</h3>
          </a>
        </div>
      </header>

      <div class="success-container">
        <div class="success-card">
          <span class="material-symbols-outlined success-icon">delete_forever</span>
          <h1>Vehículo eliminado correctamente</h1>
          <p>El vehículo ha sido eliminado de la base de datos.</p>
          <a href="./selectDelete.php" class="btn-return">
            <span class="material-symbols-outlined">arrow_back</span>
            Volver al listado
          </a>
        </div>
      </div>

      <footer>
        <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
      </footer>

      <style>
        .success-container {
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 70vh;
          animation: fadeIn 0.5s ease-in-out;
        }
        .success-card {
          background: #fff;
          border-radius: 20px;
          box-shadow: 0 8px 25px rgba(0,0,0,0.1);
          padding: 2.5rem 3rem;
          text-align: center;
          max-width: 450px;
          animation: slideUp 0.6s ease-in-out;
        }
        .success-icon {
          color: #e53935;
          font-size: 4rem;
          margin-bottom: 1rem;
          animation: pop 0.4s ease-in-out;
        }
        .success-card h1 { color: #333; margin-bottom: 0.5rem; }
        .success-card p { color: #555; font-size: 1.1rem; margin-bottom: 1.8rem; }
        .btn-return {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          text-decoration: none;
          background: #e53935;
          color: white;
          padding: 0.7rem 1.3rem;
          border-radius: 10px;
          font-weight: bold;
          transition: background 0.3s ease;
        }
        .btn-return:hover { background: #c62828; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes pop { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
      </style>
    </body>
    </html>';
    exit;
}

// Si se accede con ?id=
if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row):
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eliminar vehículo</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header>
    <div>
      <span class="material-symbols-outlined">car_gear</span>
      <h2>Gestión de Vehículos</h2>
    </div>
    <div>
      <a href="./selectDelete.php">
        <span class="material-symbols-outlined">arrow_back</span>
        <h3>Volver</h3>
      </a>
    </div>
  </header>

  <h3 class="h3Listado">¿Seguro que deseas eliminar este vehículo?</h3>

  <main>
    <!-- ✅ ESTRUCTURA ORIGINAL INTACTA -->
    <div class="vehicleContainer">
        <img class="vehicleImage" src="<?=$row['image']?>">
        <div>
            <div class="lineInfoOne">
                <h2><?=$row['brand']. " - ".$row['plate']?></h2>
                <p class="warranty"><?=$row['warranty'] == 1?"Con garantia" : ""?></p>
                <p class="combustion"><?=$row['combustion']?></p>
            </div>
            <div class="mainInfo">
                <div class="lineInfoStandart">
                    <p class="lightGray">Cliente</p>
                    <p><?=$row['clientName']?></p>
                </div>
                <div class="lineInfoStandart">
                    <p class="lightGray">Marca</p>
                    <p><?=$row['brand']?></p>
                </div>
                <div class="lineInfoStandart">
                    <p class="lightGray">Matrícula</p>
                    <p><?=$row['plate']?></p>
                </div>
                <div class="lineInfoStandart">
                    <p class="lightGray">Tipo</p>
                    <p><?=$row['combustion']?></p>
                </div>
            </div>
            <hr>
            <h4 class="h4Additions">Servicios adicionales</h4>
            <div class="additions">
                <?=$row['cleanCar'] == 1?"<p>Limpieza de vehículo</p>" : ""?>
                <?=$row['oilChange'] == 1?"<p>Cambio de aceite</p>" : ""?>
                <?=$row['brakeCheck'] == 1?"<p>Revisión de frenos</p>" : ""?>
                <?=$row['alignment'] == 1?"<p>Alineamiento</p>" : ""?>
            </div>
        </div>
    </div>

    <!-- 🔽 BOTONES DE ACCIÓN FUERA DEL VEHÍCULO -->
    <div class="delete-actions">
      <form action="delete.php" method="POST">
        <input type="hidden" name="id" value="<?=$row['id']?>">
        <button type="submit" name="confirm_delete" class="btn-delete">
          <span class="material-symbols-outlined">delete_forever</span> Eliminar definitivamente
        </button>
        <a href="./selectDelete.php" class="btn-cancel">
          <span class="material-symbols-outlined">cancel</span> Cancelar
        </a>
      </form>
    </div>
  </main>

  <footer>
    <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
  </footer>

  <style>
    .delete-actions {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn-delete, .btn-cancel {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: none;
      padding: 0.7rem 1.3rem;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .btn-delete {
      background: #e53935;
      color: white;
    }

    .btn-delete:hover {
      background: #c62828;
    }

    .btn-cancel {
      background: #757575;
      color: white;
    }

    .btn-cancel:hover {
      background: #616161;
    }
  </style>
</body>
</html>
<?php
    else:
        echo "<p>No se encontró el vehículo.</p>";
    endif;
} else {
    echo "<p>No se ha seleccionado ningún vehículo.</p>";
}
?>
