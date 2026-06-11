<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/like.php';

class LikeController
{
    private Like $likeModel;

    public function __construct()
    {
        // Maak het likemodel klaar voor databaseacties.
        $database = new Database();
        $connection = $database->connect();
        $this->likeModel = new Like($connection);
    }

    public function handle()
    {
        // Likes mogen alleen via een POST-formulier aangepast worden.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('../../index.php');
        }

        // Bepaal op welke video de likeactie uitgevoerd wordt.
        $videoId = (int) ($_POST['video_id'] ?? 0);

        // Zonder geldige video wordt de gebruiker teruggestuurd naar de homepagina.
        if ($videoId <= 0) {
            $this->redirectTo('../../index.php');
        }

        // Voeg een like toe of verwijder de bestaande like van deze gebruiker.
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
