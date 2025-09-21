document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll(".login-container input");

  inputs.forEach(input => {
    input.addEventListener("focus", () => {
      document.body.classList.add("blur-bg");
    });

    input.addEventListener("blur", () => {
      document.body.classList.remove("blur-bg");
    });
  });
});
