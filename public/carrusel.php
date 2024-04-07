<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carousel Example</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="unc">
        <div class="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../img/medalla.jpg" alt="Image 1">
    </div>
    <div class="carousel-item">
      <img src="../img/copa.jpg" alt="Image 2">
    </div>
    <div class="carousel-item">
      <img src="../img/reconocimiento.jpg" alt="Image 3">
    </div>
  </div>
  <button class="carousel-control prev">&#10094;</button>
  <button class="carousel-control next">&#10095;</button>
</div>
    </div>

<script src=""></script>
</body>
</html>
<style>
    .unc{
        height: 1000px;
        width: 100%;
    }
    .carousel {
  position: relative;
  width: 60%;
  margin: auto;
  overflow: hidden;
}

.carousel-inner {
  display: flex;
}

.carousel-item {
  flex: 0 0 100%;
  display: none;
}

.carousel-item.active {
  display: block;
}

.carousel-item img {
  width: 100%;
  height: 80%;
}

.carousel-control {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  padding: 10px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.carousel-control:hover {
  background: rgba(0, 0, 0, 0.8);
}

.prev {
  left: 0;
}

.next {
  right: 0;
}

</style>
<script>
   document.addEventListener("DOMContentLoaded", function() {
  const carouselItems = document.querySelectorAll(".carousel-item");
  let currentIndex = 0;

  function showSlide(index) {
    carouselItems.forEach((item, i) => {
      if (i === index) {
        item.classList.add("active");
      } else {
        item.classList.remove("active");
      }
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % carouselItems.length;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
    showSlide(currentIndex);
  }

  document.querySelector(".next").addEventListener("click", nextSlide);
  document.querySelector(".prev").addEventListener("click", prevSlide);

  showSlide(currentIndex);
});
 
</script>
