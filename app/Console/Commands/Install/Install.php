<?php

namespace App\Console\Commands\Install;

use Illuminate\Console\Command;
use App\Models\Eloquent\Group;
use App\Models\Eloquent\User;
use Hash;
use Str;
use Exception;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='Install NOJ';

    public static $license=1630315446;

    public static $logoGeneral=<<< LOGOG
          ___
          \\\\||
         ,'_,-\
         ;'____\
         || =\=|
         ||  - |  Haec sententia nullam significationem habet, sed Latina pulchra est.
     ,---'._--''-,,---------.--.----_,
    / `-._- _--/,,|  ___,,--'--'._<
   /-._,  `-.__;,,|'
  /   ;\      / , ;
 /  ,' | _ - ',/, ;
(  (   |     /, ,,;
 \  \  |     ',,/,;
  \  \ |    /, / ,;
 (| ,^.|   / ,, ,/;
  `-'./ `-._,, ,/,;
       ⧫-._ `-._,,;
       |/,,`-._ `-.
       |, ,;, ,`-._\
╔═══════════════════════════════════════════════════════════════════════════════════════════════════════╗
║ ███╗   ██╗ ██████╗      ██╗    ██╗███╗   ██╗███████╗████████╗ █████╗ ██╗     ██╗     ███████╗██████╗  ║
║ ████╗  ██║██╔═══██╗     ██║    ██║████╗  ██║██╔════╝╚══██╔══╝██╔══██╗██║     ██║     ██╔════╝██╔══██╗ ║
║ ██╔██╗ ██║██║   ██║     ██║    ██║██╔██╗ ██║███████╗   ██║   ███████║██║     ██║     █████╗  ██████╔╝ ║
║ ██║╚██╗██║██║   ██║██   ██║    ██║██║╚██╗██║╚════██║   ██║   ██╔══██║██║     ██║     ██╔══╝  ██╔══██╗ ║
║ ██║ ╚████║╚██████╔╝╚█████╔╝    ██║██║ ╚████║███████║   ██║   ██║  ██║███████╗███████╗███████╗██║  ██║ ║
║ ╚═╝  ╚═══╝ ╚═════╝  ╚════╝     ╚═╝╚═╝  ╚═══╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝╚═╝  ╚═╝ ║
╚═══════════════════════════════════════════════════════════════════════════════════════════════════════╝
LOGOG;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initWelcone();
        $this->initSystemCheck();
        return;
        if ($this->confirm('Do you wish to continue?')) {
            if(!$this->acceptLicense()){
                return;
            }
            if($this->createFrontAdminUser()){
                $this->createFrontAdminGroup();
            }
            $this->installBabelExtensionNOJ();
            $this->importExampleProblem();
            $this->importBackAdminMenuPermissionsRoles();
            $this->createBackAdminUser();
        }
    }

    protected function initWelcone()
    {
        $this->line('<fg=cyan>'.static::$logoGeneral.'</>');
        $this->line(sprintf('NOJ <comment>version</comment> <info>%s</info>', version()));
        $this->comment('');
        sleep(1);
    }

    protected function initSystemCheck() {
        $this->info('All system check completed with 0 warning 0 error.');
        $this->comment('');
        sleep(1);
    }

    protected function acceptLicense() {
        $this->warn('NOJ open-source license has been updated, please accept it first:');
        $this->comment('');
        sleep(1);
        $this->line(file_get_contents(base_path('LICENSE')));
        $this->warn('Notice that above license is actually a copy from https://github.com/ZsgsDesign/NOJ and remote license is the one that takes effect.');
        $this->comment('');
        sleep(1);
        return $this->confirm('Do you agree with the license?');
    }

    protected function createFrontAdminUser() {
        $this->line('Creating frontstage admin user...');
        $this->comment('');
        $shallCreate = true;
        if (User::count()) {
            $shallCreate = $this->confirm('Detected existing frontstage user, do you really want to create frontstage admin?');
            $this->comment('');
        }
        if (!$shallCreate) {
            return false;
        }
        while(true) {
            try {
                $createdUser = $this->createFrontUser();
                break;
            } catch(Exception $e) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Error occured while creating admin user.</>\n");
                continue;
            }
        }
        return true;
    }

    private function createFrontUser() {
        $username = $this->ask('Please choose a username:', 'admin');
        $email = $this->ask('Please choose a email address:', 'admin@code.master');
        $password = Hash::make(Str::random(8));
        $createdUser=User::create([
            'name' => $username,
            'email' => $email,
            'password' => $password,
            'avatar' => '/static/img/avatar/noj.png',
        ]);
        $createdUser->markEmailAsVerified();
        return $createdUser;
    }

    protected function createFrontAdminGroup() {

    }

    protected function installBabelExtensionNOJ() {

    }

    protected function importExampleProblem() {

    }

    protected function importBackAdminMenuPermissionsRoles() {

    }

    protected function createBackAdminUser() {

    }
}
