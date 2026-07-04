<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

json_response(['user' => $_SESSION['user'] ?? null]);
