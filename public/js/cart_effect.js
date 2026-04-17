(function () {

    function bounceBadge() {
        var badge = document.getElementById('cart-badge');
        if (!badge) return;
        badge.style.animation = 'none';
        void badge.offsetWidth;
        badge.style.animation = 'badgePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
    }

    window.initCartEffects = function () {
        var cartBadge = document.getElementById('cart-badge');

        function cartRequest(payload, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'cart_api.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        callback(data);
                    } catch (e) {
                        console.error('Cart API parse error');
                    }
                }
            };
            xhr.onerror = function () {
                console.error('Cart API request failed');
            };
            xhr.send(JSON.stringify(payload));
        }

        function updateBadge(count) {
            if (count > 0) {
                cartBadge.textContent = count;
                cartBadge.style.display = 'flex';
                bounceBadge();
            } else {
                cartBadge.style.display = 'none';
            }
        }

        function showAdded(btn) {
            btn.classList.add('added');
            btn.textContent = '\u2714';
            setTimeout(function () {
                btn.classList.remove('added');
                btn.textContent = '';
            }, 1000);
        }

        var addBtns = document.querySelectorAll('.add-to-cart-btn');
        for (var i = 0; i < addBtns.length; i++) {
            addBtns[i].addEventListener('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                var btn = this;
                var index = parseInt(btn.getAttribute('data-product-index'));

                cartRequest({ action: 'add', product_index: index }, function (data) {
                    showAdded(btn);
                    updateBadge(data.total_items);
                });
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initCartEffects);
    } else {
        window.initCartEffects();
    }

})();
