<?php
if (!isset($_GET['cp']))
die('Argumentos Invalidos');

$resultado = array(
  "colonias" => array(),
  "municipio" => NULL,
  "ciudad" => NULL,
  "estado" => NULL,
  "cp" => NULL
);

$mysqli = new mysqli("127.0.0.1","root","","sepomex");
$sql = 'SELECT * FROM cp WHERE cp = ?';
$query = $mysqli->prepare($sql);
$query->bind_param("s",$_GET['cp']);
$query->execute();
$res = $query->get_result();
if ($res->num_rows == 0)
die('No existe el código postal proporcionado.');
while($row = $res->fetch_assoc()){
  $resultado['colonias'][count($resultado['colonias'])] = $row['colonia'];
  $resultado['municipio'] = $row['localidad'];
  $resultado['ciudad'] = $row['ciudad'];
  $resultado['estado'] = $row['estado'];
  $resultado['cp'] = $row['cp'];
}
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($resultado);
