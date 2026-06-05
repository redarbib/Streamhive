<?php
session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/comment.php';

function backToVideo(int $videoId)
{
    // Terug naar dezelfde video na het plaatsen van een comment.
    header('Location: ../../views/video.php?id=' . $videoId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

$videoId = (int) ($_POST['video_id']);
$content = trim($_POST['content']);


$database = new Database();
$connection = $database->connect();
$commentModel = new Comment($connection);

// Comment opslaan voor de ingelogde gebruiker.
$commentModel->create((int) $_SESSION['user_id'], $videoId, $content);

backToVideo($videoId);
