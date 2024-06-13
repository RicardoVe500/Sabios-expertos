<div class="modal fade" id="selectMonthModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Selecciona el Mes de Trabajo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <button class="btn btn-success mb-3" id="addPeriodo">
            <i class="far fa-calendar-plus"></i> Agregar Periodo</button>
            <table id="tablaperiodo" class="table" style="width:100%">
            <thead>
                <tr> 
                    <th scope="col">Mes</th>
                    <th scope="col">Año</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
        </table>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<?php include("./load/form/Periodo/Add/AddPeriodo.php");?>



