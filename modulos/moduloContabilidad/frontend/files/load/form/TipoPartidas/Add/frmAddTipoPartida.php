<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar Tipo de Partidas</h6>
    </div>
    <div class="card-body">

        <button class="btn btn-warning mb-3" id="regresarTipopartida">
            <i class="fa fa-arrow-left"></i> Regresar
        </button>

        <form name="frmAddTipoPartida" id="frmAddTipoPartida">

            <div class="row">
                <div class="col-md-6">
                    <label for="Numero">Nombre Tipo Partida:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Nombre Tipo" id="nombrePartida" name="nombrePartida">
                    </div>

                    <label for="Numero">Abreviatura:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Abreviatura" id="abreviacion" name="abreviacion">
                    </div>

                    <label for="Numero">Descripcion:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Descripcion" id="descripcion" name="descripcion">
                    </div>
                    <button type="button" class="btn btn-success mb-3" id="CrearTipoPartida">
                        <i class="fa fa-plus"></i> Crear
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="../lib/js/scripts/frmTipopartida.js"></script>
<script src="../lib/js/scripts/enter.js"></script>

<script>
$(document).ready(function() {
    
    enableEnterKeySubmission("#frmAddTipoPartida", guardarTipoPartida);
   
    $("#regresarTipopartida").click(function() {
        $("#render").load("./load/adminTipoPartidas.php");
        
    })
})
    

</script>