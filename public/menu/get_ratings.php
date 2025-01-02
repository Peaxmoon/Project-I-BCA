<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

$menu_item_id = isset($_GET['menu_item_id']) ? (int) 