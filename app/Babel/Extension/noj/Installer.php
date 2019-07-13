<?php
namespace App\Babel\Extension\noj;

use App\Babel\Install\InstallerBase;

class Installer extends InstallerBase
{
    public function install()
    {
        throw new Exception([
            "level"=>"critical",
            "info"=>"No Install Method Provided"
        ]);
    }

    public function uninstall()
    {
        throw new Exception([
            "level"=>"critical",
            "info"=>"No Uninstall Method Provided"
        ]);
    }
}
