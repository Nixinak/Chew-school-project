document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('emailPopup');
    const overlay = document.querySelector('.popup-overlay');
    const noThanksButton = document.querySelector('.no-thanks-button');
    
    function shp() {
    popup.classList.add('active');
    overlay.classList.add('active');
    }
    
    function hdp() {
    popup.classList.remove('active');
    overlay.classList.remove('active');
    }

    shp();
    
    if (noThanksButton) {
    noThanksButton.addEventListener('click', hdp);
    }

    overlay.addEventListener('click', hdp);
    });