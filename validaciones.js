function validar() {
  // Validar usuario
  var input_usuario = document.getElementById("usuario");
  var error_usuario = document.getElementById("error_usuario");

  if (input_usuario.value.trim() === "" || /^\s+$/.test(input_usuario.value)) { 
    error_usuario.textContent = "El usuario está vacío";
    error_usuario.style.color = "red";
    input_usuario.style.border = "1px solid red";
    return false;
  } else {
    input_usuario.style.border = ""; 
    error_usuario.textContent = ""; 
  }

  // Validar que el usuario no tenga espacios 
  var palabras = input_usuario.value.split(" ");
  var palabrasValidas = 0;

  for (var i = 0; i < palabras.length; i++) {
    if (palabras[i].length >= 1) {
      palabrasValidas++;
    }
  }

  if (palabrasValidas !== 1) {
    error_usuario.textContent = "El usuario solo debe tener una palabra";
    error_usuario.style.color = "red";
    input_usuario.style.border = "1px solid red"; 
    return false;
  } else {
   
  }

  // Validar contraseña
  var input_pwd = document.getElementById("pwd");
  var error_pwd = document.getElementById("error_pwd");

  if (input_pwd.value.trim() === "" || /^\s+$/.test(input_pwd.value)) { 
    error_pwd.textContent = "La contraseña está vacía";
    error_pwd.style.color = "red";
    input_pwd.style.border = "1px solid red"; 
    return false;
  } else {
    input_pwd.style.border = ""; 
    error_pwd.textContent = ""; 
  }

  // Validar que la contraseña tiene al menos 9 caracteres
  if (input_pwd.value.length < 9) {
    error_pwd.textContent = "La contraseña no cumple los requisitos";
    error_pwd.style.color = "red";
    input_pwd.style.border = "1px solid red"; 
    return false;
  } else {

    return true;
  }
}
