<!DOCTYPE html>

<html>
    <head>
        <title>⠀</title>
        <meta charset="UTF-8" name="viewport" content="width=device-width" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../CSS/Estilos.css">
    <base target="_parent">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <ul>
                <li><a href="../Apartados/index.php">Inicio</a></li>
                <li><a href="../Apartados/comunidad.php">Comunidad Autónoma</a></li>
                <li class="desplegable">
                    <a class="desplegableBoton">Islas</a>
                    <div class="contenido-desplegable">
                        <form name="IslaSeleccionar" method="post" action="../Apartados/isla.php">
                            <input type="submit" name="subElHierro" value="El Hierro" />
                            <input type="submit" name="subFuerteventura" value="Fuerteventura" />
                            <input type="submit" name="subGranCanaria" value="Gran Canaria" />
                            <input type="submit" name="subLaGomera" value="La Gomera" />
                            <input type="submit" name="subLaPalma" value="La Palma" />
                            <input type="submit" name="subLanzarote" value="Lanzarote" />
                            <input type="submit" name="subTenerife" value="Tenerife" />
                        </form>
                    </div>
                </li>
                <li><a href="../Apartados/municipio.php">Municipio</a></li>
                <li id="ultimo"><a href="http://www.gobiernodecanarias.org/agricultura/" target="_blank">Enlaces</a></li>
            </ul>
        </div>
    </body>
</html>

