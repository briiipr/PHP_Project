<?php
function CompruebaTabla($conexionUsar, $tablaUsar, $bddUsar){
                    $sentenciaTabla = "select * from " . $tablaUsar;
                        $valor = true;
                        $conexionUsar->select_db($bddUsar);
                        $queryComprobar = mysqli_query($conexionUsar, $sentenciaTabla);
                        if(!$queryComprobar){
                            echo "<h3 style=\"margin-left: 1%\">ERROR: No se encuentra la tabla " . $tablaUsar . "</h3>";
                            echo "<h3 style=\"margin-left: 1%\">Base de Datos INCOMPLETA. Se debe realizar una reinstalación.</h3>";
                            $valor = false;
                        }
                        return($valor);
                }
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="shortcut icon" href="../../Media/Escudo.png" />
        <link rel="stylesheet" type="text/css" href="CSS/Estilos.css">
        <title>Consejería de Agricultura, Ganadería, Pesca y Aguas.</title>
    </head>
    <body>
        <div class="iFrameHeader"><?php include('../iFrame/Header.php'); ?></div>
        <div class="iFrameMenu"><?php include('../iFrame/Menu.php'); ?></div>
        <div class="central" style="padding-top: 3%">
            <form method="post" action="">
            <?php
                $conexion = new mysqli('localhost', 'root', '');
                $conexion->set_charset('UTF-8');
                $nombreBDD = "ganaderia";
                $continuar = false;
                $valor_Municipio = "";
                $islaprevia = 0;
                $listaIslas = [];
                $listaMunicipios = [];
                
                if($conexion->connect_error){                                                               //Se repiten las MISMAS COMPROBACIONES
                    echo "<h3 style=\"margin-left: 1%\">Error al intentar conectar con el servidor</h3>";   //que el resto del proyecto.
                    echo "<h4 style=\"margin-left: 1%\">ERROR: </h4>" . mysqli_error($conexion);
                }
                else{
                    $compruebaBDD = mysqli_select_db($conexion, $nombreBDD);
                    
                    if(empty($compruebaBDD)){
                        echo "<h3 style=\"margin-left: 1%\">ERROR: Base de datos no encontrada.</h3>";
                    }
                    else{
                        if(CompruebaTabla($conexion, "gan_datosganaderia", $nombreBDD)){
                            if(CompruebaTabla($conexion, "gan_datosgenerales", $nombreBDD)){
                                if(CompruebaTabla($conexion, "gan_islas", $nombreBDD)){
                                    if(CompruebaTabla($conexion, "gan_municipios", $nombreBDD)){
                                        if(CompruebaTabla($conexion, "gan_tipoexplotaciones", $nombreBDD)){
                                            if(CompruebaTabla($conexion, "gan_tipoganaderia", $nombreBDD)){
                                                $continuar = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if($continuar){
                        $listaIslasSentencia = "select * from gan_islas";                       // Se obtiene la lista de Islas de la Base de Datos
                        $listaIslasConsulta = mysqli_query($conexion, $listaIslasSentencia);    // para almacenarla en un array más tarde.
                        
                        if(!$listaIslasConsulta){
                            echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                            echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                            $continuar = false;
                        }
                        else{
                            while ($row = mysqli_fetch_assoc($listaIslasConsulta)){
                                     $listaIslas[] = $row;
                                }
                            if(count($listaIslas) == 0){
                                echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                $continuar = false;
                            }
                            else{
                                //
                                echo "<label>Escoja Isla:&nbsp;&nbsp;</label>";
                                echo "<select name=\"selectIsla\">";
                                for($i = 0; $i < count($listaIslas); $i++){
                                    $valor = $listaIslas[$i]['IdIsla'];
                                    $campo = $listaIslas[$i]['Isla'];
                                    
                                    echo "<option value=\"" . $valor . "\"";
                                    if(isset($_POST['selectIsla']) && $valor == $_POST['selectIsla']){
                                        echo "selected =\"selected\"";
                                    }
                                    echo ">" . $campo . "</option>";
                                }
                                echo "</select><br/>";
                                $continuar = true;
                            }
                        }
                    }
                    //---------------------------------------------------------------------------------------------------------------------------
                    if($continuar){
                        if(isset($_POST['selectIsla'])){    // Si se ha escogido una Isla en el primer Select, se hace una consulta para almacenar la lista de Municipios de esa Isla concreta
                            $listaMunicipiosSentencia = "select * 
                                                     from gan_municipios as MUNI
                                                     join gan_islas as ISL on MUNI.IdIsla = ISL.IdIsla
                                                     where ISL.IdIsla = " . $_POST['selectIsla'];
                        $listaMunicipiosConsulta = mysqli_query($conexion, $listaMunicipiosSentencia);
                        
                        if(!$listaMunicipiosConsulta){
                            echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                            echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                            $continuar = false;
                        }
                        else{
                            while ($row = mysqli_fetch_assoc($listaMunicipiosConsulta)){
                                     $listaMunicipios[] = $row;
                                }
                            if(count($listaMunicipios) == 0){
                                echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                $continuar = false;
                            }
                            else{                                                           // Una vez almacenada la lista de Municipios,
                                echo "<br/><label>Escoja Municipio:&nbsp;&nbsp;</label>";   // se muestra el Select con el contenido de la misma.
                                echo "<select name=\"selectMunicipio\">";
                                for($i = 0; $i < count($listaMunicipios); $i++){
                                    $valor = $listaMunicipios[$i]['IdMunicipio'];
                                    $campo = $listaMunicipios[$i]['Municipio'];
                                    
                                    echo "<option value=\"" . $valor . "\"";
                                    if(isset($_POST['selectMunicipio']) && $valor == $_POST['selectMunicipio']){
                                        echo "selected =\"selected\"";
                                        $valor_Municipio = $_POST['selectMunicipio'];
                                    }
                                    echo ">" . $campo . "</option>";
                                }
                                echo "</select><br/><br/>";
                                $continuar = true;
                            }
                        }
                       }
                       else{
                           $continuar = false;
                       }
                    }
                    //---------------------------------------------------------------------------------------------------------------------
                    echo "<input type=\"submit\" value=\"Mostrar\" style=\"float: left\"/><br/><br/>";
                    //---------------------------------------------------------------------------------------------------------------------
                    if($continuar ){                                                                            
                        if(isset($_POST['selectMunicipio']) && $_POST['selectMunicipio'] == $valor_Municipio){  // Si se ha escogido un Municipio significa que también se ha 
                            $valor_isla = $_POST['selectIsla'];                                                 // escogido una Isla. Si el Municipio escogido es el actual, se muestra la Tabla
                                                                                                                // (la condición del IF elimina la posibilidad de error al cambiar de Isla)
                            $listaGenMunicipioSentencia = "select TIE.TipoExplotacion, sum(DGEN.NExplotaciones) as NumeroExplotaciones
                                                      from gan_datosgenerales as DGEN 
                                                      join gan_tipoexplotaciones as TIE on TIE.IdTipo = DGEN.idTipo
                                                      join gan_municipios as MUNI on MUNI.IdMunicipio = DGEN.idMunicipio
                                                      join gan_islas as ISL on ISL.IdIsla = MUNI.IdIsla
                                                      where ISL.IdIsla = $valor_isla and DGEN.idMunicipio = " . $_POST['selectMunicipio'] ."
                                                      group by TIE.TipoExplotacion";
                            $listaGenMunicipioConsulta = mysqli_query($conexion, $listaGenMunicipioSentencia);
                            if(!$listaGenMunicipioConsulta){
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                                $continuar = false;
                            }
                            else{
                                while ($row = mysqli_fetch_assoc($listaGenMunicipioConsulta)){                  // Se almacena la lista General de la Isla y Municipio concretos
                                     $listaGenMuni[] = $row;                                                    // y luego se muestra en Tabla.
                                }          
                                if(count($listaGenMuni) == 0){
                                    echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                    $continuar = false;
                                }
                                else{
                                    $totalExploGen = 0;
                                    echo "<table>";
                                    echo "<caption><b>Explotaciones en General</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Explotación</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($listaGenMuni); $i++){
                                       echo "<tr>";
                                       echo "<td>" . utf8_encode($listaGenMuni[$i]['TipoExplotacion']) . "</td>";
                                       echo "<td>" . utf8_encode($listaGenMuni[$i]['NumeroExplotaciones']) . "</td>";
                                       echo "</tr>";
                                       $totalExploGen += utf8_encode($listaGenMuni[$i]['NumeroExplotaciones']);
                                   }
                                   echo "<tr>";
                                   echo "<td> ⠀</td><td> ⠀</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Total Explotaciones:</td><td>" . $totalExploGen . "</td>";
                                   echo "</tr>";
                                   echo "</table>";

                                    $continuar = true;
                                }
                            }
                            //----------------------------------------------------------------------------------------------------------------------------------
                                                                    // Se almacena la lista General de la Isla y Municipio concretos. Luego se muestra en Tabla.
                            $listaGanMunicipioSentencia = "select TIG.TipoGanaderia, sum(DGAN.NExplotacionesGanaderas) as 'NumeroExplotaciones', sum(DGAN.Ncabezas) as 'TotalCabezas' 
                                                          from gan_datosganaderia as DGAN 
                                                          join gan_tipoganaderia as TIG on DGAN.idTipo = TIG.IdTipo
                                                          join gan_municipios as MUNI on MUNI.IdMunicipio = DGAN.idMunicipio
                                                          join gan_islas as ISL on ISL.IdIsla = MUNI.IdIsla
                                                          where ISL.IdIsla = $valor_isla and DGAN.idMunicipio = " . $_POST['selectMunicipio'] ."
                                                          group by TIG.TipoGanaderia";
                            $listaGanMunicipioConsulta = mysqli_query($conexion, $listaGanMunicipioSentencia);
                            if(!$listaGanMunicipioConsulta){
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                            }
                            else{
                                while ($row = mysqli_fetch_assoc($listaGanMunicipioConsulta)){
                                     $listaGanMuni[] = $row;
                                }
                                
                                if(count($listaGanMuni) == 0){
                                    echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                }
                                else{
                                    $totalCabezas = 0;
                                    $totalExploGan = 0;
                                    echo "<table>";
                                     echo "<caption><b>Explotaciones Ganaderas</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Ganadería</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "<td>Número de cabezas</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($listaGanMuni); $i++){
                                       echo "<tr>";
                                       echo "<td>" . utf8_encode($listaGanMuni[$i]['TipoGanaderia']) . "</td>";
                                       echo "<td>" . utf8_encode($listaGanMuni[$i]['NumeroExplotaciones']) . "</td>";
                                       echo "<td>" . utf8_encode($listaGanMuni[$i]['TotalCabezas']) . "</td>";
                                       echo "</tr>";
                                       $totalCabezas += utf8_encode($listaGanMuni[$i]['TotalCabezas']);
                                       $totalExploGan += utf8_encode($listaGanMuni[$i]['NumeroExplotaciones']);
                                   }
                                   echo "<tr>";
                                   echo "<td> ⠀</td><td> ⠀</td><td> ⠀</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Totales:</td><td>" . $totalExploGan . "</td><td>" . $totalCabezas . "</td>";
                                   echo "</tr>";
                                   echo "</table>";
                                }
                            }
                        }
                        else{
                            $continuar = false;
                        }
                    }
                }
                mysqli_close($conexion);
            ?>  
            </form>
        </div>
        <div class="iFrameFooter"><?php include('../iFrame/Footer.php'); ?></div>
    </body>
</html>

