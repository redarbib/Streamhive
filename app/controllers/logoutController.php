<?php

session_start();

class LogoutController
{
    public function handle()
    {
        // Log de gebruiker uit en stuur daarna terug naar het loginscherm.
        $this->logout();
        $this->redirectTo('../../views/login.php');
    }

    private function logout()
    {
        // Leeg de sessie zodat de gebruiker niet meer ingelogd is.
        $_SESSION = [];
        session_destroy();
    }

    private function redirectTo(string $path)
    {
        header('Location: ' . $path);
        exit;
    }
}

(new LogoutController())->handle();
