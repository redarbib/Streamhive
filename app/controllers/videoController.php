<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/video.php';

class VideoController
{
    private Video $videoModel;

    public function __construct()
    {
        // Maak het videomodel klaar voor databaseacties.
        $database = new Database();
        $connection = $database->connect();
        $this->videoModel = new Video($connection);
    }

    public function handle()
    {
        // Uploads mogen alleen via een POST-formulier binnenkomen.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToUpload();
        }

        // Lees de titel, beschrijving en het videobestand uit het formulier.
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $video = $_FILES['video'] ?? null;

        $this->validateUpload($title, $video);

        $fileName = $this->storeUploadedVideo($video);

        // Sla de videogegevens op nadat het bestand succesvol is verplaatst.
        $this->videoModel->create((int) $_SESSION['user_id'], $title, $description, $fileName);

        $_SESSION['upload_success'] = 'Video uploaded successfully.';
        $this->redirectToUpload();
    }

    private function validateUpload(string $title, ?array $video)
    {
        // Controleer of de verplichte uploadgegevens aanwezig zijn.
        if ($title === '' || !$video) {
            $this->failUpload('Please fill in all upload fields.');
        }

        if ($video['error'] !== UPLOAD_ERR_OK) {
            $this->failUpload('The video could not be uploaded.');
        }

        if (!$this->isAllowedVideoType($video['tmp_name'])) {
            $this->failUpload('Please upload a valid video file.');
        }
    }

    private function isAllowedVideoType(string $temporaryPath)
    {
        // Alleen gangbare videotypes toestaan.
        $allowedTypes = [
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime',
            'video/x-msvideo',
        ];

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $temporaryPath);
        finfo_close($fileInfo);

        // Vergelijk het echte MIME-type van het bestand met de toegestane lijst.
        return in_array($mimeType, $allowedTypes, true);
    }

    private function storeUploadedVideo(array $video)
    {
        // Geef elk uploadbestand een unieke naam om overschrijven te voorkomen.
        $uploadDir = __DIR__ . '/../../uploads/videos';
        $extension = pathinfo($video['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('video_', true) . '.' . $extension;
        $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($video['tmp_name'], $targetPath)) {
            $this->failUpload('The video could not be saved.');
        }

        return $fileName;
    }

    private function failUpload(string $message)
    {
        $_SESSION['upload_error'] = $message;
        $this->redirectToUpload();
    }

    private function redirectToUpload()
    {
        header('Location: ../../views/Upload.php');
        exit;
    }
}

(new VideoController())->handle();
