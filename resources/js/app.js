import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('revealOnScroll', () => ({
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.$el.classList.add('is-visible');
                this.$el.querySelectorAll('.catalog-card').forEach((card) => {
                    card.classList.add('is-visible');
                });

                return;
            }

            const io = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) {
                            return;
                        }

                        entry.target.classList.add('is-visible');

                        entry.target.querySelectorAll('.catalog-card').forEach((card) => {
                            card.classList.add('is-visible');
                        });

                        io.unobserve(entry.target);
                    });
                },
                { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
            );

            io.observe(this.$el);
        },
    }));
});

Alpine.start();
