<?php
session_start();
session_destroy();
// Redirect to login in current root since panel-level paths use relative includes
header('Location: login');
exit();
?>