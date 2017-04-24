
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

require_once dirname(__FILE__) .'/../model/Customer.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
require_once dirname(__FILE__) .'/functions.php';
require_once dirname(__FILE__) .'/../model/Pdf.php';
require_once dirname(__FILE__) .'/../model/ReportModule.php';

require_once dirname(__FILE__) .'/../model/Project.php';
require_once dirname(__FILE__) .'/../model/Customer.php';
require_once dirname(__FILE__) .'/../model/Contact.php';

$createdate = date("Y-m-d");
$ip = $_SERVER["REMOTE_ADDR"];
$project_id = $_GET['projectid'];

$project = new Project();
$project->load($project_id);
$projectname = $project->name;
$customer_id = $project->customerid;
//Load customer

$customer = new Customer();
$customer->load($customer_id);
$customername = $customer->name;
$customerid = $customer->id;


//load contact
$Contact = new Contact();
$Contact->load($customerid);
$contactname = $Contact->name;
$phone = $Contact->phone;

$session = new StartSession();
$user = $session->get('user');

$namemap = array(
    "ltm" => "LOADBALANCING",
    "apm" => "AAA",
    "gtm" => "GSLB",
    "asm" => "APPFIREWALL"
);

if (!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /');
    exit();
}
$uuid = $session->get('uuid');
$npmodules2 = $session->get('npmodules2');

if (!isset($npmodules2) || !$npmodules2 || true) {
    $npmodules2 = [];
    $c = new Conversion(array('files_uuid' => $uuid));
    $c->load('files_uuid');
    if (!$c->loadChildren()) {
        error_log("could not load features");
    }
    $i = 0;

    foreach ($c->_features as &$f) {
        $npmodules2[$f->name] = [];
        $npm = &$npmodules2[$f->name];

        $p_converted = ($f->attributes && $f->attributes > 0) ?
                round($f->converted / $f->attributes * 100) : 0;

        $npm["id"] = $f->id;
        $npm["friendly_name"] = $f->name;
        $npm["objgrp_count"] = $f->modules;
        $npm["object_count"] = $f->objects;
        $npm["attribute_count"] = $f->attributes;
        $npm["attribute_converted"] = $f->converted;
        $npm["attribute_omitted"] = 0;
        $npm["p_converted"] = $p_converted;
        $npm["ns_name"] = isset($namemap[$f->name]) ? $namemap[$f->name] : "";

        if ($f->name == 'ltm') {
            $f->loadChild(array('name' => 'rule'));
            if (isset($f->_modules['rule'])) {
                $rule = &$f->_modules['rule'];
                $npmodules2['rule'] = [];
                $npm2 = &$npmodules2['rule'];

                $p_converted = ($rule->attributes && $rule->attributes > 0) ?
                        round($rule->converted / $rule->attributes * 100) : 0;

                $npm2["id"] = $rule->id;
                $npm2["friendly_name"] = "iRULE";
                $npm2["object_count"] = $rule->objects;
                $npm2["attribute_count"] = $rule->attributes;
                $npm2["attribute_converted"] = $rule->converted;
                $npm2["attribute_omitted"] = 0;
                $npm2["ns_name"] = "APPEXPERT";
                $npm2["p_converted"] = $p_converted;

                $npm["attribute_count"] -= $npm2["attribute_count"];
                $npm["attribute_converted"] -= $npm2["attribute_converted"];
                $npm["attribute_omitted"] -= 0;
                $i++;
            }
        }

        $i++;
    }

    //$npmodules2["_data"]["module_count"] = $i;
    $npmodules2["_data"]["attribute_count"] = $c->attribute_count;
    $npmodules2["_data"]["attribute_converted"] = $c->attribute_converted;
    $npmodules2["_data"]["object_count"] = $c->object_count;
    $npmodules2["_data"]["module_count"] = $c->module_count;
    $npmodules2["_data"]["feature_count"] = $c->feature_count;
    $npmodules2["_data"]["np_version"] = $c->np_version;
    $npmodules2["_data"]["f5_version"] = $c->f5_version;

    $session->set('npmodules2', $npmodules2);
   
}

$Version = 'Unknown';
$lic = 'Standard';
$datostable = array();
foreach ($npmodules2 as $data1 => $value) {
    if ($data1 == 'ltm') {
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == 'rule') {
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == 'apm') {
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == 'asm') {
        if ($value['object_count'] > 0) {
            $lic = "Platinum";
        }
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == 'net') {
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == 'sys') {
        array_push($datostable, array(
            'friendly_name' => $value['friendly_name'], 
            'ns_name' => $value['ns_name'], 
            'object_count' => $value['object_count'], 
            'attribute_count' => $value['attribute_count'], 
            'p_converted' => $value['p_converted'],));
    }
    if ($data1 == '_data') {
        if ($value['f5_version'] == 'TMSH') {
            $Version = "11 or newer (TMSH)";
        } else {
            if ($value['f5_version'] == 'BIGPIPE') {
                $Version = "10 or older (BIGPIPE)";
            }
        }
    }
    if ($data1 == 'gtm') {
        if ($value['object_count'] > 1) {
            $lic = "Enterprise";
        }
    }
}
//load grup modules
$module = new ReportModule();
//apm
$grupmoduleapm = 'apm';
$moduleApm = $module->getModuleDetails($grupmoduleapm, $npmodules2);
//asm
$grupmoduleasm = 'asm';
$moduleAsm = $module->getModuleDetails($grupmoduleasm, $npmodules2);
//ltm
$grupmoduleltm = 'ltm';
$moduleLtm = $module->getModuleDetails($grupmoduleltm, $npmodules2);
//net
$grupmodulenet = 'net';
$moduleNet = $module->getModuleDetails($grupmodulenet, $npmodules2);
//sys
$grupmodulesys = 'sys';
$moduleSys = $module->getModuleDetails($grupmodulesys, $npmodules2);
//wom
$grupmodulewom = 'wom';
$moduleWom = $module->getModuleDetails($grupmodulewom, $npmodules2);
//rule
$grupmodulerule = 'rule';
$moduleRule = $module->getModuleDetails($grupmodulerule, $npmodules2);
//rule
$grupmodulegtm = 'gtm';
$moduleGtm = $module->getModuleDetails($grupmodulegtm, $npmodules2);

// Instanciation of inherited class
$pdf = new PDF();

// Column headers
$header = array('F5 Module', 'NetScaler Module', 'Objects', 'Attributes', 'Converted %');
$headerLtm = array('F5 Object Groups', '# Objects', '% Converted');


// Data loading
$pdf->SetFont('Arial', '', 10);
$pdf->AddPage();

//section F5 version
$pdf->SetXY(10, 30);
$pdf->Cell(10, 35, 'F5 Config & iRule Version', 0, 1);
$pdf->SetXY(10, 42);
$pdf->Cell(10, 20, $Version, 0, 1);

//Section recommended Ns
$pdf->SetXY(115, 30);
$pdf->Cell(15, 35, 'Recommended NS License', 0, 1);
$pdf->SetXY(115, 42);
$pdf->Cell(10, 20, $lic, 0, 1);

$center=15;
$pdf->BasicTable($header, $datostable,$center);
$pdf->Cell(30, 10, '', 0, 1, 'R');


if (!empty($moduleApm)) {
    $center=50;
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmoduleapm), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->SetFont("");
    //$pdf->Cell(10, 5, '', 0, 1, 'R'); 
    $pdf->BasicTable($headerLtm, $moduleApm,$center);
}

if (!empty($moduleAsm)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmoduleasm), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleAsm,$center);
}

if (!empty($moduleLtm)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmoduleltm), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleLtm,$center);
}

if (!empty($moduleNet)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmodulenet), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleNet,$center);
}

if (!empty($moduleSys)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmodulesys), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleSys,$center);
}

if (!empty($moduleWom)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmodulewom), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleWom,$center);
}

if (!empty($moduleRule)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmodulerule), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleRule,$center);
}

if (!empty($moduleGtm)) {
    $center=50;
    //$pdf->AddPage();
    $pdf->Cell(20, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(232, 232, 232, 1);
    $pdf->Cell(40,6,'',0,0,'C');
    $pdf->Cell(108, 10, strtoupper($grupmodulegtm), 0, 1, 'C', true);
    $pdf->Ln(5);
    $pdf->BasicTable($headerLtm, $moduleGtm, $center);
}

//Save report
$filename = $uuid . '-' . $createdate . '.pdf';
$filters = "";
$locationfile = "/Uploads/Reports";
$report = new ReportModule(array(
    "filename" => $filename,
    "filters" => $filters,
    "locationfile" => $locationfile,
    "uuId" => $uuid,
    "usercreate" => $user->id,
    "createdate" => $createdate,
    "ip" => $ip));
$report->save();

//Save file PDF to server
$pdf->Output(dirname(__dir__) . "/Uploads/Reports/" . $uuid . "-" . $createdate . ".pdf", "F");
//Show file PDF to navegator
$pdf->Output($uuid . '.pdf', 'I');

$result["message"] = 'filename.pdf';

?>

