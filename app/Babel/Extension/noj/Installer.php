<?php
namespace App\Babel\Extension\noj;

use App\Babel\Install\InstallerBase;
use Exception;

class Installer extends InstallerBase
{
    public function install()
    {
        // throw new Exception("No Install Method Provided");
        // file_get_contents('CompilerConfig/1563011654.json');
        $this->commitCompiler();
        $this->applyIcon();
    }

    public function uninstall()
    {
        throw new Exception("No Uninstall Method Provided");
    }
}
