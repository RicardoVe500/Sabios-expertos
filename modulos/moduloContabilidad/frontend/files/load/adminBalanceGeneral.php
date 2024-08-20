<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Reporte de Balance de General</h6>
    </div>
    <div class="card-body">
    <form id="balancegeneral" method="POST" action="../../backend/Reportes/BalanceGeneral/BalanceGeneral.php" target="_blank">
        
            <div class="row mb-3">
                <div class="col-md-5">
                    <label>Seleccione el Mes:</label>
                    <input type="text" id="monthYearPickerbalance" name="monthYearPickerbalance" class="form-control mb-3" placeholder="Selecciona mes y año">
                </div>
            </div>
            <button class="btn btn-success mb-3 float-right" id="gen">
            <i class="fa fa-plus"></i> Imprimir
        </button>
        
        </form>

      
    </div>
</div>


<script>
$(document).ready(function() {

    $('#monthYearPickerbalance').datepicker({
        format: "mm/yyyy",
        language: 'es',
        startView: "months",
        minViewMode: "months",
        autoclose: true
    })

})


</script>