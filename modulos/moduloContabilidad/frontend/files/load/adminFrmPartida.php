<?php
    $partidaId = $_REQUEST['partidaId'] ?? 'partidaId';
    echo "<input type='hidden' id='partidaId' name='partidaId' value='$partidaId'>";
    $codigoPartida = $_REQUEST['codigoPartida'] ?? 'defaultCodigo';
    echo "<input type='hidden' id='codigoPartida' name='codigoPartida' value='$codigoPartida'>";
    $tipoPartidaId = $_REQUEST['tipoPartidaId'] ?? 'defaultTipopartida';
    echo "<input type='hidden' id='tipoPartidaId' name='tipoPartidaId' value='$tipoPartidaId'>";
    
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 card-header py-3 d-flex justify-content-between align-items-center" id="cabezaboton">
        <h6 class="m-0 font-weight-bold text-primary">Movimientos de partidas</h6>
        <button class="btn btn-warning float-right " id="regresarpartidas">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-2">
                <label>Fecha Actual:</label>
                <input type="date" id="cuerpofechaActual" name="cuerpofechaActual" class="form-control" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
            <div class="col-12 col-md-2">
                <label>Fecha Contable:</label>
                <input type="date" id="cuerpofechacontable" name="cuerpofechacontable" class="form-control" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
            <div class="col-12 col-md-3">
                <label>Debe:</label>
                <input type="text" id="cuerpodebe" name="cuerpodebe" class="form-control" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
            <div class="col-12 col-md-3">
                <label>Haber:</label>
                <input type="text" id="cuerpohaber" name="cuerpohaber" class="form-control" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
            <div class="col-12 col-md-2">
                <label>Diferencia:</label>
                <input type="text" id="cuerpodiferencia" name="cuerpodiferencia" class="form-control mb-3" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-7">
                <label>Concepto:</label>
                <input type="text" id="cuerpoconcepto" name="cuerpoconcepto" class="form-control" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
            </div>
            <div class="col-12 col-md-3">
                <label>Estado:</label>
                <input type="text" id="cuerpoestado" name="cuerpoestado" class="form-control mb-3" readonly
                    style="pointer-events: none; background-color: #e9ecef;">
                    
            </div>
            <input type="hidden" id="partidaDetalleId" name="partidaDetalleId" class="form-control">
        </div>


        <form name="frmcuerpo" id="frmcuerpo">
            <hr>

            <?php
                $partidaId = $_REQUEST['partidaId'] ?? 'defaultID';
                echo "<input type='hidden' id='partidaId' name='partidaId' value='$partidaId'>";
            ?>
            <div class="row mb-3">
                <div class="col-sm-2">
                    <label style="margin-top: 5px;">Comprobante:</label>
                    <select class="form-control" id="selectcomprobante" name="selectcomprobante"
                        style="margin-top: 5px;">
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>Número de Comprobante:</label>
                    <input type="text" id="numeroComprobante" name="numeroComprobante" class="form-control">
                </div>
                <div class="col-sm-3">
                    <label>Fecha Comprobante:</label>
                    <input type="date" id="fechaComprobante" name="fechaComprobante" class="form-control">
                </div>
            </div>
            <div class="row">

                <div class="col-sm-3">
                    <label style="margin-top: 5px;">Cuenta Contable:</label>
                    <select class="form-control" id="selectcuentas" name="selectcuentas" style="margin-top: 5px;">
                    </select>
                </div>
                <div class="col-sm-4">
                    <label>Concepto Especifico:</label>
                    <input type="text" id="conceptoespecifico" name="conceptoespecifico" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label>Debe:</label>
                    <input type="text" id="debeCuerpo" name="debeCuerpo" class="form-control" value="0">
                </div>
                <div class="col-sm-2">
                    <label>haber:</label>
                    <input type="text" id="haberCuerpo" name="haberCuerpo" class="form-control" value="0">
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col">
                <div>
                    <button class="btn btn-danger mt-3" id="cerrarCuenta" style="display: none;">
                        <i class="fas fa-lock"></i> Cerrar Cuenta
                    </button>

                    <button class="btn btn-primary float-right mt-3" id="dato" data-mode="add">
                        <i class="fas fa-arrow-down"></i> Ingresar </button>
                </div>
            </div>
        </div>


        <table id="tablaCuerpo" class="table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Cuenta</th>
                    <th scope="col">Concepto</th>
                    <th scope="col">Debe</th>
                    <th scope="col">Haber</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
    </div>


    <script src="../lib/js/scripts/frmPartidaCuerpo.js"></script>
    <script src="../lib/js/scripts/enter.js"></script>

    <script>
    $(document).ready(function() {

        var fechaContable = document.getElementById('fechaComprobante');
        var fechaHoy = new Date();
        var dia = ('0' + fechaHoy.getDate()).slice(-2);
        var mes = ('0' + (fechaHoy.getMonth() + 1)).slice(-2);
        var ano = fechaHoy.getFullYear();
        var fechaMaxima = ano + '-' + mes + '-' + dia;

        fechaContable.setAttribute('max', fechaMaxima);

        cargadatospartida()
        Imprimirtablacuerpo()
       
   

        $("#dato").click(function() {
            var mode = $(this).data("mode");
            if (mode === "add") {
                guardarCuerpoPartida();
            } else if (mode === "edit") {
                editardatos();
            }
        });

        $("#regresarpartidas").click(function() {
            var tipoPartidaId = $("#tipoPartidaId").val();
            $.ajax({
                url: "load/adminPartidas.php",
                type: "POST",
                data: {
                    tipoPartidaId: tipoPartidaId
                },
                success: function(response) {
                    $("#render").html(response);

                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al mostrar',
                        text: 'No se pudo cargar el contenido Por favor, intenta de nuevo.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        })


        // Función para limpiar el valor por defecto si es 0
        function limpiarCero(event) {
            if (event.target.value === '0') {
                event.target.value = '';
            }
        }

        // Función para reestablecer a 0 si el campo está vacío
        function reestablecerCero(event) {
            if (event.target.value === '') {
                event.target.value = '0';
            }
        }

        // Añadir listeners a los campos de entrada
        document.getElementById('debeCuerpo').addEventListener('focus', limpiarCero);
        document.getElementById('debeCuerpo').addEventListener('blur', reestablecerCero);
        document.getElementById('haberCuerpo').addEventListener('focus', limpiarCero);
        document.getElementById('haberCuerpo').addEventListener('blur', reestablecerCero);


        validarCampos()
        document.getElementById('debeCuerpo').addEventListener('input', validarCampos);
        document.getElementById('haberCuerpo').addEventListener('input', validarCampos);



    })


    function validarEntrada(event) {
            const campo = event.target;
            const valor = campo.value;
            // Permite solo números y un punto decimal
            const nuevoValor = valor.replace(/[^0-9.]/g, '');
            // Asegúrate de que solo haya un punto decimal
            const partes = nuevoValor.split('.');
            if (partes.length > 2) {
                campo.value = partes[0] + '.' + partes.slice(1).join('');
            } else {
                campo.value = nuevoValor;
            }
        }

        document.getElementById('debeCuerpo').addEventListener('input', validarEntrada);
        document.getElementById('haberCuerpo').addEventListener('input', validarEntrada);
        document.getElementById('numeroComprobante').addEventListener('input', validarEntrada);

  

    </script>