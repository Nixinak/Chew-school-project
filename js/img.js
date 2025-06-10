document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    let currentIndex = 0;
    const intervalTime = 4000;

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
    }

    function nextSlide() {
        slides[currentIndex].classList.remove('active');
        currentIndex++;
        if (currentIndex >= slides.length) {
            currentIndex = 0;
        }
        setTimeout(() => {
            showSlide(currentIndex);
        }, 200);
    }

    setInterval(nextSlide, intervalTime);

    showSlide(currentIndex);
});