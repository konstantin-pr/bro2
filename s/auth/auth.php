<?php

$login = 'test';
echo hash_hmac('sha1', 'pass', $login).'<br>';
$pass1 = '7cc031be8f6cf4073cc4302af78a0c7f555620cb';
echo hash_hmac('sha1', hash_hmac('sha1', $login, 'pass'), $login).'<br>';
$pass2 = '186e9f6871599932a75ba9c4d2be6e858ebbc268';

function key_session($regenerate = false) {
    if (isset($_SESSION['key']) and ! $regenerate) {
        $key = $_SESSION['key'];
    } else {
        $key = hash_hmac('sha1', microtime(), mt_rand());
        $_SESSION['key'] = $key;
    }
    return $key;
}

function auth() {
    global $login, $pass1, $pass2;
    $key = key_session();
    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $hash = hash_hmac('sha1', $key.$remote_addr, $pass1);
    if (($_POST['key'] === $hash) and (hash_hmac('sha1', $_POST['password'], $login) === $pass2)) {
        //unset($_SESSION['key']);
        key_session(true);
        $_SESSION['remote_addr'] = $remote_addr;
        header('Location: auth.php?mode=login');
    } else {
        $key = key_session(true);
        echo <<<EOF
<script type="text/javascript" src="sha1.js"></script>
<script type="text/javascript" src="jquery-1.4.2.min.js"></script>
<form name="auth" action="auth.php" method="post">
<input type="hidden" name="remote_addr" value="$remote_addr">
<input type="hidden" name="key" value="$key">
<input type="hidden" name="login" value="$login">
$login:
<input type="password" name="password" value="">
<input type="submit">
</form>
<script>
var key = $('input[name=key]').val();
var remote_addr = $('input[name=remote_addr]').val();
$('form[name=auth]').submit(function() {
    var login = $('input[name=login]').val();
    var password = $('input[name=password]').val();
    var key_hash = hex_hmac_sha1(hex_hmac_sha1(login, password), key+remote_addr);
    $('input[name=key]').val(key_hash);
    var pass_hash = hex_hmac_sha1(password, login);
    $('input[name=password]').val(pass_hash);
});
$('input[name=password]').focus();
</script>
EOF;
    }
}

session_start();
if (isset($_SESSION['remote_addr']) and ($_SESSION['remote_addr'] === $_SERVER['REMOTE_ADDR'])) {
    if ($_GET['mode'] === 'logout') {
        unset($_SESSION['remote_addr']);
        auth();
    } else {
        print_r($_SESSION);
        echo '<a href="?mode=logout">Exit</a>';
    }
} else {
    auth();
}
