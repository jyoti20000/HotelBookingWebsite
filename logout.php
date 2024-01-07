<?php
require('admin/include_1/essentials.php');

session_start();
session_destroy();
redirect('index.php');
?>