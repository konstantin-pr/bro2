<!DOCTYPE html>
<html> 
<head>
    <title>Оновити цiни</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <meta name="robots" content="noindex"/> 
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=1200, initial-scale=0.5, minimal-ui, target-densityDpi=device-dpi" />   
    <style>
        .main {
            text-align:center;
        }
        
        .main .form-horizontal{
            padding: 5%;
            display: inline;
        }
        .main .form-horizontal legend {
            color: yellowgreen;
        }
    </style>
<?php 
 
//get prices file content

$filepath = '../js/prices.js';

$response = file_get_contents($filepath); 
if (!$response) {
    echo ' <div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
    <strong>Ошибка :</strong>
  при чтении исходного файла 
</div>';
    exit;
}
//get necessary json info, cut js code from string
list($first, $content) = explode("window.prices = ", $response);
$response = json_decode($content,true);
 

//if not checked 
if (isset($_REQUEST['chetverta_bazova']) && !isset($_REQUEST['update'])){ 
     echo '<div style="text-align:center;" class="alert alert-info" role="alert"> <strong>Будь ласка перевірте введені ціни <br/>  Відмітьте галочку  Ціни перевірено.</strong> </div>'; 
 header("Refresh: 5;1805.php");
    
}
if (!isset($_REQUEST['update']) || !isset($_REQUEST['chetverta_bazova']) ){ 
    
    ?>
    <main class="main">
        <a href="/" target="_blank" style="padding: 2%;text-align:left; display:block;"><i>На головну сторінку</i></a>
    <form class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Змінити ціни на сайті:</legend>
<legend><i style="color: gray">поточні ціни виводяться в полях нижче</i></legend>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="chetverta_bazova">Четверта черга </label>  
  <div class="col-md-5">
  <input id="chetverta_bazova" name="chetverta_bazova" type="text" placeholder="12 200" value="<?php echo $response['chetverta_bazova'];?>" class="form-control input-md" required="">
  <span class="help-block">число в форматі 12 200  (або 12200); обовязкове поле</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="chetverta_aktsiyna">Четверта акційна ціна</label>  
  <div class="col-md-5">
  <input id="chetverta_aktsiyna" name="chetverta_aktsiyna" type="text" placeholder=" " value="<?php echo $response['chetverta_aktsiyna'];?>" class="form-control input-md">
  <span class="help-block">Якщо аційної ціни немає залиш поле пустим</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="tretya_bazova">Третя базова</label>  
  <div class="col-md-5">
  <input id="tretya_bazova" name="tretya_bazova" type="text" placeholder="14 500" class="form-control input-md" value="<?php echo $response['tretya_bazova'];?>" required="">
  <span class="help-block">не залишати пустим, формат 14 500</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="tretya_aktsiyna">Третя акцiйна</label>  
  <div class="col-md-5">
  <input id="tretya_aktsiyna" value="<?php echo $response['tretya_aktsiyna'];?>" name="tretya_aktsiyna" type="text" placeholder="" class="form-control input-md">
  <span class="help-block">(можна залишити пустим , необовязкове поле)</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="druga_bazova">Друга базова </label>  
  <div class="col-md-5">
  <input id="druga_bazova" value="<?php echo $response['druga_bazova'];?>" name="druga_bazova" type="text" placeholder="15 900 " class="form-control input-md" required="">
  <span class="help-block">(не залишати поле пустим)</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="druga_aktsiyna">Друга акцiйна</label>  
  <div class="col-md-4">
  <input  id="druga_aktsiyna" value="<?php echo $response['druga_aktsiyna'];?>" name="druga_aktsiyna" type="text" placeholder="" class="form-control input-md">
  <span class="help-block">якщо пусте поле, акційна ціна не відобразиться на сайті</span>  
  </div>
</div>

<!-- Prepended checkbox -->
    
<div class="form-group">
  <label style="color: orange" class="col-md-4 control-label" for="druga_aktsiyna">!Ціни перевірено</label>  
  <div class="col-md-4">
  <input id="update" name="update" type="checkbox"  class="form-control input-md">
  <span class="help-block">Відмітити це поле після заповнення та перевірки</span>  
  </div>
</div>
     
<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" type="submit" class="btn btn-success">Оновити ціни</button>
  </div>
</div>

</fieldset>
</form>
</main>
    
    <?php
    exit;
}
  

//update prices as form values
foreach ($response as $key => &$value) {
    if (isset($_REQUEST[$key]))
        $value =  $_REQUEST[$key];
} 
    
//write to file     
try
    {
    $fp = fopen($filepath, 'w');
    $output = str_replace('{', 'window.prices = {', json_encode($response)); 
    fwrite($fp,$output);
    fclose($fp);
}  catch ( Exception $e ) {
    echo ' <div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <strong>Ошибка :</strong>
  при записи в файл
</div>';
}
//success

echo '<div class="alert alert-success" role="alert"> <strong>Ціни    оновлено :) </strong> Перевірка:    '.$output.' </div>';   
 header("Refresh: 5;1805.php");    
?> 