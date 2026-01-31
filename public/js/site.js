// Import Lenis from CDN in the template

document.addEventListener('DOMContentLoaded', () => {
    console.log('InnoLearn Hybrid Parallax (Mouse + Scroll)');

    if (window.Lenis) {
        const lenis = new window.Lenis({
            duration: 2.0,
            direction: 'vertical',
            smooth: true,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    }

    // STATE
    let mouseX = 0;
    let mouseY = 0;
    let targetMouseX = 0;
    let targetMouseY = 0;
    let scrollY = 0;

    const layers = {
        l1: document.querySelector('.float-1'), // Book
        l2: document.querySelector('.float-2'), // Brain
        // Extra decoration layers if they exist
        l3: document.querySelector('.layer-3'),
    };

    // Smooth easing factor (lower = smoother, higher = more responsive)
    const easingFactor = 0.1;

    // INPUT TRACKING
    window.addEventListener('mousemove', (e) => {
        // Normalized -0.5 to 0.5
        targetMouseX = (e.clientX - window.innerWidth / 2) / window.innerWidth;
        targetMouseY = (e.clientY - window.innerHeight / 2) / window.innerHeight;
    });

    window.addEventListener('scroll', () => {
        scrollY = window.scrollY;
    });

    // RENDER LOOP
    function updateParallax() {
        requestAnimationFrame(updateParallax);

        // Smooth easing for mouse movement (lerp - linear interpolation)
        mouseX += (targetMouseX - mouseX) * easingFactor;
        mouseY += (targetMouseY - mouseY) * easingFactor;

        // BOOK (Layer 1) - Enhanced with smoother movement and 3D transforms
        if (layers.l1) {
            // Mouse: X/Y Translation with smooth easing
            const mX = mouseX * -60;
            const mY = mouseY * -60;

            // Scroll: Y Translation (Parallax) + Enhanced 3D Rotation
            const sY = scrollY * 0.18;
            const sRotY = scrollY * 0.08 + (mouseX * 15); // Mouse affects rotation
            const sRotZ = scrollY * 0.03 + (mouseY * 10);
            const sRotX = Math.sin(scrollY * 0.01) * 5 + (mouseY * 8);

            // Combine with perspective
            layers.l1.style.transform =
                `translate3d(${mX}px, ${mY + sY}px, 0) ` +
                `rotateX(${sRotX}deg) rotateY(${sRotY}deg) rotateZ(${sRotZ}deg) ` +
                `perspective(1000px)`;
        }

        // BRAIN (Layer 2) - Enhanced with dynamic movement
        if (layers.l2) {
            // Mouse: Faster/Closer with more dramatic effect
            const mX = mouseX * -85;
            const mY = mouseY * -85;

            // Scroll: Moves UP against scroll + Complex Rotation
            const sY = -(scrollY * 0.08);
            const sRotY = -(scrollY * 0.08) + (mouseX * -20); // Mouse affects rotation
            const sRotX = Math.cos(scrollY * 0.01) * 8 + (mouseY * -12);
            const scale = 1 + (Math.sin(scrollY * 0.005) * 0.05) + (Math.abs(mouseX) * 0.03);

            layers.l2.style.transform =
                `translate3d(${mX}px, ${mY + sY}px, 0) ` +
                `rotateX(${sRotX}deg) rotateY(${sRotY}deg) ` +
                `scale(${scale}) ` +
                `perspective(1000px)`;
        }

        // Satellites (Layer 3)
        if (layers.l3) {
            const mX = mouseX * -30;
            const mY = mouseY * -30;
            const sY = scrollY * 0.12;
            const sRotZ = scrollY * 0.02 + (mouseX * 5);
            layers.l3.style.transform = 
                `translate3d(${mX}px, ${mY + sY}px, 0) ` +
                `rotateZ(${sRotZ}deg)`;
        }
    }

    // Start Loop
    updateParallax();
});
