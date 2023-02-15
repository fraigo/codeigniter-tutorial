<script>
    function tableChange(table){
        document.location="/import/"+table
    }
    function parseHeaders(element){
        var content = element.value
        var lines = content.split("\n")
        if (lines.length > 0){
            var headers = lines[0].split('\t')
            if (headers.length>1){
                if (confirm('Use existing headers?'+lines[0])){
                    element.form.selected_fields.value=headers.join("\n")
                    lines.splice(0,1)
                    element.value = lines.join("\n")
                }

            }
        }
    }
</script>