<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Estado de Cambio de Patrimonio Neto</h6>
    </div>
    <div class="card-body">
        <form id="cambioPatrimonioForm" method="POST" action="../../backend/Reportes/cambioPatrimonio/cambioPatrimonio.php" target="_blank">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label>Fecha a Seleccionar:</label>
                    <input type="text" id="fechahasta" name="fechahasta" class="datepicker form-control" placeholder="Selecciona mes y año">
                </div>
            </div>
            <button type="button" class="btn btn-success mb-3 float-right" id="reportePatrimonio">
                <i class="fa fa-plus"></i> Imprimir
            </button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: "mm/yyyy",
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
            language: 'es',
            todayHighlight: true
        });

        $('#reportePatrimonio').on('click', function() {
            var fechaHasta = $('#fechahasta').val();
            
            if (!fechaHasta) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'Por favor, seleccione una fecha.',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                $('#cambioPatrimonioForm').submit();
            }
        });
    });
</script>
