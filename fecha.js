function validar_fecha() {
    var fechaInput = document.getElementById("fecha");
    var errorFecha = document.getElementById("error_fecha");

    var valorFecha = fechaInput.value;

    if (valorFecha === "") {
        // El campo está vacío, muestra un mensaje de error
        errorFecha.style.color = "red";
        errorFecha.innerHTML = "El campo de fecha no puede estar vacío.";

        // Cambia el color del borde a rojo
        fechaInput.style.border = "1px solid red";

        return false; // Indica que hay un error y el formulario no debe ser enviado
    }

    var fechaIngresada = new Date(valorFecha);
    var fechaActual = new Date();

    if (fechaIngresada > fechaActual) {
        errorFecha.style.color = "red";
        errorFecha.innerHTML = "El campo de fecha no cumple los requisitos.";

        // Cambia el color del borde a rojo
        fechaInput.style.border = "1px solid red";

        return false; // Indica que hay un error y el formulario no debe ser enviado
    }

    return true; // La fecha es válida, el formulario puede ser enviado
}
