
//capturar el evento de envio del formulario
document.getElementById("registroForm").addEventListener('submit', event => {
  //capturar el envio del formulario
  console.log('aqui');
  event.preventDefault();

//capturar los valores de los campos
  const nombre = cleanInput(document.querySelector("#nombre").value.trim());
  const email = cleanInput(document.querySelector("#email").value.trim());
  const password = cleanInput(
    document.querySelector("#contrasena").value.trim()
  );
  const repeat_password = cleanInput(
    document.querySelector("#repetir_contrasena").value.trim()
  );

  //validar que los campos no esten vacios
  if (!nombre || !email || !password || !repeat_password) {
    alert("Todos los campos son obligatorios");
    //mostrar mensaje de error
    console.log("Todos los campos son obligatorios");
    return;
  }

  //validar que las contraseñas coincidan
  if (password !== repeat_password) {
    alert("Las contraseñas no coinciden");
    console.log("Las contraseñas no coinciden");
    return;
  }
//validar que el email sea valido
  console.log("Formulario validado - Enviando datos ...");

  //enviar formulario
  document.querySelector("#registroForm").submit();
});

//funcion para limpiar los campos de entrada
function cleanInput(input) {
  return input.replace(/['@\s]/g, "");
}
