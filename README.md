# NOJ (CodeMaster)

![NOJ](/noj.png)

NOJ's another online judge platform, stands for NJUPT Online Judge. It's written in PHP, GO, Python and other function-supporting languages.

[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster?ref=badge_shield)

## Installation

CentOS will be recommended for hosting NOJ, but all major operating systems are theoretically supported.

Till now, NOJ have been successfully deployed to the following systems:

- Ubuntu 16.04.3 LTS
- CentOS Linux release 7.6.1810 (Core)
- Windows 10 Professional 10.0.17134 Build 17134

Here is detailed step about deploying NOJ:

1. You need to have a server and installed [PHP](http://php.net/downloads.php) and [Composer](https://getcomposer.org);

2. Clone NOJ to your website folder;

```
cd /path-to-noj/
git clone https://github.com/ZsgsDesign/CodeMaster ./
```

3. Change your website root to `public` folder and then, if there is a `open_basedir` restriction, remove it;

4. Now run the following commands at the root folder of NOJ;

```
composer install
```

5. Almost done, you still got to modify a few folders and give them permission to write;

```
chmod -R 775 storage/
chmod -R 775 bootstrap/
chmod -R 775 app/Http/Controllers/VirtualCrawler/
chmod -R 775 app/Http/Controllers/VirtualJudge/
```

6. OK, right now we still need to configure environment, a typical `.env` just like the `.env.example`, you simply need to type the following codes;

```
cp .env.example .env
vim .env
```

7. Now, we need to configure the database, thankfully Laravel have migration already;

8. Lastly, we need to configure the virtual judger and online judger;

```
crontab -e
* * * * * php /path-to-noj/artisan schedule:run
```

9. NOJ's up-and-running, enjoy!

## Progress

- [ ] Basic Home Page
- [ ] General
    - [X] Cron Support
    - [ ] Notification Support
        - [ ] Browser
        - [ ] Mail
    - [ ] System Version
    - [ ] System Bug Report
- [ ] User System
    - [X] User Login
    - [X] User Register
    - [ ] User Password Retrive
    - [ ] User Email Verify
    - [ ] DashBoard
        - [ ] Statistics
        - [ ] Activities
        - [ ] Profile
    - [ ] Settings
- [ ] Search System
    - [ ] Problem Search
    - [ ] Status Search
    - [ ] Group Search
    - [ ] Contest Search
    - [ ] OnmiSearch Support
- [ ] Problem System
    - [X] Problem List
    - [X] Problem Tag
    - [ ] Problem Filter
    - [X] Problem Details
    - [ ] Problem Solution
    - [ ] Problem Discussion
    - [ ] Problem Submit
        - [X] Problem Immersive Mode
        - [X] Problem Editor
        - [X] Problem Submit History
        - [X] Problem Compiler List
        - [X] Problem Status Bar
        - [X] Problem Virtual Judge
            - [X] Submit to VJ
                - [X] CodeForces
                - [ ] UVa
                - [ ] UVa Live
                - [ ] SPOJ
            - [X] Retrive Status
        - [ ] Problem Online Judge
            - [ ] Judge Server
            - [ ] Judger
            - [ ] Submit to OJ
            - [ ] Retrive Status
- [ ] Status System
    - [ ] Status List
    - [ ] Status Filter
    - [ ] Status Details
        - [ ] Syntax Highlight
        - [ ] Verdict
        - [ ] Code Download
        - [ ] Code Share
- [ ] Contest System
    - [X] Contest List
    - [ ] Contest Tag
    - [ ] Contest Filter
    - [ ] Contest Details
        - [ ] Contest Registration
        - [ ] Contest Temp Account
        - [X] Leader Board
        - [X] Contest CountDown
        - [X] Contest Problem List
        - [X] Contest Problem Details
        - [X] Contest Announcements
        - [ ] Contest Admin Portal
            - [ ] Account Generate
            - [ ] Judge Status
            - [ ] Issue Announcements
        - [ ] In-Contest Problem Switch
        - [ ] Problem Temp Block
    - [ ] Contest Ranking System
    - [ ] Contest Clone
    - [ ] Contest Replay
- [ ] Group System
    - [X] Group List
    - [X] Group Details
        - [X] Group Timeline
        - [ ] Group Member Management
            - [ ] Invite
            - [ ] Delete
            - [ ] Sub Group
        - [X] Group Profile
        - [X] Group General Info
        - [ ] Group Functions
            - [ ] Group Announcement
            - [ ] Group Posts
            - [ ] Group Contests
                - [X] Group-wide Contests
                - [ ] Site-wide Contests
            - [ ] Group Own ProblemSet
                - [ ] Add Problem


## Credit

[Laravel](https://github.com/laravel/laravel)

[Markdown](https://github.com/GrahamCampbell/Laravel-Markdown)

[Simple-HTML-Dom](https://github.com/Kub-AT/php-simple-html-dom-parser)

[JudgeServer](https://github.com/MarkLux/JudgeServer)

[HTML Purifier](https://github.com/mewebstudio/Purifier)


## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster?ref=badge_large)
