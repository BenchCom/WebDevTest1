<?php
/* ---------------------------------------------------------------------
 * module: get_projects.php
 * input: employee, week
 *         example >>> /get_projects.php
 *                          employee=123456
 *                          week=20191231
 * --------------------------------------------------------------------- */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$errors = array();
$data   = array();

if(empty($_POST['employee']))
    $errors['employee'] = 'Employee Number is required.';

if(empty($_POST['week']))
    $errors['week'] = 'Calendar Week is required.';

if(!empty($errors)) {

    $data['success'] = false;
    $data['errors']  = $errors;
}
else {

    $soapUrl = "http://192.168.1.53:24444/cgi-bin/Maconomy/MaconomyWS.prod.cs_CZ.exe/soap.ms?service=WebTimeSheets";
    $soapRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"'.
                   '                  xmlns:mac="maconomy.com">'.
                   '<soapenv:Header/>'.
                   '  <soapenv:Body>'.
                   '    <mac:GetProjectsForWeek>'.
                   '       <EmployeeNumber>'.$_POST['employee'].'</EmployeeNumber>'.
                   '       <WeekDate>'.$_POST['week'].'</WeekDate>'.
                   '    </mac:GetProjectsForWeek>'.
                   '  </soapenv:Body>'.
                   '</soapenv:Envelope>';

    $soapOptions = array(
                        'http' => array(
                                    'header'  => "Content-type: text/xml",
                                    'method'  => 'POST',
                                    'content' => $soapRequest)
                        );
    $soapContext  = stream_context_create($soapOptions);
    $soapResponse = file_get_contents($soapUrl, false, $soapContext);
    
    // why ?
    if(false) {
        $errors['SOAPcall'] = 'SOAP call failed.';
        $data['success'] = false;
        $data['errors']  = $errors;
    }
    else {
        // why ?
        $simpleResponse = strtr($soapResponse, ['</soap:' => '</',    '<soap:' => '<', 
                                                '</namesp1:' => '</', '<namesp1:' => '<']);

        $simpleXml = simplexml_load_string($simpleResponse);

        $data['success'] = true;
        $data['message'] = 'Success!';
        // why ?
        $data['response'] = json_decode(json_encode($simpleXml), TRUE);
    }
}

echo json_encode($data);
?>
