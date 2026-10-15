// Movimento do banner.png (img.hero_banner_img_index)
// Flutua suavemente e inclina de leve seguindo o mouse.
(function () {
    'use strict';

    function iniciarBanner() {
        var img = document.querySelector('.hero_banner_img_index');
        if (!img) return;

        // Respeita quem prefere menos animação
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        var hero = img.closest('.hero_index') || document.body;

        var alvoX = 0, alvoY = 0;   // posição desejada do mouse (-1 a 1)
        var atualX = 0, atualY = 0; // posição suavizada

        img.style.willChange = 'transform';
        img.style.transition = 'none';

        hero.addEventListener('mousemove', function (e) {
            var r = hero.getBoundingClientRect();
            alvoX = ((e.clientX - r.left) / r.width - 0.5) * 2;
            alvoY = ((e.clientY - r.top) / r.height - 0.5) * 2;
        });

        hero.addEventListener('mouseleave', function () {
            alvoX = 0;
            alvoY = 0;
        });

        function animar(t) {
            // suaviza o acompanhamento do mouse
            atualX += (alvoX - atualX) * 0.06;
            atualY += (alvoY - atualY) * 0.06;

            var segundos = t / 1000;
            var flutuar = Math.sin(segundos * 1.4) * 12;      // sobe e desce (px)
            var balanco = Math.sin(segundos * 0.9) * 1.2;     // rotação leve (graus)

            var moverX = atualX * 14;
            var moverY = atualY * 10 + flutuar;
            var rotY = atualX * 6;
            var rotX = -atualY * 6;

            img.style.transform =
                'perspective(900px) ' +
                'translate3d(' + moverX.toFixed(2) + 'px,' + moverY.toFixed(2) + 'px,0) ' +
                'rotateX(' + rotX.toFixed(2) + 'deg) ' +
                'rotateY(' + rotY.toFixed(2) + 'deg) ' +
                'rotateZ(' + balanco.toFixed(2) + 'deg)';

            requestAnimationFrame(animar);
        }

        requestAnimationFrame(animar);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarBanner);
    } else {
        iniciarBanner();
    }
})();