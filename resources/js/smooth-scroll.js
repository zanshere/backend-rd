import Lenis from 'lenis';
import { motion } from 'framer-motion';

// Initialize Lenis for smooth scrolling
document.addEventListener('DOMContentLoaded', function() {
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        smoothTouch: false,
        touchMultiplier: 2,
    });

    // Sync Lenis with Framer Motion
    lenis.on('scroll', ({ scroll, limit, velocity, direction, progress }) => {
        // You can use these values with Framer Motion if needed
        // For example, you can trigger animations based on scroll position
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);
});

// Optional: Framer Motion animations
export const pageVariants = {
    initial: { opacity: 0, y: 20 },
    in: { opacity: 1, y: 0 },
    out: { opacity: 0, y: -20 }
};

export const pageTransition = {
    type: "tween",
    ease: "anticipate",
    duration: 0.5
};
