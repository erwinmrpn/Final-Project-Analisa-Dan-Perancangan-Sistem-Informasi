<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

$_SESSION = [];
session_destroy();

json_response(['ok' => true]);
