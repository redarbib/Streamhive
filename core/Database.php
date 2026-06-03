<?php

class Database
{
    // Database instellingen voor de lokale XAMPP server.
    private string $host = 'localhost';
    private string $database = 'streamhive';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $connection = null;

    public function connect(): PDO
    {
        // Maak maar een keer verbinding en hergebruik die daarna.
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

            // PDO zorgt voor veilige database queries met prepared statements.
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return $this->connection;
    }
}
