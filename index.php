<?php
//curl -X GET -u :5d82061c073c52d46820b60d79cb0816a5c16e54 https://api.github.com/user/repos --> Consulta para comprobar nuestro TOKEN
include_once './class/curl.class.php';

$curl = new Curl(); // Creamos un objeto Curl
$token = "15ae0c9babd7a901da929b1950d32b84a0d81baf"; // Creamos nuestro TOKEN que hemos creado en GitHub
if (!isset($_REQUEST['repository'])) { // si no nos a entrado una peticion con indice repository 
    repositorios($curl, $token); // Vemos todos los repositorios 
} else { // Si no vemos todos los commits del repositorio
    commits($curl, $_REQUEST['repository'], "xlfelip", $token);
}

function repositorios($curl, $token) {
    $info = ($curl->get("https://api.github.com/user/repos", $token)); // Hacemos un GET para nuestros repositorios
    $info = json_decode($info); // Lo transformamos a JSON 
    echo "<h1>Repositorios</h1>";
    foreach ($info as $value) { // Sacara todos los respositorios que tengamos
        if ($value->owner->login == "xlfelip") { // Solo saldrán los creados por nosotros
            echo '<div class="repo" > <a href="' . $_SERVER['PHP_SELF'] . '?repository=' . $value->name . '">'; 
            echo "<h4><b>$value->name</b></h4>  ";
            if ($value->private) {
                echo "Repositorio: Privado <br />";
            } else
                echo " Repositorio: Publico <br />";
            echo "Propietario: " . $value->owner->login . " <br />";
            echo "Creado: " . $value->created_at . "<br />";
            echo "Ultimo update: " . $value->updated_at . '<br /><br />';

            echo '</a></div>';
        }
    }
}

function commits($curl, $repo, $user, $token) { //Muestro todos los Commits
    $commits = $curl->get("https://api.github.com/repos/$user/$repo/commits", $token); 
    $readme = $curl->get("https://api.github.com/repos/$user/$repo/readme", $token);
    $commits = json_decode($commits); // Descodificamos en JSON
    $readme = json_decode($readme);
    echo '<a href="index.php" class="btn btn-default btn-sb">Volver Atras</a>';
    if (@isset($commits[0])) { // Si tiene commits mostraremos esto
        echo "<h2>Repositorio: $repo </h2><hr/>";
        foreach ($commits as $value) {
            echo '<div class="commit">';
            echo 'Commit: ' . $value->commit->message . '<br />';
            echo 'Autor: ' . $value->commit->committer->name . '<br />   ';
            echo 'Fecha: ' . $value->commit->committer->date . '<br /><br />';
            echo '<hr size="2px" color="black" /></div>';
        }
    } else { // Si no tiene commits mostraremos esto
        echo "<p>Sin Commits </p>";
    }
    if (isset($readme->content)) { //Mostramos Readme 
        echo "<h2>" . $readme->name . '</h2>';
        echo base64_decode($readme->content); // Lo descodificamos en base64
    }
}
?>

<html>
    <head>
        <title>Git</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <style type="text/css">
            .repo {
                background:lightgrey; margin:2px; 
            }
            .repo > a {
                color:black;
                text-decoration: none; 
                display: block;
                padding-top: 10px;
                padding-left: 10px;
            }
            .btn {
                margin-top: 15px;
                margin-left: 10px;
            }
            hr{border-color:black}
        </style>
    </head>
</html>