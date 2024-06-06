<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Catalogo</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-success mb-3" id="frmAddCatalogo">
            <i class="fa fa-plus"></i> Agregar Catalogo
        </button>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
            </div>
            <input type="text" class="form-control" placeholder="Buscar" aria-label="Username"
                aria-describedby="basic-addon1">
        </div>

        <table id="tablacatalogo" class="table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Numero cuenta</th>
                    <th scope="col">nivel cuenta</th>
                    <th scope="col">Movimiento</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
    </div>
</div> 


<script src="../lib/js/scripts/frmCatalogo.js"></script>


<script>
$(document).ready(function() {

    imprimirtabla()

    $("#frmAddCatalogo").click(function() {
        $("#render").load("./load/form/Catalogo/Add/frmAddCatalogo.php");
    })

})
</script>