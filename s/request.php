<?php
header('Content-Type: text/html; charset=utf-8');
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'/phpmailer/class.phpmailer.php';
actionSendMail();



function actionSendMail(){
    $errorMsg = array(
        "send_error"=>"Сервена помилка при запиті: '",
        "required1"=>"Будь-ласка вкажіть ",
        "required2"=>" ",
        "valid_email"=>"Будь-ласка вкажіть email (формат: name@domain.com)",
        "valid_phone"=>"Будь-ласка вкажіть телефон у вірному форматі (формат 0990504455)",
        "success"=>'Ваш запит успішно оброблений. Ми з вами з\'єднаємось',
    );

    //sleep(5000);
    $responseArr = array();
    $validationErrors = array();

      $validated = validateFields($validationErrors,$errorMsg);
     //$validated = true;
    /*
    *
    *  if (isset($_REQUEST["name"]) && trim($_REQUEST["name"]!='')
       && isset($_REQUEST["email"])&& trim($_REQUEST["email"]!='')
       && isset($_REQUEST["phone"]) && trim($_REQUEST["phone"]!='')
       && isset($_REQUEST["message"]) && trim($_REQUEST['message'])!='')
    */
    if ($validated)
    {

        $name = $_REQUEST["name"];
        //$reply_to_email = $_REQUEST["email"];

        $reply_to_email = 'kean.dev@gmail.com';
        //$_REQUEST['ajax_request']  = 'on';
        $auth_email = 'konstantin.pryanichnikov@onix-systems.com'; //email that pass auth

        $phone = $_REQUEST["phone"];
        $message = ""; 
        $mail = new  PHPMailer(true);
        $mail->IsSMTP();
        $mail->Host = "smtp.yandex.ru";
//                $mail->Port = Yii::app()->params->smtp_port;
//                $mail->SMTPSecure = "ssl";
        $mail->SMTPAuth = true;
        $mail->Username = $auth_email;
        $mail->CharSet = 'UTF-8';
        $mail->Password = 'dtxyfzdtcyf';
        $mail->AddReplyTo($reply_to_email, 'Reply to '.$name);

        $mail->Subject = 'Запит від \''.$name.'\'  ('.$phone.')';
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->MsgHTML('<p>'.$message.'</p><br/><br/>  <p><span style="color:#ccc">Телефон клієнта: </span>'.$phone.'</p>');
        $mail->SetFrom($auth_email, $name);

        $emails =  array('parkovaoselya@ukr.net');   
        //adding addresses
        foreach ($emails as $send_email)  {
            $mail->AddAddress($send_email, '');
        }
        if (isset($_FILES['uploadfile'])){
            $validAttachments = array();

            foreach($_FILES['uploadfile']['name'] as $index => $fileName) {

                $filePath = $_FILES['uploadfile']['tmp_name'][$index];
                if(is_uploaded_file($filePath))  {

                    $attachment = new stdClass;
                    $attachment->fileName = $fileName;
                    $attachment->filePath = $filePath;
                    $validAttachments[] = $attachment;
                }
            }
            foreach($validAttachments as $attachment) {
                $mail->AddAttachment($attachment->filePath, $attachment->fileName);
            }
        }
        //$res = true;
        $send = false;

        try{
            $mail->Send();
            $send = true;
        }
        catch(Exception $ex){
            $responseArr['msg'] = $errorMsg['send_error'].$ex->getMessage();
            $responseArr['status'] = 'error';
        }
        if ($send){
            $responseArr['msg'] = $errorMsg["success"];
            $responseArr['status'] = 'success';
        }

    }  else{
        $responseArr['status']='validation_error';
        $responseArr['validation_errors']= $validationErrors;
    }
    if (isset($_REQUEST['ajax_request']) && $_REQUEST['ajax_request'] === 'on'){
        echo json_encode($responseArr);
    } else {
        //var_dump($responseArr);
        if ($responseArr['status'] == 'validation_error') {
            foreach ($responseArr['validation_errors'] as $key=>$err ){
               echo '<p>Помилка вводу: <span style="color:#f46150">'.$err.'</span></p>';
            }
        } else{

           echo '<p style="color:green">'.$responseArr['msg'].'</p>';
        }
        ?>
            <script>

                function second_passed() {

                    window.location='/';

                }

                setTimeout(second_passed, 1500) ;


            </script>
        <?php
    }


}

function validateFields(&$validationErrors,$errMsg){

    $validated = true;

    if (!isset($_REQUEST["name"]) || strlen(trim($_REQUEST["name"])) === 0 || $_REQUEST["name"]  == 'Введіть Ваше ім’я' ){
        $validated = false;
        $validationErrors['name']= $errMsg['required1']." ім'я".$errMsg['required2'];
        //var_dump($validationErrors);
    }
//    if (!isset($_REQUEST["email"]) || strlen(trim($_REQUEST["email"])===0)  || strtolower(trim($_REQUEST["email"]))  == 'e-mail'  ){
//        $validated = false;
//        // echo "email now: ".$_REQUEST["email"];
//        $validationErrors['email']= $errMsg['required1']."E-mail".$errMsg['required2'];
//    }
    if (!isset($_REQUEST["phone"]) || strlen(trim($_REQUEST["phone"])) ===0 || $_REQUEST["phone"] == 'Введіть Ваш телефон' ){
        $validated = false;
        $validationErrors['phone']= $errMsg['required1']."телефон".$errMsg['required2'];

    }
    if (isset($_REQUEST['message_set']) && $_REQUEST['message_set']=='1') {

        if (!isset($_REQUEST["message"]) || strlen(trim($_REQUEST["message"])) === 0 || $_REQUEST["message"] == "Текст повідомлення" ){
            $validated = false;
            $validationErrors['message']= $errMsg['required1']."повідомлення".$errMsg['required2'];
        }
    }


    if (count($validationErrors)===0) //if no errors, continue validation
    {

//        $validated = filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL);
//        if (!$validated){
//            $validationErrors['email'] = $errMsg['valid_email'];
//        }
        if ($validated){
            $validated = preg_match("/^([0-9\(\)\/\+ \-]{7,21})$/",$_REQUEST["phone"])   ;
            $validationErrors['phone'] = $errMsg['valid_phone'];
        }

    }

    return $validated;

}
