
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de vehiculos</title>
    <link rel="stylesheet" href="../css/show.css">
</head>
<body>
    <h1>Lista de vehiculos</h1>
    <hr>
    <main>
    <?php
        require_once "connection.php";
        try{
            $conn = Connection();
            $sql = "SELECT * FROM vehiculo";
            $stmt = $conn->query($sql);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                ?>
                <div class="vehicleContainer">
                    <img class="vehicleImage" src="<?=$row['image']?>">
                    <div>
                        <div class="lineInfoOne">
                            <h2><?=$row['brand']. " - ".$row['plate']?></h2>
                            <p class="warranty"><?=$row['warranty'] == 1?"Con garantia" : "Sin garantia"?></p>
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
                        
                        <?php
                            /*
                            MATRICULA IMITANDO MATRICULA ESPAÑOLA
                            <div class="plate">
                                <img alt="Imagen del vehiculo" src="../images/Eplate.png">
                                <p><?=$row['plate']?></p>
                            </div>
                            */
                        ?>

                        </div>
                        <hr>
                        <h4 class="h4Additions">Servicios adicionales</h4>
                        <div class="additions">
                            <?=$row['cleanCar'] == 1?"<p>Limpieza de vehículo</p>" : ""?>
                            <?=$row['oilChange'] == 1?"<p>Cambio de aceite</p>" : ""?>
                            <?=$row['brakeCheck'] == 1?"<p>Revisión de frenos</p>" : ""?>
                            <?=$row['Alignment'] == 1?"<p>Alineamiento</p>" : ""?>

                        </div>
                    </div>
                </div>
                <?php
            }
        }catch(PDOException $ex){
            echo($ex);
        }
    ?>
    </main>
</body>
</html>