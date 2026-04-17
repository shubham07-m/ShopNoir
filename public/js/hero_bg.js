(function () {
    var canvas = document.getElementById('hero-canvas');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var particles = [];
    var mouse = { x: null, y: null };
    var PARTICLE_COUNT = 80;
    var CONNECT_DIST = 120;
    var MOUSE_RADIUS = 150;

    var colors = [
        'rgba(99, 102, 241, ',
        'rgba(139, 92, 246, ',
        'rgba(236, 72, 153, ',
        'rgba(59, 130, 246, ',
        'rgba(168, 85, 247, ',
    ];

    function resize() {
        var hero = canvas.parentElement;
        canvas.width = hero.offsetWidth;
        canvas.height = hero.offsetHeight;
    }

    function Particle() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.vx = (Math.random() - 0.5) * 0.6;
        this.vy = (Math.random() - 0.5) * 0.6;
        this.radius = 1.5 + Math.random() * 2;
        this.color = colors[Math.floor(Math.random() * colors.length)];
        this.alpha = 0.3 + Math.random() * 0.5;
        this.pulseSpeed = 0.01 + Math.random() * 0.02;
        this.pulseOffset = Math.random() * Math.PI * 2;
    }

    Particle.prototype.update = function (time) {
        this.x += this.vx;
        this.y += this.vy;

        if (this.x < 0) this.x = canvas.width;
        if (this.x > canvas.width) this.x = 0;
        if (this.y < 0) this.y = canvas.height;
        if (this.y > canvas.height) this.y = 0;

        if (mouse.x !== null) {
            var dx = this.x - mouse.x;
            var dy = this.y - mouse.y;
            var dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < MOUSE_RADIUS) {
                var force = (MOUSE_RADIUS - dist) / MOUSE_RADIUS;
                this.x += dx * force * 0.02;
                this.y += dy * force * 0.02;
            }
        }

        this.currentAlpha = this.alpha + Math.sin(time * this.pulseSpeed + this.pulseOffset) * 0.15;
    };

    Particle.prototype.draw = function () {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
        ctx.fillStyle = this.color + this.currentAlpha + ')';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius * 3, 0, Math.PI * 2);
        ctx.fillStyle = this.color + (this.currentAlpha * 0.15) + ')';
        ctx.fill();
    };

    function init() {
        resize();
        particles = [];
        for (var i = 0; i < PARTICLE_COUNT; i++) {
            particles.push(new Particle());
        }
    }

    function drawConnections() {
        for (var i = 0; i < particles.length; i++) {
            for (var j = i + 1; j < particles.length; j++) {
                var dx = particles[i].x - particles[j].x;
                var dy = particles[i].y - particles[j].y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECT_DIST) {
                    var opacity = (1 - dist / CONNECT_DIST) * 0.15;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);

                    var grad = ctx.createLinearGradient(
                        particles[i].x, particles[i].y,
                        particles[j].x, particles[j].y
                    );
                    grad.addColorStop(0, particles[i].color + opacity + ')');
                    grad.addColorStop(1, particles[j].color + opacity + ')');
                    ctx.strokeStyle = grad;
                    ctx.lineWidth = 0.5;
                    ctx.stroke();
                }
            }
        }

        if (mouse.x !== null) {
            for (var k = 0; k < particles.length; k++) {
                var mdx = particles[k].x - mouse.x;
                var mdy = particles[k].y - mouse.y;
                var mdist = Math.sqrt(mdx * mdx + mdy * mdy);
                if (mdist < MOUSE_RADIUS) {
                    var mopacity = (1 - mdist / MOUSE_RADIUS) * 0.3;
                    ctx.beginPath();
                    ctx.moveTo(particles[k].x, particles[k].y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = 'rgba(168, 85, 247, ' + mopacity + ')';
                    ctx.lineWidth = 0.8;
                    ctx.stroke();
                }
            }
        }
    }

    var time = 0;
    function animate() {
        time++;
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        var bgGrad = ctx.createRadialGradient(
            canvas.width * 0.3, canvas.height * 0.4, 0,
            canvas.width * 0.3, canvas.height * 0.4, canvas.width * 0.7
        );
        bgGrad.addColorStop(0, 'rgba(99, 102, 241, 0.06)');
        bgGrad.addColorStop(0.5, 'rgba(139, 92, 246, 0.03)');
        bgGrad.addColorStop(1, 'transparent');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        var bgGrad2 = ctx.createRadialGradient(
            canvas.width * 0.7, canvas.height * 0.6, 0,
            canvas.width * 0.7, canvas.height * 0.6, canvas.width * 0.5
        );
        bgGrad2.addColorStop(0, 'rgba(236, 72, 153, 0.04)');
        bgGrad2.addColorStop(1, 'transparent');
        ctx.fillStyle = bgGrad2;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for (var i = 0; i < particles.length; i++) {
            particles[i].update(time);
            particles[i].draw();
        }

        drawConnections();
        requestAnimationFrame(animate);
    }

    canvas.addEventListener('mousemove', function (e) {
        var rect = canvas.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });

    canvas.addEventListener('mouseleave', function () {
        mouse.x = null;
        mouse.y = null;
    });

    window.addEventListener('resize', function () {
        resize();
    });

    init();
    animate();
})();
