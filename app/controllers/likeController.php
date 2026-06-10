<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/like.php';

class LikeController
{
    private Like $likeModel;

    public function __construct()
    {
        $database = new Database();
        $connection = $database->connect();
        $this->likeModel = new Like($connection);
    }

    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('../../index.php');
        }

        $videoId = (int) ($_POST['video_id'] ?? 0);

        if ($videoId <= 0) {
            $this->redirectTo('../../index.php');
        }

        $this->likeModel->toggleVideoLike((int) $_SESSION['user_id'], $videoId);
        $this->redirectTo('../../views/video.php?id=' . $videoId);
    }

    private function redirectTo(string $path)
    {
        header('Location: ' . $path);
        exit;
    }
}

(new LikeController())->handle();
