<?php
session_start();
unset($_SESSION['client_id'], $_SESSION['client_prenom'], $_SESSION['client_email']);
header('Location: index.html');
exit;
?>