<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<div class="container" id="cntnr">
  <div class="release">

    <h2>Release Version - 1.0.1</h2>

    <h3>New Features</h3>
    <ul>
      <li>Added firends search</li>
      <li></li>
    </ul>

    <h3>Bug Fixes</h3>
    <ul>
      <li>Minor UI updates</li>
      <li></li>
    </ul>
  </div>
  <!--<img src="../res/changelog.png" alt="Image" class="corner-image"> -->
</div>



<!--<button id="scrollToTopBtn" class="scroll-to-top-btn"></button> -->

<!--
 
<script>
const scrollToTopButton = document.querySelector(".scroll-to-top-btn");

window.addEventListener("scroll", () => {
    if (window.pageYOffset > 100) {
        scrollToTopButton.classList.add("show");
    } else {
        scrollToTopButton.classList.remove("show");
    }
});

scrollToTopButton.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
</script>
-->

<!-- Fade-in Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.container').classList.add('fade-in');
});

var container = document.getElementById("cntnr");
container.style.opacity = 1;
</script>
