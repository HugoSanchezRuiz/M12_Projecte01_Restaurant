function validar() {
    // Validar usuario
    var input_usuario = document.getElementById("usuario");
    var error_usuario = document.getElementById("error_usuario");
  
    if (input_usuario.value.trim() === "" || /^\s+$/.test(input_usuario.value)) { 
      error_usuario.textContent = "El usuario esta vacio";
      error_usuario.style.color = "red";
      input_usuario.style.border = "1px solid red";
      return false;
    } else {
      input_usuario.style.border = ""; // Restablecer el borde a su estado original
      error_usuario.textContent = ""; // Limpiar el mensaje de error
    }
  
    // Validar contraseña
    var input_pwd = document.getElementById("pwd");
    var error_pwd = document.getElementById("error_pwd");
  
    if (input_pwd.value.trim() === "" || /^\s+$/.test(input_pwd.value)) { 
      error_pwd.textContent = "La contrasena esta vacia";
      error_pwd.style.color = "red";
      input_pwd.style.border = "1px solid red"; // Corregido el nombre de la variable
      return false;
    } else {
      input_pwd.style.border = ""; // Restablecer el borde a su estado original
      error_pwd.textContent = ""; // Limpiar el mensaje de error
    }
  
    return true;
  }
  