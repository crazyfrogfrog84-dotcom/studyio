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
