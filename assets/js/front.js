/* Front-page interactions for Toyota Monagas theme */
(function () {
  var SLIDE_DURATION = 13170; // milisegundos

  function setHeaderOffset() {
    try {
      var header = document.querySelector('.site-header');
      var main = document.getElementById('site-main');
      if (!header) return;
      var h = header.offsetHeight || 0;
      document.documentElement.style.setProperty('--header-h', h + 'px');
      if (main) { main.style.paddingTop = ''; }
    } catch (e) { }
  }

  function revealOnView() {
    var headers = document.querySelectorAll('.section-header');
    var reveals = [];
    reveals.push.apply(reveals, document.querySelectorAll('#vehiculos .toyota-card'));
    reveals.push.apply(reveals, document.querySelectorAll('#info-mm .service-card'));
    reveals.push.apply(reveals, document.querySelectorAll('#productos-mm .producto-card'));
    reveals.push.apply(reveals, document.querySelectorAll('#blog-mm .blog-card'));

    reveals.forEach(function (el) { el.classList.add('reveal-on-scroll'); });

    if (!('IntersectionObserver' in window)) {
      headers.forEach(function (h) { h.classList.add('in-view'); });
      reveals.forEach(function (el) { el.classList.add('in'); });
      return;
    }

    var ioHeaders = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          ioHeaders.unobserve(entry.target);
        }
      });
    }, { threshold: 0.2 });
    headers.forEach(function (h) { ioHeaders.observe(h); });

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    reveals.forEach(function (el) { io.observe(el); });
  }

  function initHeroSlider() {
    var slider = document.getElementById('custom-slider');
    if (!slider) return;

    // If already prepared, avoid duplicate setup
    if (slider.__heroPrepared) return;
    slider.__heroPrepared = true;

    var progressSpans = Array.prototype.slice.call(slider.querySelectorAll('.cs-progress-bar span'));
    if (!progressSpans.length) return;

    var swiperInstance = null;
    var pausedByHover = false;
    var rafId = 0;
    var lastTs = 0;
    var progressMs = 0;
    var activeIndex = 0;
    var activationToken = 0;

    function setWidth(span, value, instant) {
      if (!span) return;
      if (instant) {
        span.classList.add('no-transition');
        span.style.width = value;
        span.getBoundingClientRect();
        span.classList.remove('no-transition');
      } else {
        span.style.width = value;
      }
    }

    function markBars(idx) {
      progressSpans.forEach(function (span, i) {
        span.classList.remove('active');
        setWidth(span, i < idx ? '100%' : '0%', true);
      });
      if (progressSpans[idx]) {
        progressSpans[idx].classList.add('active');
        setWidth(progressSpans[idx], '0%', true);
      }
    }

    function pauseAllVideos() {
      slider.querySelectorAll('video').forEach(function (video) {
        try { video.pause(); } catch (e) { }
        video.onended = null;
      });
    }

    function stopProgress() {
      if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = 0;
      }
    }

    function step(now) {
      if (!swiperInstance) return;

      if (!progressSpans[activeIndex]) {
        if (!lastTs) lastTs = now;
        rafId = requestAnimationFrame(step);
        return;
      }

      var span = progressSpans[activeIndex];
      var activeSlide = swiperInstance.slides[swiperInstance.activeIndex];
      var activeVideo = activeSlide ? activeSlide.querySelector('video') : null;
      var isWorkingVideo = activeVideo && !isNaN(activeVideo.duration) && activeVideo.duration > 0;
      var currentDuration = SLIDE_DURATION;

      if (isWorkingVideo) {
        currentDuration = activeVideo.duration * 1000;
      }

      if (!lastTs) lastTs = now;
      if (!pausedByHover) {
        progressMs += (now - lastTs);
      }
      lastTs = now;

      if (isWorkingVideo && activeVideo.readyState >= 2) {
        progressMs = activeVideo.currentTime * 1000;
        
        if (pausedByHover && !activeVideo.paused) {
          try { activeVideo.pause(); } catch(e){}
        } else if (!pausedByHover && activeVideo.paused && activeVideo.currentTime < activeVideo.duration) {
          try { activeVideo.play(); } catch(e){}
        }
      }

      var pct = Math.min(100, (progressMs / currentDuration) * 100);
      span.style.width = pct + '%';

      if (progressMs >= currentDuration) {
        span.style.width = '100%';
        if (!isWorkingVideo) {
          swiperInstance.slideNext();
          return;
        }
      }

      rafId = requestAnimationFrame(step);
    }

    function startProgress() {
      progressMs = 0;
      lastTs = 0;
      stopProgress();
      rafId = requestAnimationFrame(step);
    }

    function assignVideoSource(video) {
      if (!video) return;
      var dataSrc = video.getAttribute('data-src');
      if (dataSrc && video._toyotaSrc !== dataSrc) {
        video.src = dataSrc;
        video._toyotaSrc = dataSrc;
      }
      var poster = video.getAttribute('data-poster');
      if (poster && !video.getAttribute('poster')) {
        video.setAttribute('poster', poster);
      }
    }

    function ensureVideo(video) {
      return new Promise(function (resolve) {
        if (!video) { resolve(); return; }
        assignVideoSource(video);
        video.muted = true;
        video.setAttribute('muted', '');
        video.playsInline = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('preload', 'auto');
        video.preload = 'auto';
        video.removeAttribute('loop');

        function tryPlay() {
          // Reset time just before play to avoid glitch
          try { video.currentTime = 0; } catch (e) { }
          var playPromise;
          try {
            playPromise = video.play();
          } catch (err) {
            requestData();
            return;
          }
          if (playPromise && typeof playPromise.then === 'function') {
            playPromise.then(resolve).catch(requestData);
          } else {
            resolve();
          }
        }

        // Fallback: If video doesn't resolve in 3 seconds, resolve anyway so slider isn't permanently stuck
        setTimeout(resolve, 3000);

        function requestData() {
          video.addEventListener('canplaythrough', onReady, { once: true });
          video.addEventListener('loadeddata', onReady, { once: true });
          try { video.load(); } catch (e) { }
        }

        function onReady() {
          // Ensure time is 0 after data loaded
          try { video.currentTime = 0; } catch (e) { }
          tryPlay();
        }

        // Check if already ready
        if (video.readyState >= 3) {
          tryPlay();
        } else {
          requestData();
        }
      });
    }

    function primeSlide(swiper, offset) {
      if (!swiper || !swiper.slides || !swiper.slides.length) return;
      var total = swiper.slides.length;
      if (!total) return;
      var base = typeof swiper.activeIndex === 'number' ? swiper.activeIndex : 0;
      var idx = base + offset;
      if (idx < 0) { idx = ((idx % total) + total) % total; }
      if (idx >= total) { idx = idx % total; }
      var slide = swiper.slides[idx];
      if (!slide) return;
      var vids = slide.querySelectorAll('video');
      vids.forEach(function(vid) {
        assignVideoSource(vid);
        try { vid.load(); } catch (e) { }
      });
    }

    function primeAround(swiper) {
      if (!swiper) return;
      primeSlide(swiper, 1);
      primeSlide(swiper, 2);
    }
    function getRealIndex(swiper) {
      var total = progressSpans.length || 1;
      var idx = typeof swiper.realIndex === 'number' ? swiper.realIndex : 0;
      if (idx < 0) idx = 0;
      return idx % total;
    }

    function activate(swiper) {
      activationToken++;
      var token = activationToken;
      stopProgress();
      pauseAllVideos();

      activeIndex = getRealIndex(swiper);
      markBars(activeIndex);

      var activeSlide = swiper.slides[swiper.activeIndex];
      var videos = activeSlide ? Array.prototype.slice.call(activeSlide.querySelectorAll('video')) : [];

      function begin() {
        if (token !== activationToken) return;
        if (videos.length > 0) {
          videos.forEach(function(v) {
            v.onended = function () {
              // Advance to next slide immediately when video ends, regardless of hover state
              swiper.slideNext();
            };
          });
        }
        startProgress();
        primeAround(swiper);
      }

      if (videos.length > 0) {
        Promise.all(videos.map(ensureVideo)).then(begin);
      } else {
        begin();
        primeAround(swiper);
      }
    }

    function bindEvents(inst) {
      if (!inst) return;
      inst.on('init', function () { activate(inst); });
      inst.on('slideChangeTransitionStart', function () {
        stopProgress();
        pauseAllVideos();
      });
      inst.on('slideChange', function () { activate(inst); primeAround(inst); });
    }

    function createOrAttach() {
      // If already has Swiper instance created elsewhere (e.g., app.ts), reuse it
      if (slider.swiper) {
        swiperInstance = slider.swiper;
        bindEvents(swiperInstance);
        // Manually trigger to sync bars on first load
        setTimeout(function () { activate(swiperInstance); }, 0);
        return;
      }
      swiperInstance = new Swiper('#custom-slider', {
        loop: true,
        navigation: {
          nextEl: '.cs-swiper-button-next',
          prevEl: '.cs-swiper-button-prev'
        },
        allowTouchMove: true,
        observer: true,
        observeParents: true
      });
      bindEvents(swiperInstance);
      // Swiper initializes immediately; ensure progress starts
      setTimeout(function () { activate(swiperInstance); }, 0);
    }

    if (typeof Swiper === 'undefined') return; // Swiper is enqueued via WordPress
    createOrAttach();

    slider.addEventListener('mouseenter', function () {
      pausedByHover = true;
    });

    slider.addEventListener('mouseleave', function () {
      pausedByHover = false;
    });
  }

  function initVehiculos() {
    var wrapper = document.querySelector('#vehiculos .toyota-slider .swiper-wrapper');
    var templates = Array.from(document.querySelectorAll('#vehiculos .toyota-templates template'));
    var tabs = document.querySelectorAll('#vehiculos .toyota-tab');
    var tablist = document.querySelector('#vehiculos .toyota-tabs');
    var scroller = document.querySelector('#vehiculos .toyota-nav') || tablist;
    var indicator = document.querySelector('#vehiculos .toyota-tab-indicator');
    var arrowPrev = document.querySelector('#vehiculos .toyota-arrow.swiper-button-prev');
    var arrowNext = document.querySelector('#vehiculos .toyota-arrow.swiper-button-next');
    if (!wrapper || !templates.length) return;

    var swiper = null;

    function createVehSwiper() {
      // Avoid duplicate init
      var container = document.querySelector('#vehiculos .toyota-slider');
      if (!container) return null;
      if (container.swiper) return container.swiper;

      if (typeof Swiper === 'undefined') return null;

      try {
        var inst = new Swiper('#vehiculos .toyota-slider', {
          spaceBetween: 20,
          navigation: {
            nextEl: '#vehiculos .toyota-arrow.swiper-button-next',
            prevEl: '#vehiculos .toyota-arrow.swiper-button-prev'
          },
          breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
          }
        });
        return inst;
      } catch (e) {
        console.error('Error creating vehicle swiper', e);
        return null;
      }
    }

    function getTemplateCats(tpl) {
      if (!tpl || !tpl.dataset) return [];
      return (tpl.dataset.cat || '').trim().split(/\s+/).filter(Boolean);
    }

    function getDefaultCategory() {
      if (tabs[0] && tabs[0].dataset.cat) return tabs[0].dataset.cat;
      var firstCats = getTemplateCats(templates[0]);
      return firstCats[0] || '';
    }

    function updateArrowState(count) {
      var show = count >= 4;
      var displayValue = show ? '' : 'none';
      [arrowPrev, arrowNext].forEach(function (btn) {
        if (!btn) return;
        btn.style.display = displayValue;
        btn.setAttribute('aria-hidden', show ? 'false' : 'true');
        if (!show) {
          btn.classList.add('swiper-button-disabled');
        } else {
          btn.classList.remove('swiper-button-disabled');
        }
      });
      if (swiper) {
        swiper.allowSlideNext = show;
        swiper.allowSlidePrev = show;
      }
    }

    function buildSlides(category) {
      var cat = category;
      if (!cat || cat === 'all') {
        cat = getDefaultCategory();
      }
      wrapper.innerHTML = '';
      var items = templates.filter(function (t) {
        var cats = getTemplateCats(t);
        if (!cat) return true;
        return cats.indexOf(cat) !== -1;
      });
      if (!items.length) {
        var emptySlide = document.createElement('div');
        // Ensure it spans full width if possible, or just sits as a single slide
        emptySlide.className = 'swiper-slide empty-message-slide';
        // Inline some styles or rely on css class added later
        emptySlide.innerHTML =
          '<div class="toyota-empty-card">' +
          '<div class="tec-icon"><i class="fas fa-shipping-fast"></i></div>' +
          '<h3>Pronto en Stock</h3>' +
          '<p>Estamos renovando el inventario de esta categor&iacute;a.</p>' +
          '<a href="https://wa.me/584249090679?text=Hola,%20quisiera%20consultar%20cuando%20llegan%20nuevos%20vehiculos." target="_blank" class="toyota-btn">Consultar llegada <i class="fab fa-whatsapp"></i></a>' +
          '</div>';

        wrapper.appendChild(emptySlide);

        // Hide arrows since there's nothing to scroll
        updateArrowState(0);
        if (swiper) swiper.update();
        return;
      }
      items.forEach(function (tpl) {
        var slide = document.createElement('div');
        slide.className = 'swiper-slide';
        var cloned = tpl.content.cloneNode(true);
        var card = cloned.querySelector('.toyota-card');
        if (card) {
          var cats = getTemplateCats(tpl);
          if (cats.length) {
            card.setAttribute('data-cat', cats[0]);
          }
        }
        slide.appendChild(cloned);
        wrapper.appendChild(slide);
      });
      updateArrowState(items.length);
      if (swiper) swiper.update();
    }

    function moveIndicator(tab) {
      if (!indicator || !tab) return;
      var rect = tab.getBoundingClientRect();
      var parentRect = tab.parentElement.getBoundingClientRect();
      var width = Math.max(24, rect.width * 0.6);
      var offset = (rect.left - parentRect.left) + (rect.width - width) / 2;
      indicator.style.width = width + 'px';
      indicator.style.transform = 'translateX(' + offset + 'px)';
    }

    function activateTab(tab) {
      if (!tab) return;
      tabs.forEach(function (btn) {
        btn.classList.remove('is-active');
        btn.setAttribute('aria-selected', 'false');
      });
      tab.classList.add('is-active');
      tab.setAttribute('aria-selected', 'true');
    }

    function ensureTabInView(tab, align) {
      if (!scroller || !tab) return;
      if (!window.matchMedia('(max-width: 1024px)').matches) return;
      var tl = scroller;
      var tlRect = tl.getBoundingClientRect();
      var tRect = tab.getBoundingClientRect();
      var targetLeft = tl.scrollLeft + (tRect.left - tlRect.left);
      if (align === 'center') {
        targetLeft = targetLeft - (tlRect.width - tRect.width) / 2;
      }
      if (targetLeft < 0) targetLeft = 0;
      tl.scrollTo({ left: targetLeft, behavior: 'smooth' });
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        if (tab.classList.contains('is-active')) return;
        activateTab(tab);
        buildSlides(tab.dataset.cat);
        moveIndicator(tab);
        ensureTabInView(tab, 'center');
      });
    });

    // Initial setup with retry for Swiper
    var initAttempts = 0;
    function tryInit() {
      if (typeof Swiper !== 'undefined') {
        swiper = createVehSwiper();
        // If swiper created or not needed immediately, proceed
      } else if (initAttempts < 10) {
        initAttempts++;
        setTimeout(tryInit, 200);
        return;
      }

      // Build slides anyway so content is visible even if Swiper fails
      var activeTab = document.querySelector('#vehiculos .toyota-tab.is-active') || tabs[0];
      if (scroller) { scroller.scrollLeft = 0; }
      if (activeTab) {
        activateTab(activeTab);
        buildSlides(activeTab.dataset.cat);
        requestAnimationFrame(function () {
          ensureTabInView(activeTab, 'start');
          moveIndicator(activeTab);
        });
      } else {
        buildSlides();
      }
    }

    tryInit();
  }

  // Load more on /vehiculos page (AJAX)
  function initVehiculosLoadMore() {
    var btn = document.getElementById('toyota-loadmore');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var page = parseInt(btn.getAttribute('data-page') || '2', 10);
      var max = parseInt(btn.getAttribute('data-max') || '1', 10);
      var cat = btn.getAttribute('data-cat') || '';
      if (page > max) { btn.style.display = 'none'; return; }
      btn.disabled = true; var oldText = btn.innerHTML; btn.innerHTML = 'Cargando...';
      var form = new FormData();
      form.append('action', 'toyota_load_more_vehiculos');
      form.append('page', page);
      form.append('cat', cat);
      form.append('nonce', (window.toyota_front_ajax && toyota_front_ajax.nonce) ? toyota_front_ajax.nonce : '');
      fetch((window.toyota_front_ajax && toyota_front_ajax.ajax_url) ? toyota_front_ajax.ajax_url : '/wp-admin/admin-ajax.php', {
        method: 'POST', body: form, credentials: 'same-origin'
      }).then(function (res) { return res.json(); }).then(function (data) {
        if (data && data.success && data.data && data.data.html) {
          var grid = document.querySelector('.vehiculos-grid');
          if (grid) {
            var wrapper = document.createElement('div');
            wrapper.innerHTML = data.data.html;
            // append child nodes with animation (stagger)
            var nodes = Array.from(wrapper.children);
            nodes.forEach(function (node, idx) {
              node.classList.add('is-new');
              grid.appendChild(node);
              // stagger entrance
              setTimeout(function () {
                requestAnimationFrame(function () { node.classList.add('in'); });
              }, idx * 80);
            });
          }
          // increment page and update or hide button
          page++;
          btn.setAttribute('data-page', page);
          if (page > (data.data.max || max)) { btn.style.display = 'none'; }
        } else {
          // failure: hide and log
          console.warn('Load more failed', data);
          btn.style.display = 'none';
        }
      }).catch(function (err) { console.error('AJAX error', err); btn.style.display = 'none'; })
        .finally(function () { btn.disabled = false; btn.innerHTML = oldText; });
    });
  }

  function headerScroll() {
    var header = document.querySelector('.site-header');
    if (!header) return;
    var wasScrolled = window.scrollY > 10;
    function update() {
      var isScrolled = window.scrollY > 10;
      if (isScrolled && !wasScrolled) {
        header.classList.remove('no-anim');
        header.classList.add('scrolled');
      } else if (!isScrolled && wasScrolled) {
        header.classList.add('no-anim');
        header.classList.remove('scrolled');
        setTimeout(function () { header.classList.remove('no-anim'); }, 80);
      }
      wasScrolled = isScrolled;
    }
    update();
    window.addEventListener('scroll', update);
  }

  // Infinite scroll for Blog page
  function initBlogInfinite() {
    var list = document.getElementById('blog-list');
    var sentinel = document.getElementById('blog-sentinel');
    if (!list || !sentinel) return;
    var page = parseInt(list.getAttribute('data-page') || '2', 10);
    var max = parseInt(list.getAttribute('data-max') || '1', 10);
    var loading = false;
    var ajax = (window.toyota_front_ajax && toyota_front_ajax.ajax_url) ? toyota_front_ajax.ajax_url : '/wp-admin/admin-ajax.php';
    var nonce = (window.toyota_front_ajax && toyota_front_ajax.nonce) ? toyota_front_ajax.nonce : '';

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting || loading) return;
        if (page > max) { io.disconnect(); return; }
        loading = true;
        var form = new FormData();
        form.append('action', 'toyota_load_more_posts');
        form.append('page', page);
        form.append('nonce', nonce);
        fetch(ajax, { method: 'POST', body: form, credentials: 'same-origin' })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data && data.success && data.data && data.data.html) {
              var wrap = document.createElement('div');
              wrap.innerHTML = data.data.html;
              Array.from(wrap.children).forEach(function (node, idx) {
                node.classList.add('is-new');
                list.appendChild(node);
                setTimeout(function () { node.classList.add('in'); }, idx * 60);
              });
              page++;
              list.setAttribute('data-page', page);
              if (data.data.max) { max = data.data.max; }
              if (page > max) { io.disconnect(); }
            }
          })
          .catch(function (err) { console.warn('Blog infinite failed', err); io.disconnect(); })
          .finally(function () { loading = false; });
      });
    }, { rootMargin: '360px 0px' });
    io.observe(sentinel);
  }

  // Blog search with autocomplete (AJAX)
  function initBlogSearch() {
    var input = document.getElementById('blog-search-input');
    var box = document.getElementById('blog-search-suggest');
    if (!input || !box) return;
    var ajax = (window.toyota_front_ajax && toyota_front_ajax.ajax_url) ? toyota_front_ajax.ajax_url : '/wp-admin/admin-ajax.php';
    var nonce = (window.toyota_front_ajax && toyota_front_ajax.nonce) ? toyota_front_ajax.nonce : '';
    var tmr = 0;
    function hide() { box.hidden = true; box.innerHTML = ''; }
    function render(items) {
      if (!items || !items.length) { hide(); return; }
      var html = items.map(function (it) {
        var img = it.thumb ? '<img src="' + it.thumb + '" alt="" />' : '';
        return '<a class="sug-item" href="' + it.url + '">' + img + '<span>' + it.title + '</span></a>';
      }).join('');
      box.innerHTML = html + '<a class="sug-more" href="/?s=' + encodeURIComponent(input.value.trim()) + '">Ver todos los resultados</a>';
      box.hidden = false;
    }
    input.addEventListener('input', function () {
      var q = input.value.trim();
      if (tmr) { clearTimeout(tmr); }
      if (q.length < 2) { hide(); return; }
      tmr = setTimeout(function () {
        var url = ajax + '?action=toyota_search_posts&q=' + encodeURIComponent(q) + '&nonce=' + encodeURIComponent(nonce);
        fetch(url, { credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (data) { if (data && data.success) { render(data.data.items || []); } else { hide(); } })
          .catch(function () { hide(); });
      }, 200);
    });
    input.addEventListener('keydown', function (e) { if (e.key === 'Escape') { hide(); } });
    document.addEventListener('click', function (e) { if (!box.contains(e.target) && e.target !== input) { hide(); } });
  }

  // Sobre Nosotros: reemplaza imágenes y ajusta si existe la página
  function initAboutPageTweaks() {
    var isAbout = document.body.classList.contains('page-template-sobre-nosotros-php') || document.body.classList.contains('page-sobre-nosotros') || document.querySelector('main.about-page');
    if (!isAbout) return;
    try {
      var urls = [
        'https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0481-scaled.jpg',
        'https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0611-scaled.jpg',
        'https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0326-scaled.jpg',
        'https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0457-scaled.jpg'
      ];
      var items = document.querySelectorAll('.about-page .gallery-item');
      items.forEach(function (item) {
        var idx = parseInt(item.getAttribute('data-index') || '-1', 10);
        if (idx >= 0 && urls[idx]) {
          var img = item.querySelector('img');
          if (img) { img.src = urls[idx]; }
        }
      });
    } catch (e) { }
  }

  // Make blog cards clickable (entire card navigates to article)
  function initBlogCards() {
    try {
      var cards = document.querySelectorAll('#blog-mm .blog-card');
      if (!cards.length) return;
      cards.forEach(function (card) {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function (ev) {
          if (ev.target && ev.target.closest('a')) return;
          var a = card.querySelector('h3 a') || card.querySelector('.blog-img a');
          if (a && a.href) { window.location.href = a.href; }
        });
      });
    } catch (e) { }
  }

  function headerMenuToggle() {
    var header = document.querySelector('.site-header');
    var btn = document.querySelector('.nav-toggle-modern') || document.querySelector('.nav-toggle');
    var menu = document.getElementById('site-menu');
    if (!header || !btn || !menu) return;
    btn.addEventListener('click', function () {
      var open = header.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      setTimeout(setHeaderOffset, 100);
    });
    menu.addEventListener('click', function (e) {
      if (e.target.tagName === 'A' && header.classList.contains('open')) {
        header.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        setTimeout(setHeaderOffset, 100);
      }
    });
  }

  // Enhanced Smooth Scroll by Sections - Excluir slider inicial
  function initSectionScroll() {
    if (!document.querySelector('#site-main')) return;

    // Excluir el slider inicial
    var sections = document.querySelectorAll('#site-main > section:not(#custom-slider):not(.custom-slider)');
    if (!sections.length) return;

    // Agregar clases para animación (solo secciones después del slider)
    sections.forEach(function (section, idx) {
      section.classList.add('scroll-section');
      section.setAttribute('data-section-index', idx);
    });

    // Intersection Observer mejorado
    var observerOptions = {
      threshold: [0, 0.2, 0.4, 0.6, 0.8],
      rootMargin: '0px 0px -10% 0px'
    };

    var sectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        var section = entry.target;

        if (entry.isIntersecting && entry.intersectionRatio > 0.25) {
          section.classList.add('is-visible');
        }
      });
    }, observerOptions);

    sections.forEach(function (section) {
      sectionObserver.observe(section);
    });

    // Trigger inicial después de cargar
    setTimeout(function () {
      var firstSection = sections[0];
      if (firstSection && isInViewport(firstSection)) {
        firstSection.classList.add('is-visible');
      }
    }, 200);
  }

  function isInViewport(element) {
    var rect = element.getBoundingClientRect();
    return (
      rect.top < window.innerHeight * 0.75 &&
      rect.bottom > window.innerHeight * 0.25
    );
  }

  // Gallery Lightbox - PROFESSIONAL VERSION with Scroll Close
  function initGalleryLightbox() {
    var lightbox = document.getElementById('gallery-lightbox');
    if (!lightbox) return;
    // Mover al body si está dentro de un contenedor con transform (para que position:fixed sea relativo al viewport)
    try {
      if (lightbox.parentElement && lightbox.parentElement !== document.body) {
        document.body.appendChild(lightbox);
      }
    } catch (err) {
      console.warn('No se pudo reubicar el lightbox en body:', err);
    }

    var closeBtn = lightbox.querySelector('.lightbox-close');
    var gallerySwiper = null;
    var thumbsSwiper = null;
    var currentIndex = 0;
    var scrollTimeout = null;
    var scrollAttempts = 0;
    var closedByScroll = false;
    var savedScrollY = 0;

    // Add counter element if not exists
    var counter = lightbox.querySelector('.lightbox-counter');
    if (!counter) {
      counter = document.createElement('div');
      counter.className = 'lightbox-counter';
      lightbox.appendChild(counter);
    }

    function updateCounter(current, total) {
      if (counter) counter.textContent = (current + 1) + ' / ' + total;
    }

    // Initialize Swiper instances with enhanced config
    function initSwipers(startIndex) {
      // Helper to actually create the swipers
      var create = function () {
        if (typeof Swiper === 'undefined') {
          console.warn('Swiper not loaded yet, retrying...');
          setTimeout(function () { initSwipers(startIndex); }, 200);
          return;
        }

        try {
          // Destruir instancias previas si existen
          if (thumbsSwiper) { thumbsSwiper.destroy(true, true); thumbsSwiper = null; }
          if (gallerySwiper) { gallerySwiper.destroy(true, true); gallerySwiper = null; }

          // Contar slides originales (sin duplicados de loop)
          var totalSlides = document.querySelectorAll('.gallery-swiper .swiper-slide').length;

          // Initialize thumbnails first
          thumbsSwiper = new Swiper('.gallery-thumbs', {
            spaceBetween: 12,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
            slideToClickedSlide: true,
            centeredSlides: false,
            observer: true,
            observeParents: true
          });

          // Initialize main gallery with thumbs
          gallerySwiper = new Swiper('.gallery-swiper', {
            spaceBetween: 0,
            initialSlide: startIndex || 0,
            navigation: {
              nextEl: '.gallery-arrow-next',
              prevEl: '.gallery-arrow-prev',
            },
            thumbs: { swiper: thumbsSwiper },
            keyboard: { enabled: true, onlyInViewport: false },
            // Eliminamos efecto fade y lazy para evitar pantallas negras
            speed: 400,
            loop: true,
            loopedSlides: totalSlides,
            preloadImages: true,
            lazy: false,
            observer: true,
            observeParents: true,
            on: {
              slideChange: function () {
                // Usar realIndex para ignorar duplicados del loop
                currentIndex = this.realIndex;
                updateCounter(currentIndex, totalSlides);
              },
              init: function () {
                currentIndex = this.realIndex;
                updateCounter(currentIndex, totalSlides);
                // Asegurar carga de imágenes
                ensureImagesLoaded(this.el);
              }
            }
          });

          // Garantizar carga de todas las imágenes (fallback)
          function ensureImagesLoaded(root) {
            var imgs = root.querySelectorAll('img');
            imgs.forEach(function (img) {
              if (!img.complete || img.naturalWidth === 0) {
                var src = img.getAttribute('src');
                if (src) {
                  // Forzar recarga
                  img.setAttribute('src', src + (src.indexOf('?') === -1 ? '?v=' + Date.now() : '&_=' + Date.now()));
                }
              }
            });
          }
        } catch (err) {
          console.error('Error initializing Gallery Swiper:', err);
        }
      };

      create();
    }

    // Open lightbox
    function openLightbox(index) {
      currentIndex = index;
      scrollAttempts = 0;
      lightbox.classList.add('active');
      // Freeze background scroll reliably
      savedScrollY = window.scrollY || window.pageYOffset || 0;
      document.body.style.position = 'fixed';
      document.body.style.top = (-savedScrollY) + 'px';
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
      document.body.style.overflow = 'hidden';
      document.documentElement.style.overflow = 'hidden'; // lock html too
      lightbox.setAttribute('aria-hidden', 'false');

      // Initialize swipers
      initSwipers(index);

      // Add wheel event listener
      setTimeout(function () {
        lightbox.addEventListener('wheel', handleScrollClose, { passive: false });
      }, 300);
    }

    // Close lightbox
    function closeLightbox() {
      lightbox.classList.remove('active');
      // Restore background scroll position exactly
      // Temporarily disable smooth scroll to avoid visible animation
      var prevScrollBehavior = document.documentElement.style.scrollBehavior;
      document.documentElement.style.scrollBehavior = 'auto';
      document.body.style.overflow = '';
      document.documentElement.style.overflow = '';
      document.body.style.position = '';
      document.body.style.top = '';
      document.body.style.left = '';
      document.body.style.right = '';
      document.body.style.width = '';
      lightbox.setAttribute('aria-hidden', 'true');

      // Remove wheel listener
      lightbox.removeEventListener('wheel', handleScrollClose);

      // Destroy swipers to free memory and clear content immediately
      if (thumbsSwiper) { thumbsSwiper.destroy(true, true); thumbsSwiper = null; }
      if (gallerySwiper) { gallerySwiper.destroy(true, true); gallerySwiper = null; }

      // Restore scroll after releasing fixed body
      if (typeof savedScrollY === 'number' && savedScrollY >= 0) {
        // Restore immediately without smooth animation
        window.scrollTo(0, savedScrollY);
      }
      // Restore previous scroll-behavior on next tick
      setTimeout(function () { document.documentElement.style.scrollBehavior = prevScrollBehavior || ''; }, 0);
      closedByScroll = false; // reset flag
    }

    // Handle scroll to close
    function handleScrollClose(e) {
      if (e.deltaY > 50) {
        scrollAttempts++;
        if (scrollTimeout) clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function () { scrollAttempts = 0; }, 1000);
        if (scrollAttempts >= 2) {
          e.preventDefault();
          closedByScroll = true;
          closeLightbox();
          scrollAttempts = 0;
        }
      } else {
        scrollAttempts = 0;
      }
    }

    // Bind events to gallery items
    function bindGalleryEvents() {
      var items = document.querySelectorAll('.gallery-item');
      items.forEach(function (item) {
        item.style.cursor = 'pointer';
        // Remove old listeners to avoid duplicates
        item.removeEventListener('click', item._galleryClickHandler);

        item._galleryClickHandler = function (e) {
          if (item.tagName === 'A' && item.getAttribute('href') === '#') {
            e.preventDefault();
          }
          e.stopPropagation(); // Stop bubbling
          var index = parseInt(item.getAttribute('data-index') || '0');
          openLightbox(index);
        };

        item.addEventListener('click', item._galleryClickHandler);
      });
    }

    // Initial bind
    bindGalleryEvents();
    // Re-bind on AJAX complete or other dynamic updates
    document.addEventListener('DOMContentLoaded', bindGalleryEvents);
    document.addEventListener('ajaxComplete', bindGalleryEvents);

    // Close button
    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
        closeLightbox();
        return false;
      });
    }

    // Close on background click
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target.classList.contains('lightbox-content')) {
        e.preventDefault();
        closeLightbox();
        return false;
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && lightbox.classList.contains('active')) {
        closeLightbox();
      }
    });

    // Prevent image drag
    var lightboxImages = lightbox.querySelectorAll('img');
    lightboxImages.forEach(function (img) {
      img.addEventListener('dragstart', function (e) { e.preventDefault(); });
    });
  }

  window.addEventListener('load', initHeroSlider);
  document.addEventListener('DOMContentLoaded', initVehiculos);
  document.addEventListener('DOMContentLoaded', initVehiculosLoadMore);
  // DISABLED: Scroll animations that hide content until scroll intersection
  // document.addEventListener('DOMContentLoaded', revealOnView);
  document.addEventListener('DOMContentLoaded', headerScroll);
  document.addEventListener('DOMContentLoaded', headerMenuToggle);
  document.addEventListener('DOMContentLoaded', setHeaderOffset);
  document.addEventListener('DOMContentLoaded', initBlogInfinite);
  document.addEventListener('DOMContentLoaded', initBlogSearch);
  document.addEventListener('DOMContentLoaded', initAboutPageTweaks);
  document.addEventListener('DOMContentLoaded', initBlogCards);
  document.addEventListener('DOMContentLoaded', initGalleryLightbox);
  // DISABLED: Scroll section animations for better mobile usability
  // document.addEventListener('DOMContentLoaded', initSectionScroll);
  window.addEventListener('resize', setHeaderOffset);
})();

