<?php

/**
 * Generate HTML attributes from array
 */
function htmlAttributes($attrs=[]){ 
    $content = [];
    foreach($attrs as $attr=>$value){
        $content[] = "$attr=\"$value\"";
    }
    return implode(' ',$content);
}

/**
 * Generate HTML table cell (td or th)
 */
function htmlCell($cell,$cellTag="td"){
    return "<$cellTag>$cell</$cellTag>";
}

/**
 * Generate HTML table row (tr)
 */
function htmlRow($row,$cellTag="td"){
    if (!$row) return "";
    $cols = [];
    foreach($row as $col){
        $cols[] = htmlCell($col,$cellTag);
    }
    $content = implode("\n",$cols);
    $template = "<tr>
        $content
    </tr>";
    return $template;
}

/**
 * Generate HTML table
 * @param array $columns Custom column names
 * @param array $attrs HTML Table attributes
 */
function htmlTable($data, $columns=null, $attrs=[]){
    if (!$columns){
        $columns = array_keys($data[0]);
    }
    $thead = htmlRow($columns,"th");
    $rows = [];
    foreach($data as $row){
        $rows[] = htmlRow($row);
    }
    $tbody = implode("\n",$rows);
    $attrs = htmlAttributes($attrs);
    $template = "<table $attrs>
        <thead>$thead</thead>
        <tbody>$tbody</tbody>
    </table>";
    return $template;
}