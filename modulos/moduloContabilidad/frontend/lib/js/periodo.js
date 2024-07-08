$(document).ready(function () {
  datostabla()
  //Se inicializa el modal y se establece que no se pueda cerrar de ningun modo
  $('#selectMonthModal').modal({
    backdrop: 'static',
    keyboard: false,
    show: true,

  });

  $('#addPeriodo').click(function () {
    $('#selectMonthModal').modal('hide'); // cerramos el modal al precionar el boton de agregar
    $('#secondaryModal').modal('show'); // Habre el modal de crear 
  });

  $('#secondaryModal').on('hidden.bs.modal', function () {
    $('#selectMonthModal').modal('show');
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
    seleccionarPeriodo(data.periodoId, data.mes, data.anio, data.estadoId);
  });


  $('#resetPeriodo').click(function () {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "¿Quieres cambiar el período de trabajo?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, cambiar!',
      cancelButtonText: 'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '../../backend/Periodo/session/cambioPeriodo.php',
          type: 'POST',
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              $('#selectMonthModal').modal('show');
            } else {
              Swal.fire(
                'Error!',
                response.message,
                'error'
              );
            }
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {

      }
    });
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
      dataType: "json", // Asegúrate de especificar el tipo de datos esperado
      success: function (data) {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: '¡Periodo agregado!',
            text: data.message,
          });
          $('#tablaperiodo').DataTable().ajax.reload();
          $('#secondaryModal').modal('hide');
          $('#selectMonthModal').modal('show');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
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
  if ($.fn.dataTable.isDataTable('#tablaperiodo')) {
      $('#tablaperiodo').DataTable().destroy();
  }
  console.log("Creando nuevo DataTable");
  $('#tablaperiodo').DataTable({
      "ajax": "../../backend/Periodo/listardatos/listardatos.php",
      "columns": [
          { "data": "mes" },
          { "data": "anio" },
          {
              "data": null,
              "defaultContent": '<button class="btn btn-primary btn-sm btn-selectperiodo"><i class="fas fa-folder-open"></i> </button>'
          }
      ],
      "columnDefs": [{
          "targets": -1,
          "orderable": false,
          "className": "dt-center"
      }]
  });
}

function seleccionarPeriodo(periodoId, mes, anio, estadoId) {
  $.ajax({
    type: "POST",
    url: "../../backend/Periodo/session/periodosession.php",
    data: { periodoId: periodoId, mes: mes, anio: anio, estadoId},
    success: function (response) {
      const nombreMes = obtenerNombreMes(mes);
      Swal.fire({
        icon: 'success',
        title: 'Período seleccionado',
        text: `Se trabajara el periodo de: ${nombreMes} ${anio}.`,
      });
      $('#selectMonthModal').modal('hide');
      console.log("Respuesta del servidor:", response);
  if(response.status === "success") {
    console.log("Datos del período:", response.data);
  }
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


