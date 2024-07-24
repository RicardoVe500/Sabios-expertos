<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Reporte de bitacora</h6>
    </div>
    <div class="card-body">
        <form id="bitacorareporteria" method="POST" action="../../backend/Reportes/balancepreeliminar.php" target="_blank">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label>Fecha Desde:</label>
                    <input type="date" id="fechadesde" name="fechadesde" class="datepicker form-control">
                </div>
                <div class="col-md-5">
                    <label>Fecha Hasta:</label>
                    <input type="date" id="fechahasta" name="fechahasta" class="datepicker form-control">
                </div>
            </div>
            <button class="btn btn-success mb-3 float-right" id="reportebita">
            <i class="fa fa-plus"></i> Imprimir
        </button>

        </form>
        
    </div>
</div>
<script src="../lib/js/scripts/frmTipopartida.js"></script>

<script>
$(document).ready(function() {

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        language: 'es',
        todayHighlight: true
    });


})
</script>