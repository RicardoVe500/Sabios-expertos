<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Periodos</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-success mb-3" id="frmAddPeriodo">
            <i class="fa fa-plus"></i> Agregar Tipo Partida
        </button>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

        <table id="tablaTipoPartida" class="table" style="width:100%">
            <thead>
                <tr> 
                    <th scope="col">Mes</th>
                    <th scope="col">Año</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
    </div>
</div> 
<script src="../lib/js/scripts/frmTipopartida.js"></script>

<script>
    
$(document).ready(function() {

    $("#frmAddPeriodo").click(function() {
        $("#render").load("load/form/Periodo/Add/frmAddPeriodo.php");
    })

})
</script>

