<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<div class="container" id="cntnr">
    <!-- Inbox Section -->
    <h2 class="section-header">Inbox</h2>
    <section id="inbox" class="sub-container">
        <p>
            <!-- Add content for Inbox here -->
        </p>
    </section>

    <!-- Outbox Section -->
    <h2 class="section-header">Outbox</h2>
    <section id="outbox" class="sub-container">
        <p>
            <!-- Add content for Outbox here -->
        </p>
    </section>
</div>

<button id="scrollToTopBtn" class="scroll-to-top-btn"></button>

<!-- Scroll to Top Button Script -->
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

<!-- Fade-in Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.container').classList.add('fade-in');
});

var container = document.getElementById("cntnr");
container.style.opacity = 1;
</script>
