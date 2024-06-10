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