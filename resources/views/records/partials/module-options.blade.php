@push('scripts')
<script>
(() => {
    const projectSelect = document.getElementById('record-project');
    const moduleSelect = document.getElementById('record-module');
    const modulesByProject = {{ Illuminate\Support\Js::from($moduleOptions) }};
    const emptyOption = moduleSelect.options[0].cloneNode(true);

    projectSelect.addEventListener('change', () => {
        moduleSelect.replaceChildren(emptyOption.cloneNode(true));
        (modulesByProject[projectSelect.value] || []).forEach(module => {
            moduleSelect.add(new Option(module.name, module.id));
        });
        moduleSelect.value = '';
    });
})();
</script>
@endpush
