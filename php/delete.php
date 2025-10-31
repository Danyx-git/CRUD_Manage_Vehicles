<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de vehiculos</title>
    <link rel="stylesheet" href="../css/styles.css">
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
        <table class="deleteTable">
            <tr>
                <th>Cliente</th>
                <th>Marca</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th>Imagen</th>
                <th>Accion</th>
            </tr>
            <?php
                require_once "connection.php";
                try{
                    $conn = Connection();
                    $sql = "SELECT * FROM vehicles";
                    $stmt = $conn->query($sql);
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <tr>
                            <td><?=$row['clientName']?></td>
                            <td><?=$row['brand']?></td>
                            <td><?=$row['plate']?></td>
                            <td><?=$row['combustion']?></td>
                            <td><img width="80px" src="<?=$row['image']?>"></td>
                            <td><a>Eliminar</a></td>
                        </tr>
                        <?php
                    }
                }catch(PDOException $ex){
                    echo($ex);
                }
            ?>
        </table>
    </main>
    <footer>
        <p>2ºDAW Servidor - Gestión CRUD de Vehículos - &copy;2025</p>
    </footer>
</body>
</html>