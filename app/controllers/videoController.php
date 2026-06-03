<?php
session_start();

function redirectToUpload(): void
{
    header('Location: ../../views/Upload.php');
    exit;
}

if (empty($_SESSION['logged_in'])) {
    header('Location: ../../views/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToUpload();
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$video = $_FILES['video'] ?? null;

if ($title === '' || $category === '' || !$video) {
    $_SESSION['upload_error'] = 'Please fill in all upload fields.';
    redirectToUpload();
}

if ($video['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['upload_error'] = 'The video could not be uploaded.';
    redirectToUpload();
}

$allowedTypes = [
    'video/mp4',
    'video/webm',
    'video/ogg',
    'video/quicktime',
    'video/x-msvideo',
];

$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($fileInfo, $video['tmp_name']);
finfo_close($fileInfo);

if (!in_array($mimeType, $allowedTypes, true)) {
    $_SESSION['upload_error'] = 'Please upload a valid video file.';
    redirectToUpload();
}

$uploadDir = __DIR__ . '/../../uploads/videos';

$extension = pathinfo($video['name'], PATHINFO_EXTENSION);
$safeExtension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
$fileName = uniqid('video_', true) . ($safeExtension !== '' ? ".{$safeExtension}" : '');
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

if (!move_uploaded_file($video['tmp_name'], $targetPath)) {
    $_SESSION['upload_error'] = 'The video could not be saved.';
    redirectToUpload();
}

$metadataPath = $uploadDir . DIRECTORY_SEPARATOR . 'videos.json';
$videos = [];

if (is_file($metadataPath)) {
    $json = file_get_contents($metadataPath);
    $videos = json_decode($json, true);

    if (!is_array($videos)) {
        $videos = [];
    }
}

$videos[] = [
    'title' => $title,
    'description' => $description,
    'category' => $category,
    'file' => $fileName,
    'uploaded_at' => date('Y-m-d H:i:s'),
    'user_id' => $_SESSION['user_id'] ?? null,
];

file_put_contents($metadataPath, json_encode($videos, JSON_PRETTY_PRINT));

$_SESSION['upload_success'] = 'Video uploaded successfully.';
redirectToUpload();
