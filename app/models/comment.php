<?php

class Comment
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        // De databaseverbinding komt uit Database.php.
        $this->connection = $connection;
    }

    public function create(int $userId, int $videoId, string $content): void
    {
        // Nieuwe comment opslaan bij de gekozen video.
        $statement = $this->connection->prepare(
            'INSERT INTO `comment` (user_id, video_id, content, created_at)
             VALUES (:user_id, :video_id, :content, NOW())'
        );

        $statement->execute([
            'user_id' => $userId,
            'video_id' => $videoId,
            'content' => $content,
        ]);
    }

    public function getForVideo(int $videoId)
    {
        // Alle comments met de eigenaar ophalen voor onder de video.
        $statement = $this->connection->prepare(
            'SELECT `comment`.id, `comment`.content, `comment`.created_at, `user`.email
             FROM `comment`
             INNER JOIN `user` ON `user`.id = `comment`.user_id
             WHERE `comment`.video_id = :video_id
             ORDER BY `comment`.created_at DESC'
        );
        $statement->execute(['video_id' => $videoId]);

        return $statement->fetchAll();
    }
}
