<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/comment.php';

class CommentController
{
    private Comment $commentModel;

    public function __construct()
    {
        // Maak het commentmodel klaar voor databaseacties.
        $database = new Database();
        $connection = $database->connect();
        $this->commentModel = new Comment($connection);
    }

    public function handle()
    {
        // Comments mogen alleen via een POST-formulier geplaatst worden.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('../../index.php');
        }

        // Haal de video en de tekst van de comment uit het formulier.
        $videoId = (int) ($_POST['video_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        // Zonder geldige video kan de comment nergens aan gekoppeld worden.
        if ($videoId <= 0) {
            $this->redirectTo('../../index.php');
        }

        // Lege comments worden teruggestuurd naar dezelfde videopagina.
        if ($content === '') {
            $_SESSION['comment_error'] = 'Please write a comment first.';
            $this->backToVideo($videoId);
        }

        // Sla de comment op bij de ingelogde gebruiker en de gekozen video.
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
