<?php
session_start();

function triggerAttack($name) {
    $_SESSION['attack'] = $name;
}
?>