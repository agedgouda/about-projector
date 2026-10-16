// This is all you.

// Load styles through JS so the Vite dev server always serves the current CSS (its direct <link> copy goes stale).
import '../css/site.css';

// Hero carousel: fades between [data-slide] elements inside each [data-carousel].
document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const slides = [...carousel.querySelectorAll('[data-slide]')];
    const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
    if (slides.length < 2) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const seconds = parseInt(carousel.dataset.autoplay, 10) || 0;
    let current = 0;
    let timer = null;

    const show = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.toggleAttribute('inert', !active);
            if (active) slide.removeAttribute('aria-hidden');
            else slide.setAttribute('aria-hidden', 'true');
        });

        dots.forEach((dot, i) => {
            const active = i === current;
            dot.classList.toggle('w-8', active);
            dot.classList.toggle('bg-projector-primary-600', active);
            dot.classList.toggle('w-2.5', !active);
            dot.classList.toggle('bg-zinc-400', !active);
            dot.classList.toggle('hover:bg-zinc-500', !active);
            if (active) dot.setAttribute('aria-current', 'true');
            else dot.removeAttribute('aria-current');
        });
    };

    const stop = () => {
        clearInterval(timer);
        timer = null;
    };

    const start = () => {
        if (!seconds || reducedMotion) return;
        stop();
        timer = setInterval(() => show(current + 1), seconds * 1000);
    };

    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
        show(current - 1);
        start();
    });
    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
        show(current + 1);
        start();
    });
    dots.forEach((dot, i) => dot.addEventListener('click', () => {
        show(i);
        start();
    }));

    // Pause while the visitor is hovering or using the controls.
    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', start);

    start();
});

// Scroll cue: a down-arrow that stays until the visitor reaches the bottom of the page.
document.querySelectorAll('[data-scroll-cue]').forEach((cue) => {
    const header = document.getElementById('site-header');
    // The video page wraps its sections in one [data-videos] element, so step into it.
    const sections = [...document.querySelectorAll('main > :not([data-videos]), main > [data-videos] > section')];

    const update = () => {
        const visible = window.scrollY + window.innerHeight < document.documentElement.scrollHeight - 24;
        cue.classList.toggle('opacity-0', !visible);
        cue.classList.toggle('pointer-events-none', !visible);
        cue.tabIndex = visible ? 0 : -1;
        cue.toggleAttribute('aria-hidden', !visible);
    };

    // Jump to the next section starting below the sticky header, or a screenful if none is left.
    cue.addEventListener('click', () => {
        const below = (header?.offsetHeight ?? 0) + 2;
        const next = sections.find((section) => section.getBoundingClientRect().top > below);
        if (next) next.scrollIntoView({ behavior: 'smooth' });
        else window.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
    });
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
});

// Video page: clicking a smaller video swaps it with the main one and starts it playing.
document.querySelectorAll('[data-videos]').forEach((root) => {
    const main = root.querySelector('[data-video-main]');

    const stop = (item) => {
        const player = item.querySelector('[data-video-player]');
        player.replaceChildren();
        player.hidden = true;
        item.querySelector('[data-video-play]').hidden = false;
    };

    const play = (item) => {
        const { embed, file } = item.dataset;
        if (!embed && !file) return;
        const player = item.querySelector('[data-video-player]');
        let el;
        if (embed) {
            const src = new URL(embed, window.location.href);
            src.searchParams.set('autoplay', '1');
            el = document.createElement('iframe');
            el.src = src.toString();
            el.allow = 'autoplay; fullscreen; picture-in-picture';
            el.allowFullscreen = true;
        } else {
            el = document.createElement('video');
            el.src = file;
            if (item.dataset.poster) el.poster = item.dataset.poster;
            el.controls = true;
            el.autoplay = true;
            el.playsInline = true;
        }
        // object-cover fills the 16:9 spot when a recording is slightly off (e.g. 1916×1054) instead of showing black bars.
        el.className = 'h-full w-full object-cover';
        player.replaceChildren(el);
        player.hidden = false;
        item.querySelector('[data-video-play]').hidden = true;
    };

    const setMain = (item, isMain) => {
        item.querySelector('[data-video-stage]').hidden = !isMain;
        item.querySelector('[data-video-thumb]').hidden = isMain;
        if (!isMain) stop(item);
    };

    root.addEventListener('click', (event) => {
        if (event.target.closest('[data-video-play]')) {
            play(event.target.closest('[data-video]'));
            return;
        }
        const thumb = event.target.closest('[data-video-thumb]');
        if (!thumb) return;

        // The current main video takes the clicked one's spot in the grid.
        const item = thumb.closest('[data-video]');
        const current = main.querySelector('[data-video]');
        item.replaceWith(current);
        main.append(item);
        setMain(current, false);
        setMain(item, true);
        main.scrollIntoView({ behavior: 'smooth', block: 'start' });
        play(item);
        item.querySelector('[data-video-play]').focus({ preventScroll: true });
    });
});
