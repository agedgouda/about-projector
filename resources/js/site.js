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
