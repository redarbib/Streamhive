<?php

class Like
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        // De databaseverbinding komt uit Database.php.
        $this->connection = $connection;
    }

    public function countForVideo(int $videoId): int
    {
        // Tel alle likes die bij deze video horen.
        $statement = $this->connection->prepare(
            'SELECT COUNT(*) AS total
             FROM likes
             WHERE video_id = :video_id
             AND comment_id IS NULL'
        );
        $statement->execute(['video_id' => $videoId]);

        return (int) $statement->fetch()['total'];
    }

    public function userLikedVideo(int $userId, int $videoId): bool
    {
        // Check of deze gebruiker deze video al heeft geliket.
        $statement = $this->connection->prepare(
            'SELECT id
             FROM likes
             WHERE user_id = :user_id
             AND video_id = :video_id
             AND comment_id IS NULL
             LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'video_id' => $videoId,
        ]);

        return (bool) $statement->fetch();
    }

    public function toggleVideoLike(int $userId, int $videoId)
    {
        // Als de like al bestaat verwijderen we hem, anders maken we hem aan.
        if ($this->userLikedVideo($userId, $videoId)) {
            $statement = $this->connection->prepare(
                'DELETE FROM likes
                 WHERE user_id = :user_id
                 AND video_id = :video_id
                 AND comment_id IS NULL'
            );
        } else {
            $statement = $this->connection->prepare(
                'INSERT INTO likes (user_id, video_id, comment_id)
                 VALUES (:user_id, :video_id, NULL)'
            );
        }

        $statement->execute([
            'user_id' => $userId,
            'video_id' => $videoId,
        ]);
    }
}
