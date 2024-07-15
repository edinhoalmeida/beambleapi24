<?php
// WHkPfpTCUgtN.php
if(empty($_POST)){
    $_POST = [];
}
// if ( hash_equals('sha1=' . hash_hmac('sha1', $_POST, 'cBzBpvK7XafExpx2VgMdAKxmKD8WkmZY'), 
//                  $_SERVER['HTTP_X_HUB_SIGNATURE'] ) )
if(true)
{
    $receive = print_r($_POST, true);
    file_put_contents( dirname(__DIR__) . '/uploads/wh-github.txt', "1");
    $receive = print_r($_SERVER, true);
    file_put_contents( dirname(__DIR__) . '/uploads/wh-github-headers.txt', $receive);
    //  `/home/bitnami/REPOS/beamble24-pull.sh`;
    //  `/home/bitnami/REPOS/beamble-dev24-pull.sh`;
    echo 1;
    exit;
} else {
    $receive = print_r($_POST, true);
    file_put_contents( dirname(__DIR__) . '/uploads/wh-github-error.txt', $receive);
    echo 0;
    exit;

}
?>