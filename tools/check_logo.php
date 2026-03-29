<?php

$logoRelative = getenv('COMPANY_LOGO') ?: 'logo.png';
$logoRelative = ltrim($logoRelative, "\\/\t\n\r\0\x0B");
$logoPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $logoRelative;
$logoPath = realpath($logoPath) ?: $logoPath;

echo "COMPANY_LOGO={$logoRelative}\n";
echo "logoPath={$logoPath}\n";
echo 'is_file=' . (is_file($logoPath) ? '1' : '0') . "\n";
echo 'readable=' . (is_readable($logoPath) ? '1' : '0') . "\n";
echo 'filesize=' . (string) (@filesize($logoPath) ?: 0) . "\n";

$img = @getimagesize($logoPath);
echo 'getimagesize=' . (is_array($img) ? ($img[0] . 'x' . $img[1] . ' ' . ($img['mime'] ?? '')) : 'false') . "\n";

echo 'mime_content_type=' . (string) (@mime_content_type($logoPath) ?: '') . "\n";

$contents = @file_get_contents($logoPath);
echo 'file_get_contents=' . (($contents === false) ? 'false' : ('ok(' . strlen($contents) . ')')) . "\n";

if (is_string($contents) && $contents !== '') {
    $mime = @mime_content_type($logoPath) ?: 'image/png';
    $dataUri = 'data:' . $mime . ';base64,' . base64_encode($contents);
    echo 'dataUri_prefix=' . substr($dataUri, 0, 40) . "...\n";
    echo 'dataUri_len=' . strlen($dataUri) . "\n";
}
