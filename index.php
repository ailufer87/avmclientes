<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (file_exists("archivo.txt")){
    $strJson = file_get_contents("archivo.txt"); //variable distinta a la que esta mas abajo
    $aClientes= json_decode($strJson, true);
} else{
    $aClientes= array ();
}

if (isset ($_GET ["id"])){
$id= $_GET ["id"];
} else{ 
    $id="";

}

if (isset ($_GET["do"]) && $_GET["do"]== "eliminar" ){
    unset($aClientes[$id]);
     //convertir el array de clientes en json 
     $strJson= json_encode($aClientes);

        
     //almacenar en un archivo con file_put_contents MODIFICADO
     file_put_contents("archivo.txt", $strJson);

     header("Location: index.php");
}

if ($_POST){

    $dni= $_POST ["txtDni"];
    $nombre=  $_POST ["txtNombre"];
    $telefono=  $_POST ["txtTel"];
    $correo=  $_POST ["txtCorreo"];
    $imagenNombre= "";

    if ($_FILES ["archivo"]["error"] === UPLOAD_ERR_OK ){
        $nombreAleatorio= date("Ymdhmsi").rand(1000,2000);
        $archivo_tmp= $_FILES ["archivo"]["tmp_name"];
        $extension= pathinfo($_FILES ["archivo"]["name"], PATHINFO_EXTENSION );
        if ($extension=="jpg" ||$extension=="png" ||$extension=="jpeg" ){
        $imagenNombre= "$nombreAleatorio.$extension";
        move_uploaded_file($archivo_tmp,"imagenes/$imagenNombre");
    }
    }


    if ($id >=0){
        //si no se subio img nueva y estamos editando conservar el $imagenNombre de la imagen anterior
        if ($_FILES ["archivo"]["error"] !== UPLOAD_ERR_OK ){
            $imagenNombre= $aClientes[$id]["imagen"];
        } else{
        //si viene una img nueva y hay una anterior, eliminar la anterior
            if (file_exists("imagenes/".$aClientes[$id]["imagen"])){
                unlink("imagenes/".$aClientes[$id]["imagen"]);
            }
        }

        // estoy editando
        $aClientes[$id]= array ( "dni" => $dni,
                        "nombre" => $nombre,
                        "telefono" => $telefono,
                        "correo"=> $correo,
                        "imagen"=> $imagenNombre  //va a perder la imagen a menos que la recupere
                    );
    } else{
        //estoy agregando nuevo
        $aClientes[]= array ( "dni" => $dni,
                        "nombre" => $nombre,
                        "telefono" => $telefono,
                        "correo"=> $correo,
                        "imagen"=> $imagenNombre 
                    );
    }


     //convertir el array de clientes en json 
    $strJson= json_encode($aClientes);

        
    //almacenar en un archivo con flie_put_contents
    file_put_contents("archivo.txt", $strJson);

    }

   

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABMclientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="CSS/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="CSS/estilos.css">
</head>

<body>
    <main class=container>
        <div class="row">
            <div class="col 12 text-center py-4">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col 6">
                    <form action="" method="POST" enctype="multipart/form-data" class="form">
                    <div>
                        <label for="txtDni">DNI:*</label>
                        <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : "";?>">
                    </div>
                    <div>
                        <label for="txtNombre">Nombre:*</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : "";?>">
                    </div>
                        <label for="txtTel">Telefono:*</label>
                        <input type="tel" name="txtTel" id="txtTel" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : "";?>">
                    <div>
                        <label for="">Correo:*</label>
                        <input type="email" name="txtCorreo" id="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : "";?>">
                    </div>
                    <div>
                        <p class=pt-3>Archivo adjunto: <input type="file" name= "archivo" id="archivo" accept=".jpg , .jpeg , .png" ></p>
                        <p>Archivos admitidos: .jpg, .jpeg, .png</p>
                    </div>
                    <div>
                        <button type="submit"  class="btn btn-primary mt-3">Guardar</button>
                        <a href="index.php"  class="btn btn-danger mt-3">Nuevo </a>
                    </div>

                </form>
            </div>



            <div class="col 6">
                <table class="table table-hover border">
                    <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aClientes as $pos => $cliente){ ?>
                        <tr>
                        <td><img src="imagenes/<?php echo $cliente["imagen"];?>" class="img-thumbnail" alt="img subida"></td>
                        <td><?php echo $cliente["dni"]; ?></td>
                        <td><?php echo $cliente["nombre"]; ?></td>
                        <td><?php echo $cliente["correo"]; ?></td>
                        <td>
                           <a href="?id=<?php echo $pos; ?>"> <i class="fa-solid fa-pen-to-square"></i></a>
                           <a href="?id=<?php echo $pos; ?>&do=eliminar"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                        </tr>
                        <?php }; ?>
                    </tbody>

                </table>
            </div>
        </div>



    </main>

</body>

</html>