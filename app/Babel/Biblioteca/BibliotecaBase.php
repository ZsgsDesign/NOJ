<?php

namespace App\Babel\Biblioteca;

class BibliotecaBase
{
    protected $command = null;
    protected $bibliotecaUrl = null;

    public function __construct()
    {
        $this->bibliotecaUrl = config('biblioteca.mirror');
    }

    public function importCommandLine($commandTemp)
    {
        $this->command = $commandTemp;
    }

    protected function line($line)
    {
        if (is_null($this->command)) {
            echo $line;
        } else {
            $this->command->line($line);
        }
    }
}
