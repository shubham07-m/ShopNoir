(function () {

    // Delete confirmation
    var deleteBtns = document.querySelectorAll('.delete-product-btn');
    for (var i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].addEventListener('click', function (e) {
            var name = this.getAttribute('data-product-name') || 'this product';
            if (!confirm('Delete "' + name + '"?\n\nThis action cannot be undone.')) {
                e.preventDefault();
            }
        });
    }

    // Textarea auto-resize
    var textareas = document.querySelectorAll('.admin-body textarea');
    for (var i = 0; i < textareas.length; i++) {
        textareas[i].addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    }

    // Form submission spinner
    var forms = document.querySelectorAll('.admin-body form');
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function () {
            var btn = this.querySelector('.admin-btn-primary');
            if (btn) {
                btn.innerHTML = 'Saving...';
                btn.disabled = true;
                btn.style.opacity = '0.7';
            }
        });
    }

    // Image preview
    var imageInput = document.getElementById('product-image-url');
    var imagePreview = document.getElementById('image-preview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('input', function () {
            var val = this.value.trim();
            if (val) {
                imagePreview.src = val;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // Sidebar active state
    var path = window.location.pathname;
    var sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    for (var i = 0; i < sidebarLinks.length; i++) {
        var href = sidebarLinks[i].getAttribute('href');
        if (path.indexOf(href) !== -1) {
            sidebarLinks[i].classList.add('active');
        }
    }

})();
