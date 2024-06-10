$(document).ready(function(){

    $("#render").load("../contenido/principal.php");

    $("#principal").click(function(){
        $("#render").load("../contenido/principal.php");
    });

    $("#usuarios").click(function(){
        $("#render").load("load/adminUsuarios.php");
    });

    $("#catalogo").click(function(){
        $("#render").load("load/adminCatalogo.php");
    });

    $("#tipopartida").click(function(){
        $("#render").load("load/adminTipoPartidas.php");
    });
    
    $("#periodo").click(function(){
        $("#render").load("load/adminPeriodo.php");
    });
    


})