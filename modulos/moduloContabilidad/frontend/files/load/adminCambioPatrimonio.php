<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Estado de Cambio de Patrimonio Neto</h6>
    </div>
        <div class="card-body">
            <form id="cambioPatrimonioForm" method="POST" action="../../backend/Reportes/cambioPatrimonio/cambioPatrimonio.php" target="_blank">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label>Fecha Desde:</label>
                        <input type="text" id="fechadesde" name="fechadesde" class="datepicker form-control" placeholder="Selecciona mes y año">
                    </div>
                    <div class="col-md-5">
                        <label>Fecha Hasta:</label>
                        <input type="text" id="fechahasta" name="fechahasta" class="datepicker form-control" placeholder="Selecciona mes y año">
                    </div>
                </div>
                <button type="button" class="btn btn-success mb-3 float-right" id="reportePatrimonio">
                    <i class="fa fa-plus"></i> Imprimir
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js"></script>
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
                var fechaDesde = $('#fechadesde').val();
                var fechaHasta = $('#fechahasta').val();
                
                if (!fechaDesde || !fechaHasta) {
                    Swal.fire({
                        icon: 'warning',
                        title: '¡Atención!',
                        text: 'Por favor, completa ambas fechas.',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    var fechaDesdeDate = new Date(fechaDesde + '/01');
                    var fechaHastaDate = new Date(fechaHasta + '/01');
                    
                    if (fechaDesdeDate > fechaHastaDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: '¡Atención!',
                            text: 'La fecha hasta no puede ser anterior a la fecha desde.',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        $('#cambioPatrimonioForm').submit();
                    }
                }
            });
        });
    </script>
</body>
</html>