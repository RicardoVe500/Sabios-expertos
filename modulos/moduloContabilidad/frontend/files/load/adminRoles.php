    <!-- Formulario para agregar nuevo rol -->
    <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Administración de Roles</h6>
    </div>
    <div class="card-body">
        <form id="agregar-rol-form">
            <div class="form-group">
                <label for="nombreTipo">Nombre</label>
                <input type="text" class="form-control" id="nombreTipo" name="nombreTipo">
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> Agregar Rol</button>
        </form> <!-- Cerrar el formulario aquí -->
        <div class="container mt-5">
            <!-- Parte inferior de Roles -->
            <h2>Roles</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="roles-list">
                    <!-- Aquí se mostrarán los roles -->
                </tbody>
            </table>
        </div>
    </div>
</div>

                <script src="../lib/js/scripts/frmRoles.js"></script>

