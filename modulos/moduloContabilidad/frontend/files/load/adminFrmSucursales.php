<?php
$empresaId = $_REQUEST['empresaId'] ?? 'defaultID';
echo "<input type='text' id='empresaId' value='$empresaId'>";
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Sucursales</h6>
        <button class="btn btn-secondary float-right btn-sm" id="Sucursales" title="Reporte">
            <i class="fas fa-print"></i></button>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-9">
                <button class="btn btn-success mb-3" id="frmAddsucursal">
                    <i class="fa fa-plus"></i> Agregar Sucursal
                </button>
                <button class="btn btn-warning mb-3" id="regresarEmpresas">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

        <table id="tablaSucursal" class="table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Direccion</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
    </div>
</div>



<script src="../lib/js/scripts/frmSucursal.js"></script>


<script>
$(document).ready(function() {


    $("#frmAddSucursal").click(function() {
        $("#render").load("./load/form/Empresas/Add/frmAddSucursal.php");
    })

    $("#regresarEmpresas").click(function() {
        $("#render").load("./load/adminEmpresas.php");
    })


    $("#reportecatalogo").click(function() {
    var reportUrl = '../../backend/Catalogo/reportes/catalogoreporte.php';
        window.open(reportUrl, '_blank');
    })

})

</script>