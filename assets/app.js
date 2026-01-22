import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import 'bootstrap-icons/font/bootstrap-icons.css';
import "core-js/stable";
import "regenerator-runtime/runtime";

document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        console.log("Elements to animate:", elements);

        if (!elements.length) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            elements.forEach(el => el.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        elements.forEach(el => observer.observe(el));
    }, 100);
});

// =========================
// LIGHTBOX AVEC NAVIGATION
// =========================
const lightboxTriggers = document.querySelectorAll('.lightbox-trigger');

if (lightboxTriggers.length) {

    let currentIndex = 0;
    const images = Array.from(lightboxTriggers).map(img => img.src);

    // Création overlay
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';

    overlay.innerHTML = `
        <div class="lightbox-content">
            <button class="lightbox-close">&times;</button>
            <button class="lightbox-prev">&#10094;</button>
            <img src="" alt="">
            <button class="lightbox-next">&#10095;</button>
        </div>
    `;

    document.body.appendChild(overlay);

    const overlayImg = overlay.querySelector('img');
    const btnClose = overlay.querySelector('.lightbox-close');
    const btnPrev = overlay.querySelector('.lightbox-prev');
    const btnNext = overlay.querySelector('.lightbox-next');

    const openLightbox = index => {
        currentIndex = index;
        overlayImg.src = images[currentIndex];
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeLightbox = () => {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    const showPrev = () => {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        overlayImg.src = images[currentIndex];
    };

    const showNext = () => {
        currentIndex = (currentIndex + 1) % images.length;
        overlayImg.src = images[currentIndex];
    };

    // Events images
    lightboxTriggers.forEach((img, index) => {
        img.addEventListener('click', () => openLightbox(index));
    });

    // Events boutons
    btnClose.addEventListener('click', closeLightbox);
    btnPrev.addEventListener('click', showPrev);
    btnNext.addEventListener('click', showNext);

    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeLightbox();
    });

    // Clavier
    document.addEventListener('keydown', e => {
        if (!overlay.classList.contains('active')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });
}






