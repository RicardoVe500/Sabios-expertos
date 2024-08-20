<div class="card shadow mb-4">
    <div class="card-header py-3 card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Empresas</h6>
        <button class="btn btn-secondary float-right btn-sm" id="reportecatalogo" title="Reporte">
            <i class="fas fa-print"></i></button>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-9">
                <button class="btn btn-success mb-3" id="frmAddEmpresa">
                    <i class="fa fa-plus"></i> Agregar Empresa
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

        <table id="tablaEmpresas" class="table" style="width:100%">
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



<script src="../lib/js/scripts/frmEmpresas.js"></script>


<script>
$(document).ready(function() {


    $("#frmAddEmpresa").click(function() {
        $("#render").load("./load/form/Empresas/Add/frmAddEmpresa.php");
    })


    $("#reportecatalogo").click(function() {
    var reportUrl = '../../backend/Catalogo/reportes/catalogoreporte.php';
        window.open(reportUrl, '_blank');
    })

})

</script>