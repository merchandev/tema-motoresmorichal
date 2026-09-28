/* Front-page interactions for Toyota Monagas theme */
(function () {
  var SLIDE_DURATION = 13170; // milisegundos

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
    var pauseButton = slider.querySelector('.cs-play-toggle');
    var motionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
    var pausedByUser = !!(motionQuery && motionQuery.matches);
    var rafId = 0;
    var lastTs = 0;
    var progressMs = 0;
    var activeIndex = 0;
    var activationToken = 0;

    function isPaused() {
      return pausedByHover || pausedByUser;
    }

    function updatePauseButton() {
      if (!pauseButton) return;
      pauseButton.setAttribute('aria-pressed', pausedByUser ? 'true' : 'false');
      pauseButton.setAttribute('aria-label', pausedByUser ? 'Reanudar slider' : 'Pausar slider');
      pauseButton.textContent = pausedByUser ? 'Reanudar' : 'Pausar';
    }

    function syncActiveVideo() {
      if (!swiperInstance || !swiperInstance.slides) return;
      var activeSlide = swiperInstance.slides[swiperInstance.activeIndex];
      var activeVideo = activeSlide ? activeSlide.querySelector('video') : null;
      if (!activeVideo) return;
      if (isPaused()) {
        try { activeVideo.pause(); } catch (e) { }
      } else {
        try {
          var playPromise = activeVideo.play();
          if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function () { });
          }
        } catch (e) { }
      }
    }

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
        setWidth(span, i <= idx ? '100%' : '0%', true);
      });
      if (progressSpans[idx]) {
        progressSpans[idx].classList.add('active');
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
      if (!isPaused()) {
        progressMs += (now - lastTs);
      }
      lastTs = now;

      if (isWorkingVideo && activeVideo.readyState >= 2) {
        progressMs = activeVideo.currentTime * 1000;
        
        if (isPaused() && !activeVideo.paused) {
          try { activeVideo.pause(); } catch(e){}
        } else if (!isPaused() && activeVideo.paused && activeVideo.currentTime < activeVideo.duration) {
          try { activeVideo.play(); } catch(e){}
        }
      }

      var pct = Math.min(100, (progressMs / currentDuration) * 100);

      if (progressMs >= currentDuration) {
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
        video.removeAttribute('loop');

        if (!isPaused()) {
          try { video.currentTime = 0; } catch (e) {}
          var playPromise;
          try {
            playPromise = video.play();
          } catch (e) {}
          if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function(){});
          }
        }

        resolve();
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
              if (!isPaused()) swiper.slideNext();
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

    if (pauseButton) {
      pauseButton.addEventListener('click', function () {
        pausedByUser = !pausedByUser;
        lastTs = 0;
        updatePauseButton();
        syncActiveVideo();
      });
    }

    if (motionQuery) {
      var onMotionChange = function (event) {
        pausedByUser = !!event.matches;
        lastTs = 0;
        updatePauseButton();
        syncActiveVideo();
      };
      if (typeof motionQuery.addEventListener === 'function') {
        motionQuery.addEventListener('change', onMotionChange);
      } else if (typeof motionQuery.addListener === 'function') {
        motionQuery.addListener(onMotionChange);
      }
    }

    updatePauseButton();

    slider.addEventListener('mouseenter', function () {
      pausedByHover = true;
      syncActiveVideo();
    });

    slider.addEventListener('mouseleave', function () {
      pausedByHover = false;
      lastTs = 0;
      syncActiveVideo();
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
    if (!wrapper) return;

    var swiper = null;
    var currentItemCount = 0;

    function createVehSwiper() {
      // Avoid duplicate init
      var container = document.querySelector('#vehiculos .toyota-slider');
      if (!container) return null;
      if (container.swiper) return container.swiper;

      if (typeof Swiper === 'undefined') return null;

      try {
        var inst = new Swiper('#vehiculos .toyota-slider', {
          spaceBetween: 20,
          watchOverflow: true,
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
      currentItemCount = count;
      var slidesPerView = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
      var show = count > slidesPerView;
      var canSwipe = count > 1;
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
        // Keep touch/drag enabled for two or three cards on narrow screens,
        // even when desktop arrows are intentionally hidden.
        swiper.allowSlideNext = canSwipe;
        swiper.allowSlidePrev = canSwipe;
      }
    }

    window.addEventListener('resize', function () {
      updateArrowState(currentItemCount);
    });

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
        emptySlide.className = 'swiper-slide empty-message-slide';
        emptySlide.innerHTML =
          '<div class="toyota-empty-card">' +
          '<div class="tec-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="32" height="32" focusable="false"><path d="M3 6h11v9H3zM14 9h4l3 3v3h-7zM7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></div>' +
          '<h3>Pronto en Stock</h3>' +
          '<p>Estamos renovando el inventario de esta categor&iacute;a.</p>' +
          '<a href="#whatsapp-mm" class="toyota-btn">Consultar llegada</a>' +
          '</div>';

        var emptyLink = emptySlide.querySelector('.toyota-btn');
        var config = window.toyota_front_ajax || {};
        if (emptyLink && typeof config.empty_inventory_url === 'string' && /^https:\/\/wa\.me\//i.test(config.empty_inventory_url)) {
          emptyLink.href = config.empty_inventory_url;
          emptyLink.target = '_blank';
          emptyLink.rel = 'noopener noreferrer';
        }

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
        btn.setAttribute('tabindex', '-1');
      });
      tab.classList.add('is-active');
      tab.setAttribute('aria-selected', 'true');
      tab.setAttribute('tabindex', '0');

      var panel = document.querySelector('#vehiculos .toyota-slider');
      if (panel && tab.id) panel.setAttribute('aria-labelledby', tab.id);
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

    if (tablist) {
      tablist.addEventListener('keydown', function (event) {
        var current = Array.prototype.indexOf.call(tabs, document.activeElement);
        if (current < 0) return;

        var next = current;
        if (event.key === 'ArrowRight') next = (current + 1) % tabs.length;
        else if (event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
        else if (event.key === 'Home') next = 0;
        else if (event.key === 'End') next = tabs.length - 1;
        else return;

        event.preventDefault();
        tabs[next].focus();
        tabs[next].click();
      });
    }

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

  // Load more on vehicle inventory pages (AJAX with link fallback)
  function initVehiculosLoadMore() {
    var buttons = document.querySelectorAll('.veh-loadmore[data-action]');
    if (!buttons.length) return;

    function updateFallbackHref(btn, nextPage) {
      var href = btn.getAttribute('href') || '';
      if (!href) return;
      if (/\/page\/\d+\/?(?:\?|$)/.test(href)) {
        href = href.replace(/\/page\/\d+\/?(?=\?|$)/, '/page/' + nextPage + '/');
      } else if (/([?&])paged=\d+/.test(href)) {
        href = href.replace(/([?&])paged=\d+/, '$1paged=' + nextPage);
      }
      btn.setAttribute('href', href);
    }

    Array.prototype.forEach.call(buttons, function (btn) {
      btn.addEventListener('click', function (event) {
        if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

        var page = parseInt(btn.getAttribute('data-page') || '2', 10);
        var max = parseInt(btn.getAttribute('data-max') || '1', 10);
        if (page > max) {
          event.preventDefault();
          btn.style.display = 'none';
          return;
        }
        if (btn.getAttribute('aria-disabled') === 'true') {
          event.preventDefault();
          return;
        }

        var action = btn.getAttribute('data-action') || '';
        if (action !== 'toyota_load_more_vehiculos' && action !== 'toyota_load_more_usados') return;

        var catalog = btn.closest ? btn.closest('.veh-catalogo') : null;
        var targetSelector = btn.getAttribute('data-target') || '.veh-grid';
        var grid = (catalog && catalog.querySelector(targetSelector)) || document.querySelector(targetSelector);
        var ajaxConfig = window.toyota_front_ajax || {};
        var ajax = ajaxConfig.ajax_url || btn.getAttribute('data-ajax-url') || '';
        var nonce = ajaxConfig.nonce || btn.getAttribute('data-nonce') || '';
        if (!grid || !ajax || !window.fetch) return;

        var status = catalog && catalog.querySelector('.veh-load-status');
        if (!status && catalog) {
          status = document.createElement('p');
          status.className = 'veh-load-status sr-only';
          status.setAttribute('role', 'status');
          status.setAttribute('aria-live', 'polite');
          catalog.appendChild(status);
        }

        event.preventDefault();
        var oldText = btn.textContent;
        btn.setAttribute('aria-disabled', 'true');
        btn.classList.add('is-loading');
        btn.textContent = 'Cargando…';
        grid.setAttribute('aria-busy', 'true');

        var form = new FormData();
        form.append('action', action);
        form.append('page', page);
        form.append('cat', btn.getAttribute('data-cat') || 'all');
        form.append('nonce', nonce);

        var shouldUseFallback = false;
        fetch(ajax, { method: 'POST', body: form, credentials: 'same-origin' })
          .then(function (response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
          })
          .then(function (data) {
            if (!data || !data.success || !data.data || !data.data.html) {
              throw new Error('Invalid inventory response');
            }

            var holder = document.createElement('div');
            holder.innerHTML = data.data.html;
            var nodes = Array.prototype.slice.call(holder.children);
            var buttonHadFocus = document.activeElement === btn;
            nodes.forEach(function (node, index) {
              node.classList.add('is-new');
              grid.appendChild(node);
              setTimeout(function () {
                requestAnimationFrame(function () { node.classList.add('in'); });
              }, index * 80);
            });

            var nextPage = parseInt(data.data.next_page || (page + 1), 10);
            max = parseInt(data.data.max_pages || data.data.max || max, 10);
            btn.setAttribute('data-page', nextPage);
            btn.setAttribute('data-max', max);
            updateFallbackHref(btn, nextPage);
            var isFinished = data.data.has_more === false || nextPage > max;
            if (status) {
              status.textContent = nodes.length + (nodes.length === 1
                ? ' vehículo añadido.'
                : ' vehículos añadidos.') + (isFinished ? ' No hay más resultados.' : '');
            }
            if (isFinished) {
              btn.style.display = 'none';
              if (buttonHadFocus && nodes[0]) {
                var focusTarget = nodes[0].querySelector('a[href]') || nodes[0];
                if (!focusTarget.hasAttribute('tabindex') && focusTarget === nodes[0]) focusTarget.setAttribute('tabindex', '-1');
                focusTarget.focus();
              }
            }
          })
          .catch(function (error) {
            shouldUseFallback = true;
            if (window.console && console.warn) console.warn('Inventory load failed; using page link.', error);
          })
          .then(function () {
            btn.removeAttribute('aria-disabled');
            btn.classList.remove('is-loading');
            btn.textContent = oldText;
            grid.removeAttribute('aria-busy');
            if (shouldUseFallback && btn.href) window.location.assign(btn.href);
          });
      });
    });
  }

  // Infinite scroll for Blog page
  function initBlogInfinite() {
    var list = document.getElementById('blog-list');
    var sentinel = document.getElementById('blog-sentinel');
    if (!list || !sentinel) return;
    var page = parseInt(list.getAttribute('data-page') || '2', 10);
    var max = parseInt(list.getAttribute('data-max') || '1', 10);
    var loading = false;
    var ajaxConfig = window.toyota_front_ajax || {};
    var ajax = ajaxConfig.ajax_url || list.getAttribute('data-ajax-url') || '';
    var nonce = ajaxConfig.nonce || list.getAttribute('data-nonce') || '';
    if (!ajax || !window.fetch || !window.IntersectionObserver) return;
    var pagination = document.querySelector('.blog-pagination');
    var paginationNext = pagination && pagination.querySelector('a[rel="next"]');
    var paginationStatus = pagination && pagination.querySelector('span');
    if (paginationNext) paginationNext.hidden = true;

    function updatePaginationFallback(nextPage, showNext) {
      if (!pagination) return;
      if (paginationStatus) {
        paginationStatus.textContent = 'Contenido cargado hasta la página ' + Math.min(Math.max(1, nextPage - 1), max) + ' de ' + max;
      }
      if (!paginationNext) return;
      if (nextPage > max) {
        paginationNext.hidden = true;
        return;
      }
      try {
        var nextUrl = new URL(paginationNext.href, window.location.href);
        nextUrl.searchParams.set('blog_page', String(nextPage));
        paginationNext.href = nextUrl.toString();
        paginationNext.hidden = !showNext;
      } catch (urlError) {
        paginationNext.hidden = true;
      }
    }

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
          .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
          })
          .then(function (data) {
            if (data && data.success && data.data && data.data.html) {
              var wrap = document.createElement('div');
              wrap.innerHTML = data.data.html;
              Array.from(wrap.children).forEach(function (node, idx) {
                node.classList.add('is-new');
                list.appendChild(node);
                setTimeout(function () { node.classList.add('in'); }, idx * 60);
              });
              page = parseInt(data.data.next_page || (page + 1), 10);
              list.setAttribute('data-page', page);
              if (data.data.max) { max = parseInt(data.data.max, 10); }
              updatePaginationFallback(page, false);
              if (data.data.has_more === false || page > max) { io.disconnect(); }
            } else {
              updatePaginationFallback(page, true);
              io.disconnect();
            }
            loading = false;
          })
          .catch(function (err) {
            loading = false;
            updatePaginationFallback(page, true);
            if (window.console && console.warn) console.warn('Blog infinite failed', err);
            io.disconnect();
          });
      });
    }, { rootMargin: '360px 0px' });
    io.observe(sentinel);
  }

  // Blog search with autocomplete (AJAX)
  function initBlogSearch() {
    var input = document.getElementById('blog-search-input');
    var box = document.getElementById('blog-search-suggest');
    var searchForm = document.getElementById('blog-search');
    if (!input || !box) return;
    var ajaxConfig = window.toyota_front_ajax || {};
    var ajax = ajaxConfig.ajax_url || (searchForm && searchForm.getAttribute('data-ajax-url')) || '';
    var nonce = ajaxConfig.nonce || (searchForm && searchForm.getAttribute('data-nonce')) || '';
    var tmr = 0;
    var requestNumber = 0;
    var activeController = null;

    function clearBox() {
      while (box.firstChild) box.removeChild(box.firstChild);
    }

    function hide() {
      box.hidden = true;
      input.removeAttribute('aria-busy');
      clearBox();
    }

    function cancelAndHide() {
      if (tmr) clearTimeout(tmr);
      tmr = 0;
      requestNumber++;
      if (activeController) activeController.abort();
      activeController = null;
      hide();
    }

    function safeHttpUrl(value) {
      if (!value) return '';
      var parser = document.createElement('a');
      parser.href = String(value);
      return (parser.protocol === 'http:' || parser.protocol === 'https:') ? parser.href : '';
    }

    function render(items) {
      if (!items || !items.length) { hide(); return; }
      clearBox();
      var fragment = document.createDocumentFragment();
      items.forEach(function (item, index) {
        var itemUrl = safeHttpUrl(item.url);
        if (!itemUrl) return;

        var link = document.createElement('a');
        link.className = 'sug-item';
        link.href = itemUrl;
        link.id = 'blog-search-option-' + index;

        var thumbUrl = safeHttpUrl(item.thumb);
        if (thumbUrl) {
          var image = document.createElement('img');
          image.src = thumbUrl;
          image.alt = '';
          image.loading = 'lazy';
          link.appendChild(image);
        }

        var title = document.createElement('span');
        title.textContent = item.title || '';
        link.appendChild(title);
        fragment.appendChild(link);
      });

      var more = document.createElement('a');
      var action = (searchForm && searchForm.getAttribute('action')) || window.location.pathname;
      more.className = 'sug-more';
      more.href = action + (action.indexOf('?') === -1 ? '?' : '&') + 's=' + encodeURIComponent(input.value.trim());
      more.textContent = 'Ver todos los resultados';
      fragment.appendChild(more);
      box.appendChild(fragment);
      box.hidden = false;
      input.removeAttribute('aria-busy');
    }

    input.addEventListener('input', function () {
      var q = input.value.trim();
      if (tmr) { clearTimeout(tmr); }
      requestNumber++;
      if (activeController) activeController.abort();
      if (q.length < 2 || !ajax || !window.fetch) { hide(); return; }
      tmr = setTimeout(function () {
        tmr = 0;
        var thisRequest = requestNumber;
        activeController = window.AbortController ? new window.AbortController() : null;
        var url = ajax + '?action=toyota_search_posts&q=' + encodeURIComponent(q) + '&nonce=' + encodeURIComponent(nonce);
        var options = { credentials: 'same-origin' };
        if (activeController) options.signal = activeController.signal;
        input.setAttribute('aria-busy', 'true');
        fetch(url, options)
          .then(function (response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
          })
          .then(function (data) {
            if (thisRequest !== requestNumber) return;
            if (data && data.success) render((data.data && data.data.items) || []);
            else hide();
          })
          .catch(function (error) {
            if (thisRequest !== requestNumber || (error && error.name === 'AbortError')) return;
            hide();
          });
      }, 200);
    });

    input.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        cancelAndHide();
      } else if (event.key === 'ArrowDown' && !box.hidden) {
        var first = box.querySelector('a');
        if (first) {
          event.preventDefault();
          first.focus();
        }
      }
    });

    box.addEventListener('keydown', function (event) {
      var links = box.querySelectorAll('a');
      var current = Array.prototype.indexOf.call(links, document.activeElement);
      if (event.key === 'Escape') {
        event.preventDefault();
        cancelAndHide();
        input.focus();
      } else if (event.key === 'ArrowDown' && current >= 0) {
        event.preventDefault();
        links[(current + 1) % links.length].focus();
      } else if (event.key === 'ArrowUp' && current >= 0) {
        event.preventDefault();
        if (current === 0) input.focus();
        else links[current - 1].focus();
      }
    });

    document.addEventListener('click', function (e) {
      if (!box.contains(e.target) && e.target !== input) cancelAndHide();
    });
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
    var lastFocused = null;
    var backgroundStates = [];
    var swiperRetries = 0;
    var focusTimer = null;
    var savedBodyStyle = null;
    var savedHtmlOverflow = '';

    // Add counter element if not exists
    var counter = lightbox.querySelector('.lightbox-counter');
    if (!counter) {
      counter = document.createElement('div');
      counter.className = 'lightbox-counter';
      lightbox.appendChild(counter);
    }
    counter.setAttribute('role', 'status');
    counter.setAttribute('aria-live', 'polite');

    function setBackgroundInert(makeInert) {
      if (makeInert) {
        backgroundStates = [];
        Array.prototype.forEach.call(document.body.children, function (element) {
          if (element === lightbox || /^(SCRIPT|STYLE|LINK)$/.test(element.tagName)) return;
          var supportsInert = 'inert' in element;
          backgroundStates.push({
            element: element,
            supportsInert: supportsInert,
            previousInert: supportsInert ? element.inert : false,
            previousAriaHidden: element.getAttribute('aria-hidden')
          });
          if (supportsInert) element.inert = true;
          else element.setAttribute('aria-hidden', 'true');
        });
        return;
      }

      backgroundStates.forEach(function (state) {
        if (state.supportsInert) state.element.inert = state.previousInert;
        if (state.previousAriaHidden === null) state.element.removeAttribute('aria-hidden');
        else state.element.setAttribute('aria-hidden', state.previousAriaHidden);
      });
      backgroundStates = [];
    }

    function getFocusableElements() {
      return Array.prototype.slice.call(lightbox.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
      )).filter(function (element) {
        return element.getAttribute('aria-hidden') !== 'true';
      });
    }

    function updateCounter(current, total) {
      if (counter) counter.textContent = (current + 1) + ' / ' + total;
    }

    // Initialize Swiper instances with enhanced config
    function initSwipers(startIndex) {
      // Helper to actually create the swipers
      var create = function () {
        if (typeof Swiper === 'undefined') {
          if (swiperRetries < 10) {
            swiperRetries++;
            setTimeout(function () { initSwipers(startIndex); }, 200);
          }
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
            speed: 400,
            loop: false,
            rewind: true,
            preloadImages: false,
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
              }
            }
          });

          Array.prototype.forEach.call(lightbox.querySelectorAll('.gallery-thumbs [data-gallery-index]'), function (thumbnail) {
            if (thumbnail.getAttribute('data-gallery-bound') === 'true') return;
            thumbnail.setAttribute('data-gallery-bound', 'true');
            thumbnail.addEventListener('click', function () {
              var index = parseInt(thumbnail.getAttribute('data-gallery-index') || '0', 10);
              if (gallerySwiper && Number.isFinite(index)) gallerySwiper.slideTo(index);
            });
          });
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
      swiperRetries = 0;
      lastFocused = document.activeElement;
      if (focusTimer) clearTimeout(focusTimer);
      lightbox.classList.add('active');
      // Freeze background scroll reliably
      savedScrollY = window.scrollY || window.pageYOffset || 0;
      savedBodyStyle = document.body.getAttribute('style');
      savedHtmlOverflow = document.documentElement.style.overflow;
      document.body.style.position = 'fixed';
      document.body.style.top = (-savedScrollY) + 'px';
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
      document.body.style.overflow = 'hidden';
      document.documentElement.style.overflow = 'hidden'; // lock html too
      lightbox.setAttribute('aria-hidden', 'false');
      setBackgroundInert(true);

      // Initialize swipers
      initSwipers(index);

      // Add wheel event listener
      focusTimer = setTimeout(function () {
        focusTimer = null;
        if (!lightbox.classList.contains('active')) return;
        lightbox.addEventListener('wheel', handleScrollClose, { passive: false });
        if (closeBtn) closeBtn.focus();
        else lightbox.focus();
      }, 300);
    }

    // Close lightbox
    function closeLightbox() {
      if (focusTimer) {
        clearTimeout(focusTimer);
        focusTimer = null;
      }
      lightbox.classList.remove('active');
      // Restore background scroll position exactly
      // Temporarily disable smooth scroll to avoid visible animation
      var prevScrollBehavior = document.documentElement.style.scrollBehavior;
      document.documentElement.style.scrollBehavior = 'auto';
      if (savedBodyStyle === null) document.body.removeAttribute('style');
      else document.body.setAttribute('style', savedBodyStyle);
      document.documentElement.style.overflow = savedHtmlOverflow;
      lightbox.setAttribute('aria-hidden', 'true');
      setBackgroundInert(false);

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
      if (lastFocused && typeof lastFocused.focus === 'function') {
        try { lastFocused.focus(); } catch (focusError) { }
      }
      lastFocused = null;
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
        if (!item.hasAttribute('role')) item.setAttribute('role', 'button');
        if (!item.hasAttribute('tabindex')) item.setAttribute('tabindex', '0');
        item.setAttribute('aria-haspopup', 'dialog');
        item.setAttribute('aria-controls', lightbox.id);
        // Remove old listeners to avoid duplicates
        item.removeEventListener('click', item._galleryClickHandler);
        item.removeEventListener('keydown', item._galleryKeyHandler);

        item._galleryClickHandler = function (e) {
          if (item.tagName === 'A' && item.getAttribute('href') === '#') {
            e.preventDefault();
          }
          e.stopPropagation(); // Stop bubbling
          var index = parseInt(item.getAttribute('data-index') || '0');
          openLightbox(index);
        };

        item.addEventListener('click', item._galleryClickHandler);
        item._galleryKeyHandler = function (event) {
          if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            item.click();
          }
        };
        item.addEventListener('keydown', item._galleryKeyHandler);
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

    // Close on Escape and keep keyboard focus inside the modal.
    document.addEventListener('keydown', function (e) {
      if (!lightbox.classList.contains('active')) return;
      if (e.key === 'Escape') {
        e.preventDefault();
        closeLightbox();
      } else if (e.key === 'Tab') {
        var focusable = getFocusableElements();
        if (!focusable.length) {
          e.preventDefault();
          lightbox.focus();
          return;
        }
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
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
  document.addEventListener('DOMContentLoaded', initBlogInfinite);
  document.addEventListener('DOMContentLoaded', initBlogSearch);
  document.addEventListener('DOMContentLoaded', initBlogCards);
  document.addEventListener('DOMContentLoaded', initGalleryLightbox);
  // DISABLED: Scroll section animations for better mobile usability
  // document.addEventListener('DOMContentLoaded', initSectionScroll);
})();
