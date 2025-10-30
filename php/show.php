
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
                    <img src="<?=$row['image']?>">
                    <h1><?=$row['clientName']?></h1>
                    <p><?=$row['brand']?></p>
                    <p><?=$row['combustion']?></p>
                    <div class="plate">
                        <img alt="Imagen del vehiculo" src="../images/Eplate.png">
                        <p><?=$row['plate']?></p>
                    </div>
                    <p><?=$row['warranty']?></p>
                    <p><?=$row['cleanCar']?></p>
                    <p><?=$row['tracking']?></p>
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