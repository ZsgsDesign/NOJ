# NOJ  - Automatic Algorithm Test Platform

![NOJ](/noj_banner.png)

NOJ's another online judge platform, stands for NJUPT Online Judge. It's written in PHP, GO, Python and other function-supporting languages and supports both online judges and virtual judges, we called it **mixed judge**.

[![Build Status](https://img.shields.io/scrutinizer/build/g/ZsgsDesign/NOJ.svg?style=flat-square)](https://scrutinizer-ci.com/g/ZsgsDesign/NOJ/build-status/master)
![License](https://img.shields.io/github/license/ZsgsDesign/NOJ.svg?style=flat-square)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/ZsgsDesign/NOJ.svg?style=flat-square)](https://scrutinizer-ci.com/g/ZsgsDesign/NOJ/?branch=master)
[![FOSSA Status](https://img.shields.io/badge/license%20scan-passing-green.svg?style=flat-square)](https://app.fossa.io/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster?ref=badge_shield)
![GitHub repo size](https://img.shields.io/github/repo-size/ZsgsDesign/NOJ.svg?style=flat-square)
![GitHub release](https://img.shields.io/github/release/zsgsdesign/noj.svg?style=flat-square)
![Stars](https://img.shields.io/github/stars/zsgsdesign/noj.svg?style=flat-square)
![Forks](https://img.shields.io/github/forks/zsgsdesign/noj.svg?style=flat-square)

## Built using Jet Brains Open Source License

<img style="width: 10rem;" src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.png" alt="JetBrains Logo (Main) logo.">

## v0.17.0 Characinae

For more information, please visit [v0.17.0 Characinae Release Log](https://github.com/ZsgsDesign/NOJ/releases/tag/0.17.0) page.

### Installation

:exclamation: | **If you are having trouble installing NOJ or requires additional help setting up, you can contact us via noj@njupt.edu.cn or start an issue.**
:-----------: | :----------------------------------------------------------------------------------------------------------------------------

CentOS 8 will be recommended for hosting NOJ, but all major operating systems are theoretically supported.

We recommend running NOJ on the following platforms:

- **Ubuntu 20.04** and above
- **CentOS Linux release 8.0** and above
- **Windows 10 Professional** (requires additional setup)

Your web browser should be one of the following:


| [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/chrome/chrome_48x48.png" alt="Chrome" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Chrome  |  [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/firefox/firefox_48x48.png" alt="Firefox" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Firefox  | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/main/src/archive/internet-explorer-tile_10-11/internet-explorer-tile_10-11_48x48.png" alt="IE" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br> Internet Explorer  |  [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/edge/edge_48x48.png" alt="Edge" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br> Edge  | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/opera/opera_48x48.png" alt="Opera" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Opera                  |       [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/safari/safari_48x48.png" alt="Safari" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Safari       |
|:---------:|:-----------:|:-------------:|:-----------------:|:----------------:|:----------------:|
|69 and above|62 and above|Not supported|69 and above|Not Supported|13.1 and above|

- **Chrome 69** and above
- **Edge 69** and above
- **Firefox 62** and above
- **Safari 13.1** and above

We do not provide any support for Opera, which doesn't mean Opera cannot access NOJ without error, it just means issues about compatibilities of Opera will not be fixed.

For installation options and troubleshooting tips, see [NOJ Documentation](https://njuptaaa.github.io/docs/).

### Supported Languages

NOJ now supports 15 popular programming languages, you can start issues about new languages support. 

|Language|Compile/Run Command|
|--------|-------------------|
|C|/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c99 {src_path} -lm -o {exe_path}|
|C11|/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c11 {src_path} -lm -o {exe_path}|
|C++|/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++11 {src_path} -lm -o {exe_path}|
|C++14|/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++14 {src_path} -lm -o {exe_path}|
|C++17|/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++17 {src_path} -lm -o {exe_path}|
|Java|/usr/bin/javac {src_path} -d {exe_dir} -encoding UTF8<br>/usr/bin/java -cp {exe_dir} -XX:MaxRAM={max_memory}k -Djava.security.manager -Dfile.encoding=UTF-8 -Djava.security.policy==/etc/java_policy -Djava.awt.headless=true Main|
|Python2|/usr/bin/python -m py_compile {src_path}<br>/usr/bin/python {exe_path}|
|Python3|/usr/bin/python3.7 -m py_compile {src_path}<br>/usr/bin/python3.7 {exe_path}|
|PHP7|/usr/bin/php {exe_path}|
|Javascript|/usr/bin/jsc {exe_path}|
|Go|/usr/bin/go build -o {exe_path} {src_path}|
|C#|/usr/bin/mcs -optimize+ -out:{exe_path} {src_path}|
|Ruby|/usr/bin/ruby {exe_path}|
|Rust|/usr/bin/rustc -O -o {exe_path} {src_path}|
|Haskell|/usr/bin/ghc -O -outputdir /tmp -o {exe_path} {src_path}|
|Free Pascal|/usr/bin/fpc -O2 -o{exe_path} {src_path}|
|Plaintext|/bin/cat {exe_path}|
|Free Basic|/usr/local/bin/fbc {src_path}|

### Supported Feature

- [X] Basic Home Page
- [X] General
    - [X] Cron Support
    - [X] Queue Support
    - [X] Notification Support
        - [X] Browser
        - [X] MessageBox
        - [X] Mail
    - [X] System Version
    - [x] System Bug Report
- [X] User System
    - [X] User Login
    - [X] User Register
    - [X] User Password Retrieve
    - [X] User Email Verify
    - [X] DashBoard
        - [X] Statistics
        - [X] Activities
        - [X] Profile
    - [X] Settings
- [X] Search System
    - [X] Basic Redirect
    - [X] Problem Search
    - [X] User Search
    - [X] Group Search
    - [X] Contest Search
    - [X] OmniSearch Support
- [X] Problem System
    - [X] Problem List
    - [X] Problem Tag
    - [X] Problem Filter
    - [X] Problem Details
    - [X] Problem Solution
    - [X] Problem Discussion
    - [X] Problem Submit
        - [X] Problem Immersive Mode
        - [X] Problem Editor
        - [X] Problem Submit History
        - [X] Problem Compiler List
        - [X] Problem Status Bar
        - [X] Problem Virtual Judge
            - [X] Submit to VJ
                - [X] CodeForces
                - [X] UVa
                - [X] UVa Live
                - [X] HDU
                - [X] Contest Hunter
                - [X] POJ
                - [X] Vijos
                - [X] PTA
            - [X] Retrieve Status
        - [X] Problem Online Judge
            - [X] Judge Server
            - [X] Judger
            - [X] Submit to OJ
            - [X] Retrieve Status
- [X] Status System
    - [X] Status List
    - [X] Status Filter
    - [X] Status Details
        - [X] Syntax Highlight
        - [X] Verdict
        - [X] Code Download
        - [X] Code Share
- [X] Ranking System
    - [X] Casual Ranking List
    - [X] Professional Ranking List
- [X] Contest System
    - [X] Contest List
    - [X] Contest Ranking
    - [X] Contest Filter
    - [X] Contest Details
        - [x] Contest Registration
        - [X] Contest Temp Account
        - [X] Leader Board
        - [X] Contest CountDown
        - [X] Contest Problem List
        - [X] Contest Problem Details
        - [X] Contest Announcements
        - [X] Contest Admin Portal
            - [X] Account Generate
            - [X] Judge Status
            - [X] Issue Announcements
            - [X] ScrollBoard
        - [X] In-Contest Problem Switch
        - [X] Problem Temp Block
    - [X] Contest Ranking System
- [X] Group System
    - [X] Group List
    - [X] Group Details
        - [X] Group Timeline
        - [X] Group Member Management
            - [X] Invite
            - [X] Remove Members
            - [X] Approve Requests
            - [X] Sub Group
        - [X] Group Profile
        - [X] Group General Info
        - [X] Group Functions
            - [X] Group Announcement
            - [X] Group Contests
                - [X] Group-wide Contests
                - [X] Site-wide Contests
            - [X] Group Settings
- [X] Admin Portal
    - [X] Abuse Management
    - [X] Admin User Management
    - [X] Announcement Management
    - [X] Babel Extension Management
    - [X] Babel Marketspace Viewer
    - [X] Contest Management
    - [X] Dashboard Management
    - [X] Dojo Task Management
    - [X] Dojo Pass Records Management
    - [X] Dojo Phase Management
    - [X] Group Management
    - [X] Judge Agent Management
    - [X] Judge Server Management
    - [X] Problem Management
    - [X] Settings
    - [X] Solution Management
    - [X] Submission Management
    - [X] User Management
    - [X] Tools
        - [X] Artisan Terminal
        - [X] Backup Manager
        - [X] Code Tester
        - [X] Database Terminal
        - [X] Log Viewer
        - [X] Media Manager
        - [X] Route Viewer
        - [X] Task Scheduling


## Open Source Contributors

[![Contributors](https://opencollective.com/noj/contributors.svg?width=890&button=false)](https://github.com/ZsgsDesign/NOJ/graphs/contributors)

## Credits

[Laravel](https://github.com/laravel/laravel)

[NOJ JudgeServer](https://github.com/NJUPTAAA/NOJ_JudgeServer)

[NOJ Judger](https://github.com/NJUPTAAA/NOJ_Judger)

See `composer.json` or `package.json` for more info.

## License Info
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FZsgsDesign%2FNOJ.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FZsgsDesign%2FNOJ?ref=badge_large)
