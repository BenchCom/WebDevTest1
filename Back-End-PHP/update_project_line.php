<?php
/* ---------------------------------------------------------------------
 * module: update_project_line.php
 * input: employe, week, day1time, day2time, day3time, day4time, day5time
 *         example >>> /update_project_line.php
 *                          employee=123456
 *                          week=2020.01.01
 *                          day1time..day5time=8.00
 * --------------------------------------------------------------------- */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$errors = array();
$data   = array();

if(empty($_POST['employee']))
    $errors['employee'] = 'Employee Number is required.';

if(empty($_POST['week']))
    $errors['week'] = 'Calendar Week is required.';

if(empty($_POST['project']))
    $errors['project'] = 'Project Number is required.';

if(empty($_POST['task']))
    $errors['task'] = 'Task Number is required.';

if(empty($_POST['day1time']))
    $errors['day1time'] = 'Day1 Time is required.';

if(empty($_POST['day2time']))
    $errors['day2time'] = 'Day2 Time is required.';

if(empty($_POST['day3time']))
    $errors['day3time'] = 'Day3 Time is required.';

if(empty($_POST['day4time']))
    $errors['day4time'] = 'Day4 Time is required.';

if(empty($_POST['day5time']))
    $errors['day5time'] = 'Day5 Time is required.';

if(!empty($errors)) {

    $data['success'] = false;
    $data['errors']  = $errors;
}
else {

    // why ?
    $soapUrl = "http://192.168.1.53:24444/cgi-bin/Maconomy/MaconomyWS.prod.cs_CZ.exe/soap.ms?service=WebTimeSheets";
    $soapRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"'.
                   '                  xmlns:mac="maconomy.com">'.
                   '<soapenv:Header/>'.
                   '  <soapenv:Body>'.
                   '    <mac:UpdateProjectLineForWeek>'.
                   '      <EmployeeNumber>'.$_POST['employee'].'</EmployeeNumber>'.
                   '      <WeekDate>'.$_POST['week'].'</WeekDate>'.
                   '      <TimeRegistration>'.
                   '        <ProjectNumber>'.$_POST['project'].'</ProjectNumber>'.
                   '        <TaskNumber>'.$_POST['task'].'</TaskNumber>'.
                   '        <Day1RegisteredTime>'.$_POST['day1time'].'</Day1RegisteredTime>'.
                   '        <Day1Description>?</Day1Description>'.
                   '        <Day2RegisteredTime>'.$_POST['day2time'].'</Day2RegisteredTime>'.
                   '        <Day2Description>?</Day2Description>'.
                   '        <Day3RegisteredTime>'.$_POST['day3time'].'</Day3RegisteredTime>'.
                   '        <Day3Description>?</Day3Description>'.
                   '        <Day4RegisteredTime>'.$_POST['day4time'].'</Day4RegisteredTime>'.
                   '        <Day4Description>?</Day4Description>'.
                   '        <Day5RegisteredTime>'.$_POST['day5time'].'</Day5RegisteredTime>'.
                   '        <Day5Description>?</Day5Description>'.
                   '        <Day6RegisteredTime>?</Day6RegisteredTime>'.
                   '        <Day6Description>?</Day6Description>'.
                   '        <Day7RegisteredTime>?</Day7RegisteredTime>'.
                   '        <Day7Description>?</Day7Description>'.
                   '      </TimeRegistration>'.
                   '    </mac:UpdateProjectLineForWeek>'.
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
