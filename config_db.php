<?php
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        return null;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignore comments
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '"'); // Remove quotes if any

        putenv("$key=$value");
    }
}

?>
