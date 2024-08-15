<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
    </div>
    <div class="card-body">
        <div class="card-deck">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tipo de Partidas</h5>
                    <p class="card-text">Esta categoría se utiliza para clasificar y organizar las transacciones económicas dentro de un sistema contable.</p>
                    <button class="btn btn-success mb-3" id="tipopartida">
                        <i class="fa fa-angle-right"></i> Ir
                    </button>
                    <p class="card-text mt-2"><small class="text-muted">Última vez ingresado hace <span id="time1"></span> minutos</small></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
                    <button class="btn btn-secondary">Go somewhere</button>
                    <p class="card-text mt-2"><small class="text-muted">Last accessed <span id="time2"></span> mins ago</small></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
                    <button class="btn btn-success">Go somewhere</button>
                    <p class="card-text mt-2"><small class="text-muted">Last accessed <span id="time3"></span> mins ago</small></p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
  $("#tipopartida").click(function(){
        $("#render").load("load/adminTipoPartidas.php");
    });
</script>

<!-- JavaScript to update time in real-time -->
<script>
    function updateTime() {
        const now = new Date();
        const minutes = now.getMinutes();
        document.getElementById('time1').textContent = minutes;
        document.getElementById('time2').textContent = minutes;
        document.getElementById('time3').textContent = minutes;
    }
    setInterval(updateTime, 1000); // Update every second
    updateTime(); // Initial call to set the time immediately
</script>