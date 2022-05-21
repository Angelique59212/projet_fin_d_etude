const plugins = 'link image code';
const toolbar = 'undo redo | styleselect | forecolor | bold italic | alignleft aligncenter alignright alignjustify | outdent indent';

tinymce.init({
    selector: '#editor-summary',
    width: '70vw',
    height: '250px',
    plugins,
    toolbar
});

tinymce.init({
    selector: '#editor-content',
    width: '70vw',
    height: '50vh',
    plugins,
    toolbar
});
