<?php
define('TEMP',sys_get_temp_dir() . '/');
ini_set('mysql.connect_timeout', 1000);
ini_set('default_socket_timeout', 1000);

$URL = 'http://www.correosdemexico.gob.mx/lservicios/servicios/CodigoPostal_Exportar.aspx';
$request = array(
  '__VIEWSTATE' => '/wEPDwUKMTIxMDU0NDIwMA9kFgICAQ9kFgICAQ9kFgYCAw8PFgIeBFRleHQFOsOabHRpbWEgQWN0dWFsaXphY2nDs24gZGUgSW5mb3JtYWNpw7NuOiBPY3R1YnJlIDEyIGRlIDIwMTVkZAIHDxAPFgYeDURhdGFUZXh0RmllbGQFA0Vkbx4ORGF0YVZhbHVlRmllbGQFBUlkRWRvHgtfIURhdGFCb3VuZGdkEBUhIy0tLS0tLS0tLS0gVCAgbyAgZCAgbyAgcyAtLS0tLS0tLS0tDkFndWFzY2FsaWVudGVzD0JhamEgQ2FsaWZvcm5pYRNCYWphIENhbGlmb3JuaWEgU3VyCENhbXBlY2hlFENvYWh1aWxhIGRlIFphcmFnb3phBkNvbGltYQdDaGlhcGFzCUNoaWh1YWh1YRBEaXN0cml0byBGZWRlcmFsB0R1cmFuZ28KR3VhbmFqdWF0bwhHdWVycmVybwdIaWRhbGdvB0phbGlzY28HTcOpeGljbxRNaWNob2Fjw6FuIGRlIE9jYW1wbwdNb3JlbG9zB05heWFyaXQLTnVldm8gTGXDs24GT2F4YWNhBlB1ZWJsYQpRdWVyw6l0YXJvDFF1aW50YW5hIFJvbxBTYW4gTHVpcyBQb3Rvc8OtB1NpbmFsb2EGU29ub3JhB1RhYmFzY28KVGFtYXVsaXBhcwhUbGF4Y2FsYR9WZXJhY3J1eiBkZSBJZ25hY2lvIGRlIGxhIExsYXZlCFl1Y2F0w6FuCVphY2F0ZWNhcxUhAjAwAjAxAjAyAjAzAjA0AjA1AjA2AjA3AjA4AjA5AjEwAjExAjEyAjEzAjE0AjE1AjE2AjE3AjE4AjE5AjIwAjIxAjIyAjIzAjI0AjI1AjI2AjI3AjI4AjI5AjMwAjMxAjMyFCsDIWdnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2RkAh0PPCsACwBkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBQtidG5EZXNjYXJnYW7mKf2QWdV7ACF9fcRwj17tjpW4',
  '__EVENTVALIDATION' => '/wEWKAKgn7qiAQLG/OLvBgLWk4iCCgLWk4SCCgLWk4CCCgLWk7yCCgLWk7iCCgLWk7SCCgLWk7CCCgLWk6yCCgLWk+iBCgLWk+SBCgLJk4iCCgLJk4SCCgLJk4CCCgLJk7yCCgLJk7iCCgLJk7SCCgLJk7CCCgLJk6yCCgLJk+iBCgLJk+SBCgLIk4iCCgLIk4SCCgLIk4CCCgLIk7yCCgLIk7iCCgLIk7SCCgLIk7CCCgLIk6yCCgLIk+iBCgLIk+SBCgLLk4iCCgLLk4SCCgLLk4CCCgLL+uTWBALa4Za4AgK+qOyRAQLI56b6CwL1/KjtBePsl9sOAa2kuhQy2NGiYZah6Oiv',
  'cboEdo' => '00',
  'rblTipo' => 'txt',
  'btnDescarga.x' => '44',
  'btnDescarga.y' => '10'
);

$options = array(
  'http' => array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($request)
  )
);
$context  = stream_context_create($options);
$file = @file_get_contents($URL, 0, $context);

if (file_exists(TEMP . 'CP.zip'))
unlink(TEMP . 'CP.zip');

if (file_put_contents(TEMP . 'CP.zip',$file)){
  $zip = new ZipArchive;
  $res = $zip->open(TEMP . 'CP.zip');
  if ($res === TRUE) {
    $zip->extractTo(TEMP);
    $zip->close();
    if (!file_exists(TEMP . 'CPdescarga.txt'))
    die('El servidor de correos de méxico ha entregado un archivo no válido o inesperado.');
    $file = file(TEMP . 'CPdescarga.txt');
  }else{
    die('El servidor de correos de méxico ha entregado un archivo no válido o inesperado.');
  }
}

if (!is_array($file))
die('No hay conexión al servidor de Correos de México.');

$sql = 'TRUNCATE cp;' . "\n";
$sql .= 'INSERT INTO cp (cp,colonia,localidad,ciudad,estado) VALUES ' . "\n";
$mysqli = new mysqli('127.0.0.1','root','','sepomex') or die($mysqli->error);

for ($i = 2; $i < count($file); $i++){
  if ($i != 2)
  $sql .= ",\n";
  $cp = explode('|',$file[$i]);
  if (count($cp) == 0 || count($cp) < 13)
  die('El archivo introducido no es válido.');
  for($o = 0; $o < count($cp); $o++){
    if (strlen($cp[$o]) == 0)
    $cp[$o] = 'NO REGISTRADO(A)';
    $cp[$o] = strtoupper(limpiar(utf8_encode($cp[$o])));
  }
  $codigo = array(
    "cp" => $cp[0], //<-- cp
    "colonia" => $cp[1], //<-- colonia
    "localidad" => $cp[3], //<-- localidad
    "ciudad" => $cp[5], //<-- ciudad
    "estado" => $cp[4]  //<-- estado
  );
  $sql .= "({$codigo['cp']}, '{$codigo['colonia']}', '{$codigo['localidad']}', '{$codigo['ciudad']}', '{$codigo['estado']}')";
}

$mysqli->multi_query($sql) or die($mysqli->error);
$mysqli->close(); #cerrar conexion
#Limpieza final
if (file_exists(TEMP . 'CP.zip'))
unlink(TEMP . 'CP.zip');
if (file_exists(TEMP . 'CPdescarga.txt'))
unlink(TEMP . 'CPdescarga.txt');
echo '1';

function limpiar($string){
  //remover BOM
  $bom = pack('H*','EFBBBF');
  $string = preg_replace("/^$bom/", '', $string);
  //REMPLAZAR ACENTOS
  $string = trim($string);

  $string = str_replace(
    array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
    array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
    $string
  );

  $string = str_replace(
    array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
    array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
    $string
  );

  $string = str_replace(
    array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
    array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
    $string
  );

  $string = str_replace(
    array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
    array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
    $string
  );

  $string = str_replace(
    array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
    array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
    $string
  );

  $string = str_replace(
    array('ñ', 'Ñ', 'ç', 'Ç'),
    array('n', 'N', 'c', 'C',),
    $string
  );

  $string = str_replace(array(',','"',"'"),'',$string);

  return $string;
}
