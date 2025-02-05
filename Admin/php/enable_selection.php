<?php
session_start();
require_once '../../php/db.php';

$edit = isset($_POST['edit']) ? $_POST['edit'] : false;
$sql = "UPDATE admin_settings SET select_enabled = $edit WHERE id = 1";
$conn->query($sql);

$sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'UPDATE', 'admin_settings', NULL, 'Changed volunteer selection status to $edit')";
$conn->query($sql);

header('Location: ../admin_logs.php?for=delActive');

?>
