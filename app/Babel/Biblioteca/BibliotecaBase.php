<?php

namespace App\Babel\Biblioteca;

class BibliotecaBase
{
    protected $command = null;
    protected $bibliotecaUrl = "https://ghproxy.com/https://raw.githubusercontent.com/NJUPTAAA/biblioteca/gh-pages/"; # https://njuptaaa.github.io/biblioteca/

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
