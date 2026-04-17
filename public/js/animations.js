(function () {

    // AOS init
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    }

    // Scroll reveal
    var revealElements = document.querySelectorAll('.scroll-reveal, .stagger-children');

    function checkReveal() {
        for (var i = 0; i < revealElements.length; i++) {
            var el = revealElements[i];
            var rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight - 80) {
                el.classList.add('revealed');
            }
        }
    }

    window.addEventListener('scroll', checkReveal);
    window.addEventListener('load', checkReveal);
    checkReveal();

    // Parallax effect
    var parallaxEls = document.querySelectorAll('[data-parallax-speed]');

    function updateParallax() {
        var scrollY = window.pageYOffset;
        for (var i = 0; i < parallaxEls.length; i++) {
            var el = parallaxEls[i];
            var speed = parseFloat(el.getAttribute('data-parallax-speed')) || 0.3;
            var yOffset = -(scrollY * speed);
            el.style.transform = 'translate3d(0, ' + yOffset + 'px, 0)';
        }
    }

    if (parallaxEls.length > 0) {
        window.addEventListener('scroll', updateParallax);
        updateParallax();
    }

    // 3D tilt on product cards
    var tiltCards = document.querySelectorAll('.product-card');

    for (var i = 0; i < tiltCards.length; i++) {
        (function (card) {
            card.addEventListener('mousemove', function (e) {
                var rect = card.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var centerX = rect.width / 2;
                var centerY = rect.height / 2;
                var rotateX = ((y - centerY) / centerY) * -5;
                var rotateY = ((x - centerX) / centerX) * 5;
                card.style.transform = 'perspective(800px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-6px)';
            });

            card.addEventListener('mouseleave', function () {
                card.style.transform = '';
            });
        })(tiltCards[i]);
    }

    // Navbar scroll shadow
    var navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

})();
