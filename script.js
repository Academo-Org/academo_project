const prev = document.querySelector(".prev");
const next = document.querySelector(".next");
const carouselImages = document.querySelector(".carousel-images");
const images = document.querySelectorAll(".carousel-images img");

let index = 0;

function showSlide(i) {
  if (i < 0) index = images.length - 1;
  else if (i >= images.length) index = 0;
  else index = i;

  carouselImages.style.transform = `translateX(${-index * 100}%)`;
}

prev.addEventListener("click", () => showSlide(index - 1));
next.addEventListener("click", () => showSlide(index + 1));
