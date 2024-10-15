
// Responsive mega code
document.addEventListener('DOMContentLoaded', function() {
    const openMenu = document.querySelector('.openMenu');
    const closeMenu = document.querySelector('.closeMenu');
    const navbar = document.querySelector('#navbar');

    // Open the menu
    openMenu.addEventListener('click', function() {
        navbar.classList.add('show');  // Add the 'show' class to display the menu
        document.body.style.overflow = 'hidden';  // Disable background scrolling when menu is open
    });

    // Close the menu
    closeMenu.addEventListener('click', function() {
        navbar.classList.remove('show');  // Remove the 'show' class to hide the menu
        document.body.style.overflow = 'auto';  // Re-enable background scrolling when menu is closed
    });
});



// slider code



const progressCircle = document.querySelector(".autoplay-progress svg");
const progressContent = document.querySelector(".autoplay-progress span");
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





