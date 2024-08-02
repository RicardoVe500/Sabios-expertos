$(document).ready(function(){

    $("#render").load("../contenido/principal.php");

    $("#principal").click(function(){
        $("#render").load("../contenido/principal.php");
    });

    $("#usuarios").click(function(){
        $("#render").load("load/adminUsuarios.php");
    });

    $("#roles").click(function(){
        $("#render").load("load/adminRoles.php");
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
    
    $("#bitacora").click(function(){
        $("#render").load("load/adminbitacora.php");
    });

    $("#empresas").click(function(){
        $("#render").load("load/adminEmpresas.php");
    });

    $("#balancegeneral").click(function(){
        $("#render").load("load/adminbalancecomprobacion.php");
    });

    $("#cambiopatrimonio").click(function(){
        $("#render").load("load/adminCambioPatrimonio.php");
    });
    

    
})

