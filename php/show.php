
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de vehiculos</title>
</head>
<body>
    <h1>Lista de vehiculos</h1>
    <?php
        require_once "connection.php";
        try{
            $conn = Connection();
            $sql = "SELECT * FROM vehiculo";
            $stmt = $conn->query($sql);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                ?>
                <div name=divVehicle>
                    <h1></h1>
                </div>
                <?php
            }
        }catch(PDOException $ex){
            echo($ex);
        }
    ?>
</body>
</html>