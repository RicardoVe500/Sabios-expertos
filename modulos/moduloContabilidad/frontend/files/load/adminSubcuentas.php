<?php
$numeroCuenta = $_REQUEST['numeroCuenta'] ?? 'defaultID';
echo "<input type='hidden' id='numeroCuenta' value='$numeroCuenta'>";
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de SubCuentas</h6>
    </div>
    <div class="card-body">
        <button class="btn btn-warning mb-3" id="regresarcat">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

        <table id="tablasubcuenta" class="table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Numero cuenta</th>
                    <th scope="col">nivel cuenta</th>
                    <th scope="col">Cuenta Dependiente</th>
                    <th scope="col">Movimiento</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script src="../lib/js/scripts/frmSubcuentas.js"></script>

