<?php
session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/like.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

$videoId = (int) ($_POST['video_id'] ?? 0);


$database = new Database();
$connection = $database->connect();
$likeModel = new Like($connection);

// Like aanzetten of weghalen voor deze gebruiker.
$likeModel->toggleVideoLike((int) $_SESSION['user_id'], $videoId);

header('Location: ../../views/video.php?id=' . $videoId);
exit;
