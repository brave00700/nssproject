const ham_icon = document.querySelector(".ham-icon");

ham_icon.addEventListener("click", () => {
  document.querySelector(".nav > ul").classList.toggle("scroll");
});
