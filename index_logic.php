<?php

function logout() {
    unset($_SESSION['admin_token']);
    header("Location: /");
    exit;
}

if ($_POST){
    if (isset($_POST['create'])){
        header('location: newtest.php');
        exit;
    }
    if (isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['admin_auth'])){
        if($_POST['adm_login']===$admin_login && sha1($_POST['adm_password'])===$admin_password) {
            $_SESSION['admin_token'] = sha1(microtime(true));
        }
    }
    if(isset($_POST['start_test'])){
        $_SESSION['test'] = $_POST['filename'];
        header('Location: test.php');
        exit;
    }
    if(isset($_POST['delete_test'])){
        $title = $_POST['delete_test'];
        $now = date('Y-m-d H:i:s');
        $sql=$db->prepare('update tests set deleted = :now where title = :title;');
        $sql->execute(['title' => $title, 'now' => $now]);
        rename($testDir.$title.'.json', $deletedTestDir.$now.'.json');
        rename($answerDir.$title.'.json', $deletedAnswerDir.$now.'.json');
    }
}

$isAdmin = isset($_SESSION['admin_token']);

$sql = $db->prepare('SELECT title, description, created, deleted FROM tests;');
$sql->execute();
$tests = $sql->fetchall();
