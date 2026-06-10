<?php

session_start();

require_once __DIR__ . '/../../core/Database.php';

class AuthController
{
    private PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }

    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('../../views/login.php');
        }

        $action = $_POST['action'] ?? 'login';

        try {
            if ($action === 'register') {
                $this->register();
                return;
            }

            $this->login();
        } catch (PDOException $exception) {
            if ($action === 'register') {
                $this->failRegister('Something went wrong. Please try again.');
            }

            $this->failLogin('Something went wrong. Please try again.');
        }
    }

    private function register()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($email === '' || $password === '' || $confirmPassword === '') {
            $this->failRegister('Please fill in all fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->failRegister('Please enter a valid email address.');
        }

        if (strlen($password) < 6) {
            $this->failRegister('Password must be at least 6 characters.');
        }

        if ($password !== $confirmPassword) {
            $this->failRegister('Passwords do not match.');
        }

        if ($this->emailExists($email)) {
            $this->failRegister('An account with this email already exists.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $statement = $this->connection->prepare(
            'INSERT INTO user (email, password, role)
             VALUES (:email, :password, :role)'
        );
        $statement->execute([
            'email' => $email,
            'password' => $passwordHash,
            'role' => 'user',
        ]);

        $_SESSION['login_success'] = 'Account created. You can sign in now.';
        $this->redirectTo('../../views/login.php');
    }

    private function login()
    {
        $login = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($login === '' || $password === '') {
            $this->failLogin('Please fill in both fields.');
        }

        $user = $this->findUserByEmail($login);

        if (!$user || !$this->passwordMatches($password, $user['password'])) {
            $this->failLogin('Invalid email or password.');
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        $this->redirectTo('../../index.php');
    }

    private function emailExists(string $email)
    {
        $statement = $this->connection->prepare(
            'SELECT id
             FROM user
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        return (bool) $statement->fetch();
    }

    private function findUserByEmail(string $email)
    {
        $statement = $this->connection->prepare(
            'SELECT id, email, password, role
             FROM user
             WHERE email = :login
             LIMIT 1'
        );
        $statement->execute(['login' => $email]);

        $user = $statement->fetch();

        return $user ?: null;
    }

    private function passwordMatches(string $password, string $storedPassword)
    {
        return password_verify($password, $storedPassword)
            || hash_equals($storedPassword, $password);
    }

    private function failLogin(string $message)
    {
        $_SESSION['login_error'] = $message;
        $this->redirectTo('../../views/login.php');
    }

    private function failRegister(string $message)
    {
        $_SESSION['register_error'] = $message;
        $this->redirectTo('../../views/register.php');
    }

    private function redirectTo(string $path)
    {
        header("Location: {$path}");
        exit;
    }
}

(new AuthController())->handle();
