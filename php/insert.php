<?php
  $vehicle_name = trim(filter_input(INPUT_POST, "vehicle_name", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
  $vehicle_brand = trim(filter_input(INPUT_POST, "vehicle_brand", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
  $vehicle_plate = trim(filter_input(INPUT_POST, "vehicle_plate", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
  $vehicle_combustion = trim(filter_input(INPUT_POST, "vehicle_combustion", FILTER_SANITIZE_SPECIAL_CHARS)) ?: "";
  $vehicle_warranty = filter_input(INPUT_POST, "vehicle_warranty", FILTER_SANITIZE_SPECIAL_CHARS) ?: "No";
  $vehicle_addition1 = filter_input(INPUT_POST, "vehicle_addition1", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
  $vehicle_addition2 = filter_input(INPUT_POST, "vehicle_addition2", FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;

  $errors = [];

  if(empty($vehicle_name)){$errors[] = "Por favor, introduce un nombre válido para el vehículo.";}
  if(empty($vehicle_brand)){$errors[] = "Selecciona una marca válida del listado disponible.";}
  if(empty($vehicle_plate)){$errors[] = "Introduce una matrícula válida del vehículo.";} 
  else{
    $pattern_modern = '/^[0-9]{4}[BCDFGHJKLMNPRSTVWXYZ]{3}$/i';
    if(!preg_match($pattern_modern, $vehicle_plate)){$errors[] = "La matrícula introducida no tiene un formato válido. Ejemplo válido: 1234BCD.";}
  }
  if (empty($vehicle_combustion)){$errors[] = "Selecciona el tipo de combustible correspondiente.";}
  if (empty($vehicle_warranty)){$errors[] = "Indica si el vehículo cuenta con garantía o no.";}
  if(isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK){
    $tmp_image = $_FILES['vehicle_image']['tmp_name'];
    $name_image = $_FILES['vehicle_image']['name'];
    $path = "uploads/".uniqid()."_".$name_image;

    if(!file_exists("uploads")){mkdir("uploads",0777,true);}
    if(!move_uploaded_file($tmp_image,$path)){$errors[] = "No se ha podido mover la imagen.";}
  }
  else{
    $path = null;
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inserción del vehículo</title>
</head>
<body>
  <?php if(empty($errors)):?>
    
  <?php else:?>
  <?php endif;?>
</body>
</html>