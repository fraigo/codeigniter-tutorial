<?php

/**
 * Generate HTML attributes from array
 */
function htmlAttributes($attrs=[]){
    if (!$attrs){
        return "";
    }
    $content = [];
    foreach($attrs as $attr=>$value){
        if ($value===true){
            $content[] = "$attr";
        } else {
            $content[] = "$attr=\"$value\"";
        }
        
    }
    return implode(' ',$content);
}

function htmlContent($content){
    if (is_array($content)){
        if (@$content["tag"]){
            return htmlTag($content["tag"],@$content["content"],@$content["attributes"]);
        } else {
            $contents=[];
            foreach($content as $cont){
                $contents[] = htmlContent($cont);
            }
            return implode("",$contents);
        }
    } else {
        return $content;
    }
}

function htmlTag($tag,$content,$attributes=[]){
    $attr = htmlAttributes($attributes);
    if (is_array($content)){
        if (@$content["tag"]){
            $content = htmlTag($content["tag"],@$content["content"],@$content["attributes"]);
        } else {
            $content = htmlContent($content);
        }
    }
    return "<$tag $attr>$content</$tag>";
}

/**
 * Generate HTML table cell (td or th)
 */
function htmlCell($content,$header=false,$attributes=[]){
    if ($header){
        $cellTag = "th";
    } else {
        $cellTag = "td";
    }
    return htmlTag($cellTag,$content,$attributes);
}

function parseData($content,$data){
    if (is_array($content)){
        foreach($content as $key=>$col){
            if (is_array($col)){
                $content[$key] = parseData($col, $data);
            } else {
                foreach($data as $fld=>$value){
                    $content[$key] = str_replace("{$key}","$value", "$col");
                }
            }
        }
    } else {
        foreach($data as $fld=>$value){
            $content = str_replace("{$fld}","$value", $content);
        }
    }
    return $content;
}

/**
 * Generate HTML table row (tr)
 */
function htmlRow($row,$columns,$header=false){
    if (!is_array($row)) return "";
    $cols = [];
    foreach($columns as $fld=>$cfg){
        if (!is_array($cfg)){
            $cfg=["label"=>$cfg];
        }
        $cfg = parseData($cfg,$row);
        if ($header){
            $content = @$cfg["label"];
        } else {
            $content = @$row[$fld];
            if (@$cfg["content"]){
                $content = $cfg["content"];
            }
            if (@$cfg["options"]){
                $content = @$cfg["options"][@$row[$fld]];
            }
        }
        $cols[] = htmlCell($content,$header,@$cfg["cellAttributes"]);
    }
    $content = implode("\n",$cols);
    $template = "<tr>
        $content
    </tr>";
    foreach($row as $fld=>$value){
        $template = str_replace("{".$fld."}", "$value", $template);
    }
    return $template;
}

/**
 * Generate HTML table
 * @param array $columns Custom column names
 * @param array $attrs HTML Table attributes
 */
function htmlTable($data, $columns=null, $attrs=[]){
    if (!$columns){
        $columns = [];
        foreach($data[0] as $fld=>$val){
            $columns[$fld] = ["label"=>ucfirst($fld)];
        }
    }
    $thead = htmlRow([], $columns, true);
    $rows = [];
    foreach($data as $row){
        $rows[] = htmlRow($row, $columns, false);
    }
    $tbody = implode("\n",$rows);
    $attrs = htmlAttributes($attrs);
    $template = "<div class=\"table-container\" ><table $attrs>
        <thead>$thead</thead>
        <tbody>$tbody</tbody>
    </table></div>";
    return $template;
}