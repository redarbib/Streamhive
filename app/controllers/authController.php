<?php
session_start();

require_once __DIR__ . '/../../core/Database.php';

function redirectTo(string $path): void
{
    // Stuur de gebruiker door naar een andere pagina.
    header("Location: {$path}");
    exit;
}

function failLogin(string $message): void
{
    // Login fout opslaan zodat login.php die kan tonen.
    $_SESSION['login_error'] = $message;
    redirectTo('../../views/login.php');
}

function failRegister(string $message): void
{
    // Registratie fout opslaan zodat register.php die kan tonen.
    $_SESSION['register_error'] = $message;
    redirectTo('../../views/register.php');
}

// De controller mag alleen formulierdata via POST verwerken.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectTo('../../views/login.php');
}

// Bepaal of het formulier login of register is.
$action = $_POST['action'];
$login = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $database = new Database();
    $connection = $database->connect();

    if ($action === 'register') {
        // Velden uit het registratieformulier ophalen.
        $email = trim($_POST['email'] ?? '');
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($email === '' || $password === '' || $confirmPassword === '') {
            failRegister('Please fill in all fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            failRegister('Please enter a valid email address.');
        }

        if (strlen($password) < 6) {
            failRegister('Password must be at least 6 characters.');
        }

        if ($password !== $confirmPassword) {
            failRegister('Passwords do not match.');
        }

        // Controleer of er al een gebruiker met dit emailadres bestaat.
        $statement = $connection->prepare(
            'SELECT id
             FROM user
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        if ($statement->fetch()) {
            failRegister('An account with this email already exists.');
        }

        // Wachtwoord hashen voordat het in de database komt.
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $statement = $connection->prepare(
            'INSERT INTO user (email, password, role)
             VALUES (:email, :password, :role)'
        );
        $statement->execute([
            'email' => $email,
            'password' => $passwordHash,
            'role' => 'user',
        ]);

        $_SESSION['login_success'] = 'Account created. You can sign in now.';
        redirectTo('../../views/login.php');
    }

    // Vanaf hier wordt de normale login afgehandeld.
    if ($login === '' || $password === '') {
        failLogin('Please fill in both fields.');
    }

    // Zoek de gebruiker op met het ingevulde emailadres.
    $statement = $connection->prepare(
        'SELECT id, email, password, role
         FROM user
         WHERE email = :login
         LIMIT 1'
    );
    $statement->execute(['login' => $login]);

    $user = $statement->fetch();

    // Controleer het ingevulde wachtwoord met de hash uit de database.
    $passwordMatches = $user && (
        password_verify($password, $user['password'])
        || hash_equals($user['password'], $password)
    );

    if (!$passwordMatches) {
        failLogin('Invalid email or password.');
    }

    // Nieuwe sessie id voor extra veiligheid na succesvol inloggen.
    session_regenerate_id(true);

    // Gebruikersgegevens bewaren in de sessie.
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;

    redirectTo('../../index.php');
} catch (PDOException $exception) {
    // Algemene foutmelding tonen zonder database details te lekken.
    if ($action === 'register') {
        failRegister('Something went wrong. Please try again.');
    }
    failLogin('Something went wrong. Please try again.');
}
