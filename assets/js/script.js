const ham_icon = document.querySelector(".ham-icon");

ham_icon.addEventListener("click", () => {
  document.querySelector(".nav > ul").classList.toggle("scroll");
});

function adjustMainHeight() {
  const logoHeight = document.querySelector('header').offsetHeight;
  const navHeight = document.querySelector('.nav').offsetHeight;
  const mainElement = document.querySelector('.main');

  mainElement.style.minHeight = `calc(100vh - ${logoHeight + navHeight}px - 40px)`;
}

