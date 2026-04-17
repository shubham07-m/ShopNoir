(function () {
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) return;

    var glow = document.createElement('div');
    glow.className = 'cursor-glow';
    document.body.appendChild(glow);

    var mouseX = 0, mouseY = 0;
    var glowX = 0, glowY = 0;

    document.addEventListener('mousemove', function (e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });

    function animateGlow() {
        glowX += (mouseX - glowX) * 0.12;
        glowY += (mouseY - glowY) * 0.12;
        glow.style.left = glowX + 'px';
        glow.style.top = glowY + 'px';
        requestAnimationFrame(animateGlow);
    }
    animateGlow();

    var hoverSelectors = 'a, button, .product-card, .category-card, .stat-card, input, textarea, select';

    document.addEventListener('mouseover', function (e) {
        if (e.target.closest(hoverSelectors)) {
            glow.classList.add('glow-active');
        }
    });

    document.addEventListener('mouseout', function (e) {
        if (e.target.closest(hoverSelectors)) {
            glow.classList.remove('glow-active');
        }
    });

    document.addEventListener('mouseleave', function () {
        glow.style.opacity = '0';
    });

    document.addEventListener('mouseenter', function () {
        glow.style.opacity = '1';
    });
})();
