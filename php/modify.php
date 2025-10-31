<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modificar vehículo</title>
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
    <a href="../index.html">
      <span class="material-symbols-outlined">arrow_back</span>
      <h3>Volver</h3>
    </a>
  </div>
</header>

<main class="modifyMain">
  <h3>Modificar vehículo</h3>

  <?php
  require_once "connection.php";
  $conn = Connection();

  // Actualizar vehículo si el formulario fue enviado
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $id = $_POST['id'];
      $vehicle_name = trim($_POST['vehicle_name']);
      $vehicle_brand = trim($_POST['vehicle_brand']);
      $vehicle_plate = trim($_POST['vehicle_plate']);
      $vehicle_combustion = trim($_POST['vehicle_combustion']);
      $vehicle_warranty = ($_POST['vehicle_warranty'] ?? 'No') === 'Sí' ? 1 : 0;

      // Servicios adicionales
      $vehicle_cleanCar = isset($_POST['vehicle_addition1']) ? 1 : 0;
      $vehicle_oilChange = isset($_POST['vehicle_addition2']) ? 1 : 0;
      $vehicle_brakeCheck = isset($_POST['vehicle_addition3']) ? 1 : 0;
      $vehicle_alignment = isset($_POST['vehicle_addition4']) ? 1 : 0;

      // Imagen (opcional)
      $image_path = null;
      if (!empty($_FILES['vehicle_image']['name'])) {
          if ($_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK) {
              $tmp = $_FILES['vehicle_image']['tmp_name'];
              $name = basename($_FILES['vehicle_image']['name']);
              if (!file_exists("uploads")) mkdir("uploads", 0777, true);
              $image_path = "uploads/" . uniqid() . "_" . $name;
              move_uploaded_file($tmp, $image_path);
          }
      }

      // Actualizar registro
      $sql = "UPDATE vehicles 
              SET clientName=?, brand=?, plate=?, combustion=?, warranty=?, 
                  cleanCar=?, oilChange=?, brakeCheck=?, alignment=?" .
              ($image_path ? ", image=?" : "") . 
              " WHERE id=?";
      $params = [
          $vehicle_name, $vehicle_brand, $vehicle_plate,
          $vehicle_combustion, $vehicle_warranty,
          $vehicle_cleanCar, $vehicle_oilChange,
          $vehicle_brakeCheck, $vehicle_alignment
      ];
      if ($image_path) $params[] = $image_path;
      $params[] = $id;

      $stmt = $conn->prepare($sql);
      $stmt->execute($params);

      echo '
            <div class="success-container">
              <div class="success-card">
                <span class="material-symbols-outlined success-icon">check_circle</span>
                <h1>¡Vehículo actualizado correctamente!</h1>
                <p>Los cambios se han guardado con éxito en la base de datos.</p>
                <a href="selectModify.php" class="btn-return">
                  <span class="material-symbols-outlined">arrow_back</span>
                  Volver al listado
                </a>
              </div>
            </div>

            <style>
              .success-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 70vh;
                animation: fadeIn 0.5s ease-in-out;
              }

              .success-card {
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                padding: 2.5rem 3rem;
                text-align: center;
                max-width: 450px;
                animation: slideUp 0.6s ease-in-out;
              }

              .success-icon {
                color: #4caf50;
                font-size: 4rem;
                margin-bottom: 1rem;
                animation: pop 0.4s ease-in-out;
              }

              .success-card h1 {
                color: #333;
                margin-bottom: 0.5rem;
              }

              .success-card p {
                color: #555;
                font-size: 1.1rem;
                margin-bottom: 1.8rem;
              }

              .btn-return {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                background: #4caf50;
                color: white;
                padding: 0.7rem 1.3rem;
                border-radius: 10px;
                font-weight: bold;
                transition: background 0.3s ease;
              }

              .btn-return:hover {
                background: #43a047;
              }

              @keyframes fadeIn {
                from { opacity: 0; } to { opacity: 1; }
              }

              @keyframes slideUp {
                from { transform: translateY(30px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
              }

              @keyframes pop {
                0% { transform: scale(0.8); opacity: 0; }
                100% { transform: scale(1); opacity: 1; }
              }
            </style>
            ';

      }
  // Mostrar formulario si hay ID válido
  if (isset($_GET['id'])) {
      $id = $_GET['id'];
      $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id=?");
      $stmt->execute([$id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row):
  ?>
  <form class="insert-form" action="modify.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

    <!-- Nombre -->
    <div>
      <label for="vehicle_name">Nombre:</label>
      <input type="text" id="vehicle_name" name="vehicle_name" maxlength="150" 
             value="<?= htmlspecialchars($row['clientName']) ?>" required>
    </div>

    <!-- Marca -->
    <div>
      <label for="vehicle_brand">Marca:</label>
      <select id="vehicle_brand" name="vehicle_brand" required>
        <option value="" hidden>--Seleccione una marca--</option>
        <?php
          $brands = ["Toyota","Ford","Chevrolet","Honda","Nissan","Volkswagen","Hyundai",
                     "Kia","Mazda","Subaru","Jeep","Tesla","BMW","Mercedes-Benz",
                     "Audi","Porsche","Volvo","Lexus","Ferrari","Lamborghini"];
          foreach ($brands as $brand) {
              $selected = ($row['brand'] === $brand) ? "selected" : "";
              echo "<option value='$brand' $selected>$brand</option>";
          }
        ?>
      </select>
    </div>

    <div id="plate-combustion-container">
      <!-- Matrícula -->
      <div>
        <label for="vehicle_plate">Matrícula:</label>
        <input type="text" id="vehicle_plate" name="vehicle_plate" 
               value="<?= htmlspecialchars($row['plate']) ?>" required>
      </div>

      <!-- Tipo de combustible -->
      <div>
        <label for="vehicle_combustion">Combustión:</label>
        <select id="vehicle_combustion" name="vehicle_combustion" required>
          <?php
            $combustiones = ["Gasolina", "Diesel", "Híbrido", "Eléctrico"];
            foreach ($combustiones as $tipo) {
                $sel = ($row['combustion'] === $tipo) ? "selected" : "";
                echo "<option value='$tipo' $sel>$tipo</option>";
            }
          ?>
        </select>
      </div>
    </div>

    <!-- En garantía -->
    <div id="warranty-container">
      <label>¿En garantía?</label>
      <label><input type="radio" name="vehicle_warranty" value="Sí" <?= $row['warranty'] ? 'checked' : '' ?>> Sí</label>
      <label><input type="radio" name="vehicle_warranty" value="No" <?= !$row['warranty'] ? 'checked' : '' ?>> No</label>
    </div>

    <!-- Servicios adicionales -->
    <div>
      <label>Servicios adicionales:</label>
      <label><input type="checkbox" name="vehicle_addition1" <?= $row['cleanCar'] ? 'checked' : '' ?>> Lavado completo</label>
      <label><input type="checkbox" name="vehicle_addition2" <?= $row['oilChange'] ? 'checked' : '' ?>> Cambio de aceite</label>
      <label><input type="checkbox" name="vehicle_addition3" <?= $row['brakeCheck'] ? 'checked' : '' ?>> Revisión de frenos</label>
      <label><input type="checkbox" name="vehicle_addition4" <?= $row['alignment'] ? 'checked' : '' ?>> Alineación</label>
    </div>

    <!-- Imagen actual y nueva -->
    <div>
      <?php if (!empty($row['image'])): ?>
        <p>Imagen actual:</p>
        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Imagen del vehículo" width="150">
      <?php endif; ?>
      <label for="vehicle_image" class="file-label">
        <span class="material-symbols-outlined">upload</span>
        <span class="file-label-text">Haz clic o arrastra una nueva imagen aquí</span>
        <span class="file-name"></span>
      </label>
      <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*">
    </div>

    <!-- Botón -->
    <input type="submit" value="Guardar cambios">
  </form>
  <?php 
      else:
          echo "<p>No se encontró el vehículo.</p>";
      endif;
  } else {
      echo "<p>No se ha seleccionado ningún vehículo.</p>";
  }
  ?>
</main>

<footer>
  <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
</footer>

<script>
  const fileInput = document.getElementById("vehicle_image");
  const fileName = document.querySelector(".file-label .file-name");
  fileInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    fileName.textContent = file ? file.name : "";
  });
</script>
</body>
</html>
