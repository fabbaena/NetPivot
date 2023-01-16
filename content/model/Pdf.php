<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../engine/fpdf.php';

class PDF extends FPDF {

    // Load data
    function LoadData($file) {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', trim($line));
        return $data;
    }

// Simple table
    function BasicTable($header, $data, $center) {
        // Header

        $this->SetFillColor(61, 137, 203, 1);
        $this->SetTextColor(255);
        $this->SetDrawColor(61, 137, 203, 1);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        $w = array();

        for($i = 0; $i < count($header); $i++) {
            $w[$i] = $this->GetStringWidth($header[$i]) + 1;
        }
        foreach($data as $row) {
            $i = 0;
            foreach($row as $col) {
                $cw = $this->GetStringWidth($col) + 1;
                if($cw > $w[$i]) $w[$i] = $cw;
                $i++;
            }
        }
        $total_width = 0;
        for($i = 0; $i < count($w); $i++) {
            $total_width += $w[$i];
        }
        if($total_width < $this->GetPageWidth()) {
            $center = ($this->GetPageWidth() - $total_width) / 2;
        }
        $this->SetX($center);


        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(232, 232, 232, 1);
        $this->SetTextColor(34, 30, 31, 1);
        $this->SetFont('');

        // Data
        $fill = false;

        foreach ($data as $row) {
            $this->SetX($center);
            $i = 0;
            foreach ($row as $col) {
                if (is_numeric($col)) {
                   $this->Cell($w[$i], 10, $col, 'LR', 0, 'C', $fill);
                } else {
                   $this->Cell($w[$i], 10, $col, 'LR', 0, 'L', $fill);
                }
                $i++;
            }
            $this->Ln();
            $fill = !$fill;

            //$this->Cell(35, 0, '', 'T');
        }

        $this->SetX($center);
        $this->Cell($total_width, 0, '', 'T');
    }

// Better table
    function ImprovedTable($header, $data) {
        // Column widths
        $w = array(30, 40, 20, 20, 20);
        // Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR');
            $this->Cell($w[1], 6, $row[1], 'LR');
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R');
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R');
            $this->Cell($w[4], 6, number_format($row[3]), 'LR', 0, 'R');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

// Colored table
    function FancyTable($header, $data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(30, 40, 20, 20, 20);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

    function Header() {
        global $title;
        global $projectname;
        global $customername;
        global $contactname;
        global $phone;
        // Logo
        $host = $_SERVER["HTTP_HOST"];

        $this->Image('https://' . $host . '/images/logocitrix-netpivot.png', 10, 6, 60);

        
        // Arial bold 12
        $this->SetFont('Arial', '', 15);
        $this->SetTextColor(61, 137, 203, 1);
        // Move to the right
        $this->Cell(60);
        // Title
        $this->Cell(30, 6, $projectname, 0, 0, 'C');
        $this->Cell(30, 10, '', 0, 1, 'R');
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(35, 11, 'Customer: ');
        $this->SetFont('', '', 11);
        $this->Cell(20, 11, $customername, 0, 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(35, 3, 'Contact Name: ');
        $this->SetFont('', '', 11);
        $this->Cell(60, 3, $contactname);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(35, 3, 'Contact Phone: ');
        $this->SetFont('', '', 11);
        $this->Cell(58, 3, $phone);
        // Line break
        $this->Ln(10);
    }

    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

}
