<?php
session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/video.php';

function redirectToUpload(): void
{
    // Na upload of fout terug naar de uploadpagina.
    header('Location: ../../views/Upload.php');
    exit;
}

// Alleen ingelogde gebruikers mogen uploaden.
if (empty($_SESSION['logged_in'])) {
    header('Location: ../../views/login.php');
    exit;
}

// Direct openen van deze controller is niet de bedoeling.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToUpload();
}

// Lees de velden uit het uploadformulier.
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$video = $_FILES['video'] ?? null;

if ($title === '' || !$video) {
    $_SESSION['upload_error'] = 'Please fill in all upload fields.';
    redirectToUpload();
}

if ($video['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['upload_error'] = 'The video could not be uploaded.';
    redirectToUpload();
}

// Toegestane video bestandsformaten.
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

// Controleer of het bestand echt een video is.
if (!in_array($mimeType, $allowedTypes, true)) {
    $_SESSION['upload_error'] = 'Please upload a valid video file.';
    redirectToUpload();
}

$uploadDir = __DIR__ . '/../../uploads/videos';

// Maak een unieke bestandsnaam zodat uploads elkaar niet overschrijven.
$extension = pathinfo($video['name'], PATHINFO_EXTENSION);
$fileName = uniqid('video_', true) . '.' . $extension;
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

// Verplaats het uploadbestand naar de uploads map.
if (!move_uploaded_file($video['tmp_name'], $targetPath)) {
    $_SESSION['upload_error'] = 'The video could not be saved.';
    redirectToUpload();
}

$database = new Database();
$connection = $database->connect();
$videoModel = new Video($connection);

// Sla de videogegevens op in de database.
$videoModel->create((int) $_SESSION['user_id'], $title, $description, $fileName);

$_SESSION['upload_success'] = 'Video uploaded successfully.';
redirectToUpload();
