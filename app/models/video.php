<?php

class Video
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        // De databaseverbinding komt uit Database.php.
        $this->connection = $connection;
    }

    public function create(int $userId, string $title, string $description, string $filename)
    {
        // Nieuwe video opslaan na een succesvolle upload.
        $statement = $this->connection->prepare(
            'INSERT INTO video (user_id, title, description, filename, views, created_at)
             VALUES (:user_id, :title, :description, :filename, 0, NOW())'
        );

        $statement->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'filename' => $filename,
        ]);
    }

    public function getAll()
    {
        // Nieuwste videos eerst tonen op de homepagina.
        $statement = $this->connection->query(
            'SELECT id, user_id, title, description, filename, views, created_at
             FROM video
             ORDER BY created_at DESC'
        );

        return $statement->fetchAll();
    }

    public function search(string $search)
    {
        // Zoek videos op titel of beschrijving.
        $statement = $this->connection->prepare(
            'SELECT id, user_id, title, description, filename, views, created_at
             FROM video
             WHERE title LIKE :title_search
             OR description LIKE :description_search
             ORDER BY created_at DESC'
        );
        $statement->execute([
            'title_search' => '%' . $search . '%',
            'description_search' => '%' . $search . '%',
        ]);

        return $statement->fetchAll();
    }

    public function findById(int $id)
    {
        // Een video ophalen voor de pagina.
        $statement = $this->connection->prepare(
            'SELECT id, user_id, title, description, filename, views, created_at
             FROM video
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $video = $statement->fetch();

        return $video ?: null;
    }

    public function incrementViews(int $id)
    {
        // Verhoog de teller wanneer iemand de video bekijkt.
        $statement = $this->connection->prepare(
            'UPDATE video
             SET views = views + 1
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }
}
