<?php

class MYPDF extends TCPDF {

    public $htmlHeader = null;

    //Page header
    public function Header() {
        $this->setY(5);
        $this->WriteHTML($this->htmlHeader?:'', true, false, true, false, '');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Generated '.date("D, M js Y g:i A"), 0, false, 'L');
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'R');
    }
}

function pdf_filename($filename){
    $file = ROOTPATH . "writable/pdf/$filename";
    return $file;
}

function html_to_pdf($html,$htmlHeader='',$filename=null,$overwrite=false){
    $file = pdf_filename($filename);
    if (file_exists($file) && !$overwrite){
        return $file;
    }
    chdir(ROOTPATH.'public');
    $pdf = new MYPDF();
    $pdf->htmlHeader = $htmlHeader;
    $pdf->SetFont ('helvetica', '', 10 , '', 'default', true );
    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->AddPage();
    $pdf->WriteHTML($html, true, false, true, false, '');
    if ($filename){
        $pdf->Output($file,'F');
        return $file;
    } else {
        $pdf->Output("document.pdf");
        return null;
    }
}