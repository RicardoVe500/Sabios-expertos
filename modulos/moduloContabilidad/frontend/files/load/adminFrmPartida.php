<?php
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
                    <label>Numero de Comprobante:</label>
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
                    <label>Haber:</label>
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
                        <i class="fas fa-arrow-down"></i> Ingresar 
                    </button>
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

        <!-- Agrega el botón de mayorización y la tabla de resultados aquí -->
        <div class="row mb-3">
            <div class="col">
                <button class="btn btn-success mt-3" id="generarMayorizacion">
                    <i class="fas fa-calculator"></i> Generar Mayorización
                </button>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Mayorización</h6>
            </div>
            <div class="card-body">
                <table id="tablaMayorizacion" class="table">
                    <thead>
                        <tr>
                            <th>Cuenta</th>
                            <th>Debe</th>
                            <th>Haber</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se mostrarán los datos de la mayorización -->
                    </tbody>
                </table>
            </div>
        </div>
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
                    url: '../lib/partida/v_partida.php',
                    type: 'GET',
                    data: {
                        tipoPartidaId: tipoPartidaId
                    },
                    success: function(response) {
                        $('#page-content-wrapper').html(response);
                    },
                    error: function(xhr, status, error) {
                        alert("Error al cargar la página de partidas: " + error);
                    }
                });
            });

            $("#generarMayorizacion").click(function() {
                generarMayorizacion();
            });

            async function generarMayorizacion() {
                try {
                    const response = await fetch('../../backend/Mayorizacion/mayorizacion.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ partidaId: $("#partidaId").val() })
                    });

                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }

                    const data = await response.json();
                    mostrarMayorizacion(data);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar la mayorización. Por favor, intenta de nuevo.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }

            function mostrarMayorizacion(data) {
                const tbody = $("#tablaMayorizacion tbody");
                tbody.empty();
                data.forEach(row => {
                    tbody.append(`
                        <tr>
                            <td>${row.cuenta}</td>
                            <td>${row.debe}</td>
                            <td>${row.haber}</td>
                            <td>${row.saldo}</td>
                        </tr>
                    `);
                });
            }
        });
    </script>
</div>
