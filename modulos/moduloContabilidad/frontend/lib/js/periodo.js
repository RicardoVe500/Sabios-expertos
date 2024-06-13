$(document).ready(function () {
  datostabla()
  //Se inicializa el modal y se establece que no se pueda cerrar de ningun modo
  $('#selectMonthModal').modal({
    backdrop: 'static',
    keyboard: false,
    show: true,

  });

  $('#addPeriodo').click(function () {
    $('#selectMonthModal').modal('hide'); // Cierra el modal actual
    $('#secondaryModal').modal('show'); // Abre el modal secundario
  });

  $('#secondaryModal').on('hidden.bs.modal', function () {
    $('#selectMonthModal').modal('show'); // Opcional: Reabre el modal principal si es necesario
  });

  $('#monthYearPicker').datepicker({
    format: "mm/yyyy",
    language: 'es',
    startView: "months",
    minViewMode: "months",
    autoclose: true

  });


  $('#addPeriodoboton').click(function () {
    guardarperiodo()
  });

  $('#tablaperiodo').on('click', '.btn-selectperiodo', function () {
    var data = $('#tablaperiodo').DataTable().row($(this).parents('tr')).data();
    seleccionarPeriodo(data.periodoId, data.mes, data.anio);
  });


});


function guardarperiodo() {
  if ($("#monthYearPicker").val() == "") {
    Swal.fire({
      title: 'Error',
      text: 'Ingrese el Periodo',
      icon: 'warning',
      confirmButtonText: 'Aceptar'
    });
  } else {
    var url = "../../backend/Periodo/Add/AddPeriodo.php";
    $.ajax({
      type: "POST",
      url: url,
      data: $("#frmperiodo").serialize(),
      success: function (data) {
        
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: '¡Periodo agregado!',
            text: "Se agrego con exito",
          });
          $('#tablaperiodo').DataTable().ajax.reload();
          $('#secondaryModal').modal('hide');
          $('#selectMonthModal').modal('show');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "Este Periodo ya esta registrado",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Error al crear',
          text: 'No se pudo crear el periodo. Por favor, intenta de nuevo.',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  }
}

function datostabla() {
  $('#tablaperiodo').DataTable({
    "ajax": "../../backend/Periodo/listardatos/listardatos.php",
    "columns": [
      { "data": "mes" },
      { "data": "anio" },
      {
        "data": null,
        "defaultContent": `
                <button class='btn btn-primary btn-sm btn-selectperiodo'><i class="fas fa-folder-open"></i> </button>
            `
      }
    ],
    "columnDefs": [{
      "targets": -1,
      "orderable": false,
      "className": "dt-center"
    }]
  });

}


/*
$(document).ready(function() {
//Se inicializa el modal y se establece que no se pueda cerrar de ningun modo
$('#selectMonthModal').modal({
    backdrop: 'static', 
    keyboard: false, 
    show: true 
});

//se inicializa la libreria de datepicker para poder solo escojer los meses
$('#monthYearPicker').datepicker({
    format: "mm/yyyy",
    language: 'es',
    startView: "months",
    minViewMode: "months",
    autoclose: true
});

//Se hace la validacion si a seleccionado un mes de lo contrario no se cierra el modal y landa un mensaje
function handleSelection() {
    var selectedMonthYear = $('#monthYearPicker').val(); 
    if (selectedMonthYear) {
        sessionStorage.setItem('selectedMonthYear', selectedMonthYear);
        return true; 
    } else {
        Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Seleccione un periodo'
                }); 
        return false; 
    }
}

//se cierra el modal cuando se llena el campo y le damos al boton
$('#confirmSelection').click(function() {
    if (handleSelection()) {
        $('#selectMonthModal').modal('hide'); 
    }
});


$('#selectMonthModal').on('hide.bs.modal', function (e) {
    if (!handleSelection()) {
        e.preventDefault(); 
    }
});
});
*/

function seleccionarPeriodo(periodoId, mes, anio) {
  $.ajax({
    type: "POST",
    url: "../../backend/Periodo/session/periodosession.php",
    data: { periodoId: periodoId, mes: mes, anio: anio },
    success: function (response) {
      const nombreMes = obtenerNombreMes(mes);
      Swal.fire({
        icon: 'success',
        title: 'Período seleccionado',
        text: `Se trabajara el periodo de: ${nombreMes} ${anio}.`,
      });
      $('#selectMonthModal').modal('hide');
      // Recargar datos o realizar otras acciones necesarias
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo establecer el período. Por favor, intenta de nuevo.',
        confirmButtonText: 'Aceptar'
      });
    }
  });
}

function obtenerNombreMes(mes) {
  const meses = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
  ];
  return meses[mes - 1]; // Restar 1 porque los meses en JavaScript empiezan desde 0
}

/*
//funcion para establecer el mes en el que se trabajara
function setWorkMonth() {
  var selectedMonthYear = $('#monthYearPicker').val(); // Formato mm/yyyy
  sessionStorage.setItem('selectedMonthYear', selectedMonthYear); // Guarda el mes y año seleccionado
  $('#selectMonthModal').modal('hide'); // Cierra el modal
}

function updateComprobanteDateField() {
  var monthYear = sessionStorage.getItem('selectedMonthYear');
  if (monthYear) {
      var parts = monthYear.split('/'); // Dividir el string en mes y año
      var year = parts[1];
      var month = parts[0];

      // Establecer las fechas mínima y máxima para limitar el selector de fecha
      var firstDay = `${year}-${month}-01`;
      var lastDay = new Date(year, month, 0).toISOString().split('T')[0]; // Obtener el último día del mes

      $('#fechacontable').attr('min', firstDay);
      $('#fechacontable').attr('max', lastDay);
  }
}
  */