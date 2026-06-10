<?php

session_start();

class LogoutController
{
    public function handle()
    {
        $this->logout();
        $this->redirectTo('../../views/login.php');
    }

    private function logout()
    {
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
