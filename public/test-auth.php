<?php
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "User: " . ($_SESSION['username'] ?? 'Guest') . "<br>";
echo "Role: " . ($_SESSION['role'] ?? 'None') . "<br>";
echo "Last Activity: " . date('H:i:s', $_SESSION['last_activity'] ?? 0) . "<br>";
?>
