(function () {
    var loader = document.getElementById('page-loader');
    if (!loader) return;

    window.addEventListener('load', function () {
        setTimeout(function () {
            loader.classList.add('loaded');
        }, 600);

        setTimeout(function () {
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
        }, 1400);
    });
})();
