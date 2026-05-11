<?php
require_once __DIR__ . '/CONTROLLER/EmploiController.php';
require_once __DIR__ . '/VIEW/shared/theme.php';

$host = $_SERVER['HTTP_HOST'] ?? 'N/A';
$addr = $_SERVER['SERVER_ADDR'] ?? 'N/A';
$path = theme_url('test.php');

echo "HTTP_HOST: " . $host . "<br>";
echo "SERVER_ADDR: " . $addr . "<br>";
echo "Theme URL: " . $path . "<br>";

function get_lan_ip() {
    $output = shell_exec('ipconfig');
    if (preg_match_all('/IPv4[^\r\n:]*[.: ]+([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/i', $output, $matches)) {
        return $matches[1];
    }
    return 'None';
}

echo "LAN IPs: " . implode(', ', (array)get_lan_ip()) . "<br>";
?>
