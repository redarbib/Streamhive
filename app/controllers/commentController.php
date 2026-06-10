<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/comment.php';

class CommentController
{
    private Comment $commentModel;

    public function __construct()
    {
        $database = new Database();
        $connection = $database->connect();
        $this->commentModel = new Comment($connection);
    }

    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('../../index.php');
        }

        $videoId = (int) ($_POST['video_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($videoId <= 0) {
            $this->redirectTo('../../index.php');
        }

        if ($content === '') {
            $_SESSION['comment_error'] = 'Please write a comment first.';
            $this->backToVideo($videoId);
        }

        $this->commentModel->create((int) $_SESSION['user_id'], $videoId, $content);
        $this->backToVideo($videoId);
    }

    private function backToVideo(int $videoId)
    {
        $this->redirectTo('../../views/video.php?id=' . $videoId);
    }

    private function redirectTo(string $path)
    {
        header('Location: ' . $path);
        exit;
    }
}

(new CommentController())->handle();
