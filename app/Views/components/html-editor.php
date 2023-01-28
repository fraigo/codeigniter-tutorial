<script src="/components/tinymce/tinymce.min.js" ></script>
<textarea name="<?= $name ?>" id="<?= $id ?>" style="opacity:0;width:100%;height:600px" contenteditable><?= $value ?></textarea>
<script>
    var editor = tinymce.init({
        selector: "textarea#contents",
        body_class: "some_class_name",
        menubar: false,
        // https://www.tiny.cloud/docs/tinymce/6/available-toolbar-buttons/
        toolbar: "undo redo | bold italic | headings fontfamily fontsize | alignleft aligncenter alignright | forecolor backcolor removeformat | bullist numlist | link image | template codesample ",
        toolbar_groups: {
            headings: {
                text: 'Headings',
                tooltip: 'Headings',
                items: 'h1 h2 h3 h4'
            }
        },
        font_family_formats: "Normal=; Times=Cambria,Times,sans-serif; Mono=Courier New,courier,monospace",
        plugins: ["advlist", "anchor", "autolink", "codesample", "fullscreen", "help", "image", "lists", "link", "media", "preview", "searchreplace", "table", "template", "visualblocks", "wordcount"],
        templates: [{
            "title": "2 Columns",
            "description": "",
            "content": "<div class=\"columns2 row\"><div class=\"col-12 col-sm-6\">Column1</div><div class=\"col-12 col-sm-6\">Column2</div></div><br>"
        }, {
            "title": "3 Columns",
            "description": "",
            "content": "<div class=\"columns3 row\"><div class=\"col-12 col-md-4\">Column1</div><div class=\"col-12 col-md-4\">Column2</div><div class=\"col-12 col-md-4\">Column3</div></div><br>"
        }],
        statusbar: false,
        content_css: ['/css/bootstrap.min.css','/css/style.css']
    });
</script>