<?php

//include('php-barcode.php');
//require('fpdf.php');
require('tcpdf.php');

class PDF extends FPDF {
    
    var $widths;
    var $aligns;

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','',10);
        // Print current and total page numbers
        
        //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

        if ($this->hasFooter) {
            /*
            $this->MultiCell(0, 4,'This document shall be deemed uncontrolled unless labelled "CONTROLLED".' . 
                         "\nUser should verify latest revision.",1,'C');*/

            $this->Cell(/*190*/ $this->w - 10, 4, 'This document shall be deemed uncontrolled unless labelled "CONTROLLED"', 0, 0, 'C');
            $this->ln();
            $this->Cell(/*107*/ $this->w * 0.324242 * 1.64, 4, 'User should verify latest', 0, 0, 'R');
            $this->SetFont('Arial','B',10);
            $this->Cell(/*83*/ $this->w * 0.25152, 4, 'revision.', 0, 0, 'L');
        } else {
            $this->MultiCell(0,4, '',0,'C');
        }
        
    }

    function Row($data) {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function Row2($data) {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, $h,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CustomRow($data, $height, $fontStyle, $fontSize, $customFont = "Arial") {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            //Draw the border
            $this->Rect($x, $y, $w, $h);

            $this->SetFont($customFont, $fontStyle[$i], $fontSize[$i]);
            $x1 = $this->GetX();
            $y1 = $this->GetY();
            //Print the text
            $this->MultiCell($w, $height, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CustomRow2($data, $height, $fontStyle, $fontSize , $toggleBorders = 1, $customFont = "Arial") {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            //Draw the border
            //$this->Rect($x, $y, $w, $h);

            $this->SetFont($customFont, $fontStyle[$i], $fontSize[$i]);
            $x1 = $this->GetX();
            $y1 = $this->GetY();
            //Print the text
            $this->Cell($w, $height, $data[$i], $toggleBorders, 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w,$y);
        }
        //Go to the next line
        $this->Ln();
    }

    function CustomRow3($data, $height, $fontStyle, $fontSize , $toggleBorders = 1, $customFont = "Arial") {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            //Draw the border
            //$this->Rect($x, $y, $w, $h);

            $this->SetFont($customFont, $fontStyle[$i], $fontSize[$i]);
            $x1 = $this->GetX();
            $y1 = $this->GetY();
            //Print the text
            $this->MultiCell($w, $height, $data[$i], $toggleBorders, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w,$y);
        }
        //Go to the next line
        $this->Ln();
    }
    
    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function RowforAbs($data) {
        //Calculate the height of the row
        $nb = 0;
        
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $newData = $this->CheckPageBreak($h,$this->h,$data);
        if(!empty($newData)){
            $data  = $newData;  
        }
        //Draw the cells of the row
        for($i=0; $i < count($data); $i++)
        {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    // Load data
    function LoadData($_data) {
        // Read data array/s
        $data = array();

        foreach ($_data as $items) {
            # code...
        }
        return $data;
    }

    // Simple table
    function generateTable($header, $data) {
        // Header
        $pdf = new PDF();
        $pdf->Row($header);

        // Data
        /*
        foreach($data as $row){
            $pdf->Row($row);
        }*/
    }

    function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){

        $wide = $baseline;
        $narrow = $baseline / 3 ; 
        $gap = $narrow;

        $barChar['0'] = 'nnnwwnwnn';
        $barChar['1'] = 'wnnwnnnnw';
        $barChar['2'] = 'nnwwnnnnw';
        $barChar['3'] = 'wnwwnnnnn';
        $barChar['4'] = 'nnnwwnnnw';
        $barChar['5'] = 'wnnwwnnnn';
        $barChar['6'] = 'nnwwwnnnn';
        $barChar['7'] = 'nnnwnnwnw';
        $barChar['8'] = 'wnnwnnwnn';
        $barChar['9'] = 'nnwwnnwnn';
        $barChar['A'] = 'wnnnnwnnw';
        $barChar['B'] = 'nnwnnwnnw';
        $barChar['C'] = 'wnwnnwnnn';
        $barChar['D'] = 'nnnnwwnnw';
        $barChar['E'] = 'wnnnwwnnn';
        $barChar['F'] = 'nnwnwwnnn';
        $barChar['G'] = 'nnnnnwwnw';
        $barChar['H'] = 'wnnnnwwnn';
        $barChar['I'] = 'nnwnnwwnn';
        $barChar['J'] = 'nnnnwwwnn';
        $barChar['K'] = 'wnnnnnnww';
        $barChar['L'] = 'nnwnnnnww';
        $barChar['M'] = 'wnwnnnnwn';
        $barChar['N'] = 'nnnnwnnww';
        $barChar['O'] = 'wnnnwnnwn'; 
        $barChar['P'] = 'nnwnwnnwn';
        $barChar['Q'] = 'nnnnnnwww';
        $barChar['R'] = 'wnnnnnwwn';
        $barChar['S'] = 'nnwnnnwwn';
        $barChar['T'] = 'nnnnwnwwn';
        $barChar['U'] = 'wwnnnnnnw';
        $barChar['V'] = 'nwwnnnnnw';
        $barChar['W'] = 'wwwnnnnnn';
        $barChar['X'] = 'nwnnwnnnw';
        $barChar['Y'] = 'wwnnwnnnn';
        $barChar['Z'] = 'nwwnwnnnn';
        $barChar['-'] = 'nwnnnnwnw';
        $barChar['.'] = 'wwnnnnwnn';
        $barChar[' '] = 'nwwnnnwnn';
        $barChar['*'] = 'nwnnwnwnn';
        $barChar['$'] = 'nwnwnwnnn';
        $barChar['/'] = 'nwnwnnnwn';
        $barChar['+'] = 'nwnnnwnwn';
        $barChar['%'] = 'nnnwnwnwn';

        $this->SetFont('Arial','',10);
        $this->Text($xpos, $ypos + $height + 4, $code);
        $this->SetFillColor(0);

        $code = '*'.strtoupper($code).'*';
        for($i=0; $i<strlen($code); $i++){
            $char = $code[$i];
            if(!isset($barChar[$char])){
                $this->Error('Invalid character in barcode: '.$char);
            }
            $seq = $barChar[$char];
            for($bar=0; $bar<9; $bar++){
                if($seq[$bar] == 'n'){
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                if($bar % 2 == 0){
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $gap;
        }
    }

    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0) { 
        $font_angle+=90+$txt_angle; 
        $txt_angle*=M_PI/180; 
        $font_angle*=M_PI/180; 
    
        $txt_dx=cos($txt_angle); 
        $txt_dy=sin($txt_angle); 
        $font_dx=cos($font_angle); 
        $font_dy=sin($font_angle); 
    
        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt)); 
        if ($this->ColorFlag) 
            $s='q '.$this->TextColor.' '.$s.' Q'; 
        $this->_out($s); 
    } 

}

?>