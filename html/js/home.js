// Página inicial: efeitos de rolagem e de mouse.
// O movimento do banner (flutuar + inclinar) continua no css/script.js; aqui ficam os demais.
(function () {
    'use strict';

    var raiz = document.documentElement;
    raiz.classList.add('js-ready'); // avisa ao <head> que o JS carregou (senão ele desfaz o "js")

    var reduzir  = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var temMouse = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    function limitar(v, min, max) { return Math.min(max, Math.max(min, v)); }

    function iniciar() {
        var cabecalho = document.querySelector('.header_index');
        var progresso = document.querySelector('.scroll_progress_index');
        var brilho    = document.querySelector('.cursor_glow_index');
        var hero      = document.querySelector('.hero_index');
        var heroTexto = document.querySelector('.hero_text_index');
        var heroImg   = document.querySelector('.hero_image_index');
        var decos     = Array.prototype.slice.call(document.querySelectorAll('.deco_index'));
        var botoes    = Array.prototype.slice.call(document.querySelectorAll('.hero_buttons_index .btn_primary_index, .hero_buttons_index .btn_outline_index'));

        var movimento = !reduzir; // parallax, brilho, inclinação, botões magnéticos

        // alvo = onde o mouse está; suave = valor que "persegue" o alvo (dá a sensação de inércia)
        var alvo  = { mx: 0, my: 0, px: 0, py: 0 };
        var suave = { mx: 0, my: 0, px: 0, py: 0 };
        var alturaHero = hero ? hero.offsetHeight : 0;
        var pendente = false;

        function pedirFrame() {
            if (!pendente) {
                pendente = true;
                requestAnimationFrame(frame);
            }
        }

        function frame() {
            pendente = false;
            var y = window.pageYOffset || document.documentElement.scrollTop || 0;

            // ----- barra de progresso e cabeçalho (todo mundo) -----
            if (progresso) {
                var total = document.documentElement.scrollHeight - window.innerHeight;
                progresso.style.setProperty('--progresso', total > 0 ? limitar(y / total, 0, 1).toFixed(4) : 0);
            }
            if (cabecalho) {
                cabecalho.classList.toggle('compacto_index', y > 24);
            }

            if (!movimento) { return; }

            // ----- hero: o texto sobe devagar e esmaece, a imagem anda ao contrário -----
            var p = alturaHero ? limitar(y / alturaHero, 0, 1) : 0;
            if (heroTexto) {
                heroTexto.style.translate = '0 ' + (p * 70).toFixed(1) + 'px';
                heroTexto.style.opacity = (1 - p * 0.75).toFixed(3);
            }
            if (heroImg) {
                heroImg.style.translate = '0 ' + (p * -40).toFixed(1) + 'px';
            }

            // ----- inércia do mouse -----
            var dmx = alvo.mx - suave.mx, dmy = alvo.my - suave.my;
            var dpx = alvo.px - suave.px, dpy = alvo.py - suave.py;
            suave.mx += dmx * 0.08;  suave.my += dmy * 0.08;
            suave.px += dpx * 0.18;  suave.py += dpy * 0.18;

            // ----- formas do hero: cada uma anda numa "profundidade" diferente -----
            decos.forEach(function (d) {
                var prof = parseFloat(d.dataset.depth) || 0.3;
                var x = suave.mx * prof * 46;
                var yy = suave.my * prof * 46 - Math.min(y, alturaHero) * prof * 0.35;
                d.style.translate = x.toFixed(1) + 'px ' + yy.toFixed(1) + 'px';
            });

            // ----- brilho do cursor -----
            if (brilho) {
                brilho.style.transform = 'translate3d(' + suave.px.toFixed(1) + 'px,' + suave.py.toFixed(1) + 'px,0)';
            }

            // continua animando enquanto o valor suave ainda não chegou no alvo
            if (Math.abs(dmx) > 0.001 || Math.abs(dmy) > 0.001 || Math.abs(dpx) > 0.5 || Math.abs(dpy) > 0.5) {
                pedirFrame();
            }
        }

        window.addEventListener('scroll', pedirFrame, { passive: true });
        window.addEventListener('resize', function () {
            alturaHero = hero ? hero.offsetHeight : 0;
            pedirFrame();
        });

        // ---------- mouse: parallax, brilho e botões magnéticos ----------
        function atrairBotoes(x, y) {
            botoes.forEach(function (b) {
                var tx = b._tx || 0, ty = b._ty || 0;
                var r = b.getBoundingClientRect();
                // centro "de verdade" (sem o deslocamento que a gente mesmo aplicou)
                var cx = r.left + r.width / 2 - tx;
                var cy = r.top + r.height / 2 - ty;
                var dx = x - cx, dy = y - cy;
                if (Math.hypot(dx, dy) < 120) {
                    b._tx = limitar(dx * 0.28, -12, 12);
                    b._ty = limitar(dy * 0.28, -12, 12);
                } else {
                    b._tx = 0;
                    b._ty = 0;
                }
                b.style.setProperty('--tx', b._tx.toFixed(1) + 'px');
                b.style.setProperty('--ty', b._ty.toFixed(1) + 'px');
            });
        }

        if (movimento && temMouse) {
            window.addEventListener('pointermove', function (e) {
                if (e.pointerType !== 'mouse') { return; }
                alvo.mx = (e.clientX / window.innerWidth - 0.5) * 2;
                alvo.my = (e.clientY / window.innerHeight - 0.5) * 2;
                alvo.px = e.clientX;
                alvo.py = e.clientY;
                if (brilho && !brilho.classList.contains('ativo_index')) {
                    // primeira vez: já nasce no lugar certo, sem "voar" desde o canto
                    suave.px = alvo.px;
                    suave.py = alvo.py;
                    brilho.classList.add('ativo_index');
                }
                atrairBotoes(e.clientX, e.clientY);
                pedirFrame();
            }, { passive: true });

            document.documentElement.addEventListener('mouseleave', function () {
                alvo.mx = 0;
                alvo.my = 0;
                if (brilho) { brilho.classList.remove('ativo_index'); }
                botoes.forEach(function (b) {
                    b._tx = 0; b._ty = 0;
                    b.style.setProperty('--tx', '0px');
                    b.style.setProperty('--ty', '0px');
                });
                pedirFrame();
            });

            // ---------- cartões: inclinação 3D + brilho que segue o mouse ----------
            var cartoes = document.querySelectorAll('.feature_card_index, .comment_card_index');
            Array.prototype.forEach.call(cartoes, function (card) {
                card.addEventListener('pointermove', function (e) {
                    if (e.pointerType !== 'mouse') { return; }
                    var r = card.getBoundingClientRect();
                    var x = (e.clientX - r.left) / r.width;
                    var y = (e.clientY - r.top) / r.height;
                    card.style.setProperty('--ry', ((x - 0.5) * 10).toFixed(2) + 'deg');
                    card.style.setProperty('--rx', ((0.5 - y) * 10).toFixed(2) + 'deg');
                    card.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
                    card.style.setProperty('--my', (y * 100).toFixed(1) + '%');
                });
                card.addEventListener('pointerleave', function () {
                    card.style.setProperty('--rx', '0deg');
                    card.style.setProperty('--ry', '0deg');
                });
            });

            // ---------- fundadores: a bolinha roxa de trás foge do mouse ----------
            var fundadores = document.querySelectorAll('.founder_card_index');
            Array.prototype.forEach.call(fundadores, function (card) {
                var aro = card.querySelector('.founder_photo_wrap_index');
                if (!aro) { return; }
                card.addEventListener('pointermove', function (e) {
                    if (e.pointerType !== 'mouse') { return; }
                    var r = card.getBoundingClientRect();
                    var x = (e.clientX - r.left) / r.width - 0.5;
                    var y = (e.clientY - r.top) / r.height - 0.5;
                    aro.style.setProperty('--px', (-x * 34).toFixed(1) + 'px');
                    aro.style.setProperty('--py', (-y * 34).toFixed(1) + 'px');
                });
                card.addEventListener('pointerleave', function () {
                    aro.style.setProperty('--px', '0px');
                    aro.style.setProperty('--py', '0px');
                });
            });
        }

        // ---------- aparecer ao rolar ----------
        var revelar = Array.prototype.slice.call(document.querySelectorAll(
            '.features_title_index, .feature_card_index, .comments_title_index, .comments_subtitle_index, ' +
            '.comment_card_index, .founders_title_index, .founder_card_index, .founders_description_index'
        ));

        // atraso escalonado dentro de cada grade (0, 100ms, 200ms...)
        ['.features_grid_index', '.comments_grid_index', '.founders_grid_index'].forEach(function (seletor) {
            var grade = document.querySelector(seletor);
            if (!grade) { return; }
            Array.prototype.forEach.call(grade.children, function (filho, i) {
                filho.style.setProperty('--i', i % 3);
            });
        });

        if (reduzir || !('IntersectionObserver' in window)) {
            revelar.forEach(function (el) { el.classList.add('visivel_index'); });
        } else {
            var obsRevelar = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (en) {
                    if (en.isIntersecting) {
                        en.target.classList.add('visivel_index');
                        obsRevelar.unobserve(en.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -6% 0px' });
            revelar.forEach(function (el) { obsRevelar.observe(el); });
        }

        // ---------- menu: marca o link da seção que está na tela ----------
        var links = {
            '#home':   document.querySelector('.nav_list_index a[href="#home"]'),
            '#sarolau': document.querySelector('.nav_list_index a[href="#sarolau"]')
        };
        // seções "features" e "comments" fazem parte de "Sobre o site"
        var secaoParaLink = { home: '#home', features: '#home', comments: '#home', sarolau: '#sarolau' };

        if ('IntersectionObserver' in window) {
            var obsMenu = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (en) {
                    if (!en.isIntersecting) { return; }
                    var alvoLink = secaoParaLink[en.target.id];
                    Object.keys(links).forEach(function (href) {
                        var a = links[href];
                        if (!a) { return; }
                        var ativo = (href === alvoLink);
                        a.classList.toggle('ativo_index', ativo);
                        if (ativo) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); }
                    });
                });
            }, { rootMargin: '-45% 0px -50% 0px' }); // faixa fina no meio da tela
            Object.keys(secaoParaLink).forEach(function (id) {
                var s = document.getElementById(id);
                if (s) { obsMenu.observe(s); }
            });
        }

        pedirFrame(); // estado inicial (progresso, cabeçalho, hero)
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();
