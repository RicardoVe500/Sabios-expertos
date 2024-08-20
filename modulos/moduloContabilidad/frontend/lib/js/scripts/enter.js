function enableEnterKeySubmission(formId, callbackFunction) {
    $(formId).keypress(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault(); // Previene el comportamiento por defecto del formulario
            callbackFunction();    // Ejecuta la función específica para el formulario
        }
    });
}