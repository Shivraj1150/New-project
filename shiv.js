
// Responsive mega code

document.addEventListener('DOMContentLoaded', function() {
    const openMenu = document.querySelector('.openMenu');
    const closeMenu = document.querySelector('.closeMenu');
    const navbar = document.querySelector('#navbar');

    openMenu.addEventListener('click', function() {
        navbar.classList.add('show');
    });

    closeMenu.addEventListener('click', function() {
        navbar.classList.remove('show');
    });
    

});



// slider code



var swiper = new Swiper(".mySwiper", {
  spaceBetween: 30,
  centeredSlides: true,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".backbutton",
  },
  on: {
    autoplayTimeLeft(s, time, progress) {
      progressCircle.style.setProperty("--progress", 1 - progress);
      progressContent.textContent = `${Math.ceil(time / 1000)}s`;
    },
  },
});





