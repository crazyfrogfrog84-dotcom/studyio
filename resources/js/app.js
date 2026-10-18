document.addEventListener('click', (event) => {
    const addButton = event.target.closest('[data-add-item]');
    if (addButton) {
        const section = addButton.closest('.repeat-section');
        const list = section?.querySelector('.repeat-list');
        const template = section?.querySelector('template');
        if (!list || !template) return;

        const index = list.children.length;
        const fragment = template.content.cloneNode(true);
        fragment.querySelectorAll('[name]').forEach((field) => {
            field.name = field.name.replaceAll('__INDEX__', String(index));
        });
        list.append(fragment);
        list.lastElementChild?.querySelector('input:not([type="hidden"]), textarea')?.focus();
        return;
    }

    const removeButton = event.target.closest('.remove-row');
    if (removeButton) {
        removeButton.closest('.repeat-card')?.remove();
    }
});

const modernLayout = document.querySelector('.modern-layout');
const modernSections = modernLayout?.querySelectorAll('.modern-section');

if (modernLayout && modernSections?.length && 'IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    modernLayout.classList.add('has-scroll-reveal');
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -28px 0px' });

    modernSections.forEach((section) => revealObserver.observe(section));
}
