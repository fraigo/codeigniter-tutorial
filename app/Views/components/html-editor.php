<?php
    $images = [
        // [ "title" => "Frank" , "value" => "/images/frank.jpg"]
    ];
    $componentId = @$id ?: 'editor'.rand(10000,99999);
?>
<script src="/components/tinymce/tinymce.min.js" ></script>
<input type="file" id="htmleditorfile" accept="image/*" style="display: none">
<textarea name="<?= @$name ?>" id="<?= $componentId ?>" style="opacity:0;width:100%;height:<?= @$height ? "{$height}px" : '300px' ?>" contenteditable><?= $value ?></textarea>
<script>
    var editor = tinymce.init({
        selector: "textarea#<?= $componentId ?>",
        body_class: "some_class_name",
        menubar: false,
        height: <?= @$height ?: 300 ?>,
        // https://www.tiny.cloud/docs/tinymce/6/available-toolbar-buttons/
        toolbar: "undo redo | bold italic | h1 h2 h3 h4 | fontfamily | alignleft aligncenter alignright | forecolor backcolor | bullist numlist | link image table | fontsize template codesample ",
        toolbar_groups: {
            headings: {
                text: 'Headings',
                tooltip: 'Headings',
                items: 'h1 h2 h3 h4'
            }
        },
        readonly: <?=@$readonly ? 'true' : 'false'?>,
        relative_urls : false,
        remove_script_host : true,
        image_list: <?php echo json_encode($images) ?>,
        file_picker_types: 'image',
        file_picker_callback: (callback, value, meta) => {
            // Provide file and text for the link dialog
            var fileInput = document.getElementById('htmleditorfile');
            
            /*
            if (meta.filetype == 'file') {
                tinymce.activeEditor.windowManager.openUrl({
                    title: 'Select Local Links',
                    url: '/dialogs/linkselector',
                    onMessage: function(api, details){
                        console.log('message', details)
                        if (details.mceAction=='customAction' && details.data && details.data.url){
                            callback(details.data.url, { text: details.data.text || '' });
                        }
                        api.close()
                    }
                });
            }
            */

            // Provide image and alt text for the image dialog
            if (meta.filetype == 'image') {
                fileInput.setAttribute("accept","image/*")
                fileInput.onchange = function(e){
                    var file = fileInput.files[0]
                    if (file){
                        console.log(file)
                        var reader = new FileReader()
                        reader.addEventListener("load", () => {
                            callback(reader.result, { alt: file.name });
                        }, false);
                        reader.readAsDataURL(file);
                    }
                }
                fileInput.click()
            }

            /* media
            if (meta.filetype == 'media') {
                //callback('movie.mp4', { source2: 'alt.ogg', poster: 'image.jpg' });
            }
            */
        },
        contextmenu: 'link image table element',
        font_family_formats: "Normal=; Times=Cambria,Times,sans-serif; Mono=Courier New,courier,monospace",
        plugins: ["advlist", "anchor", "autolink", "codesample", "fullscreen", "help", "image", "lists", "link", "media", "preview", "searchreplace", "table", "template", "visualblocks", "wordcount", "element"],
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
        setup: function(editor) {
            editor.on('focus', function(e) {
                window.sel = e.target.selection
                console.log('focus', e.target.selection)
                $(e.target.container).find('.mce-edit-area').toggleClass('focused');
            });

            editor.on('blur', function(e) {
                console.log('blur', e.target.container)
                $(e.target.container).find('.mce-edit-area').toggleClass('focused');
            });
        },
        content_css: ['/css/bootstrap.min.css','/css/style.css']
    });
</script>
<style>
    .tox[aria-disabled='true'] .tox-editor-header{
        display: none;
    }
</style>