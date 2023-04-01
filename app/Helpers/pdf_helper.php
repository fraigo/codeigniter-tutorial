<?php

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        //$image_file = ROOTPATH.'logo_example.jpg';
        //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        //$this->SetFont('helvetica', 'B', 20);
        // Title
        // $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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

function html_to_pdf($html,$filename=null,$overwrite=false){
    $file = ROOTPATH . "writable/pdf/$filename";
    if (file_exists($file) && !$overwrite){
        return $file;
    }
    chdir(ROOTPATH.'public');
    $pdf = new MYPDF();
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