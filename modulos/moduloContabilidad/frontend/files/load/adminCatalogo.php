<div class="card shadow mb-4">
    <div class="card-header py-3 card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Catalogo</h6>
        <button class="btn btn-secondary float-right btn-sm" id="reportecatalogo" title="Reporte">
            <i class="fas fa-print"></i></button>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-9">
                <button class="btn btn-success mb-3" id="frmAddCatalogo">
                    <i class="fa fa-plus"></i> Agregar Catalogo
                </button>
            </div>

            <div class="col-3">
                <div class="input-group mb-3 float-right">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button"
                            id="inputGroupFileAddon03">Subir</button>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivoex"
                            aria-describedby="inputGroupFileAddon03" accept=".xlsx, .xls">
                        <label class="custom-file-label" for="archivoex">Subir archivo</label>
                    </div>
                </div>
            </div>
        </div>


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

    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop();  // Extrae el nombre del archivo
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
   
})



</script>
