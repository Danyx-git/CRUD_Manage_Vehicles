<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seleccionar vehículo para modificar</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/modify.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
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

<main>
  <div class="modify-container">
    <h3>Seleccione el vehículo a modificar</h3>

    <?php
    require_once "connection.php";
    $conn = Connection();

    // Traer todos los vehículos
    $sql = "SELECT * FROM vehicles ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($vehicles) > 0) {
        echo '<table class="modify-table">';
        echo '<thead>
                <tr>
                  <th>Imagen</th>
                  <th>Cliente</th>
                  <th>Marca</th>
                  <th>Matrícula</th>
                  <th>Combustión</th>
                  <th>Garantía</th>
                  <th>Servicios adicionales</th>
                  <th>Acción</th>
                </tr>
              </thead>';
        echo '<tbody>';

        foreach ($vehicles as $vehicle) {
            echo '<tr>';
            
            // Imagen
            if (!empty($vehicle['image']) && file_exists($vehicle['image'])) {
                echo '<td><img src="' . htmlspecialchars($vehicle['image']) . '" alt="Vehículo" class="thumb"></td>';
            } else {
                echo '<td><span class="no-image">Sin imagen</span></td>';
            }

            // Datos principales
            echo '<td>' . htmlspecialchars($vehicle['clientName']) . '</td>';
            echo '<td>' . htmlspecialchars($vehicle['brand']) . '</td>';
            echo '<td>' . htmlspecialchars($vehicle['plate']) . '</td>';
            echo '<td>' . htmlspecialchars($vehicle['combustion']) . '</td>';

            // Garantía
            if ($vehicle['warranty']) {
                echo '<td><span class="garantia-tag">Sí</span></td>';
            } else {
                echo '<td><span class="garantia-tag no">No</span></td>';
            }

            // Servicios adicionales
            $services = [];
            if ($vehicle['cleanCar']) $services[] = "Limpieza";
            if ($vehicle['oilChange']) $services[] = "Aceite";
            if ($vehicle['brakeCheck']) $services[] = "Frenos";
            if ($vehicle['alignment']) $services[] = "Alineación";

            echo '<td>' . (!empty($services) ? implode(', ', $services) : 'Ninguno') . '</td>';

            // Botón de modificación
            echo '<td>
                    <a href="modify.php?id=' . $vehicle['id'] . '" class="modify-btn">
                      <span class="material-symbols-outlined">edit</span>
                      Modificar
                    </a>
                  </td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo "<p>No hay vehículos registrados.</p>";
    }
    ?>
  </div>
</main>

<footer>
  <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
</footer>
</body>
</html>
