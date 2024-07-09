<div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Administración de Usuarios</h6>
                                </div>
                                <div class="card-body">
                                    <button class="btn btn-primary mb-3" id="frmAddUsuario">
                                        <i class="fa fa-plus"></i> Agregar Usuario
                                    </button>
                                     <!--  <button class="btn btn-warning mb-3" id="frmEditUsuario">
                                        <i class="fa fa-edit"></i> Editar Usuario
                                    </button>  -->

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                                        </div>
                                        <input type="email" class="form-control" placeholder="Correo Electrónico" id="searchEmail" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>
                                    <button class="btn btn-secondary mb-3" id="searchUser">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>

                                    <div id="searchResults">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellidos</th>
                                                    <th>Email</th>
                                                    <th>Rol</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="userTableBody">
                                                <!-- Aquí se insertarán los resultados de búsqueda -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        
                     
                <script src="../lib/js/scripts/frmEditUsuarios.js"></script>
                        
                        <script>
                            $(document).ready(function(){

                                $("#frmAddUsuario").click(function(){
                                    $("#render").load("./load/form/usuario/Add/frmUsuarios.php");
                                })

                            })
                        </script>
                        <script>
                             $(document).ready(function(){

                                $("#frmEditUsuario").click(function(){
                                    $("#render").load("./load/form/usuario/Edit/frmEditUsuarios.php");
                                })

                            })
                        </script>
                        
                          