# NOJ  - Automatic Algorithm Test Platform

![NOJ](/noj2.png)

NOJ's another online judge platform, stands for NJUPT Online Judge. It's written in PHP, GO, Python and other function-supporting languages and supports both online judges and virtual judges, we called it **mixed judge**.

![License](https://img.shields.io/github/license/ZsgsDesign/NOJ.svg?style=flat-square)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/ZsgsDesign/NOJ.svg?style=flat-square)](https://scrutinizer-ci.com/g/ZsgsDesign/NOJ/?branch=master)
[![FOSSA Status](https://img.shields.io/badge/license%20scan-passing-green.svg?style=flat-square)](https://app.fossa.io/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster?ref=badge_shield)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ZsgsDesign/NOJ/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ZsgsDesign/NOJ/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/ZsgsDesign/NOJ.svg?style=flat-square)](https://scrutinizer-ci.com/g/ZsgsDesign/NOJ/build-status/master)
![GitHub repo size](https://img.shields.io/github/repo-size/ZsgsDesign/NOJ.svg?style=flat-square)
![Stars](https://img.shields.io/github/stars/zsgsdesign/noj.svg?style=flat-square)
![Forks](https://img.shields.io/github/forks/zsgsdesign/noj.svg?style=flat-square)

## NOJ Development Team

| [<img src="https://github.com/ZsgsDesign.png?s=64" width="100px;"/><br /><sub><b>John Zhang</b></sub>](https://github.com/ZsgsDesign)<br />**Leader**   | [<img src="https://github.com/DavidDiao.png?s=64" width="100px;"/><br /><sub><b>David Diao</b></sub>](https://github.com/DavidDiao)<br />**Deaputy**<br />  | [<img src="https://github.com/pikanglong.png?s=64" width="100px;"/><br /><sub><b>Cone Pi</b></sub>](https://github.com/pikanglong)<br />**BackEnd**  | [<img src="https://github.com/X3ZvaWQ.png?s=64" width="100px;"/><br /><sub><b>X3ZvaWQ</b></sub>](https://github.com/X3ZvaWQ)<br />**BackEnd** | [<img src="https://github.com/Alicefantay.png?s=64" width="100px;"/><br /><sub><b>Alice</b></sub>](https://github.com/Alicefantay)<br />**Design** | [<img src="https://github.com/goufaan.png?s=64" width="100px;"/><br /><sub><b>goufaan</b></sub>](https://github.com/goufaan)<br />**FrontEnd**   |  [<img src="https://github.com/ChenKS12138.png?s=64" width="100px;"/><br /><sub><b>ChenKS12138</b></sub>](https://github.com/ChenKS12138)<br />**FrontEnd** |
| :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| [<img src="https://github.com/Rp12138.png?s=64" width="100px;"/><br /><sub><b>Rp12138</b></sub>](https://github.com/Rp12138)<br />**BackEnd**   |

## Installation

CentOS will be recommended for hosting NOJ, but all major operating systems are theoretically supported.

Till now, NOJ have been successfully deployed to the following systems:

- **Ubuntu 16.04** and above
- **CentOS Linux release 7.1** and above
- **Windows 10 Professional**

For installation options and troubleshooting tips, see [installation](https://njuptaaa.github.io/docs/).

## Supported Feature

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
    - [X] User Password Retrive
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
                - [ ] SPOJ
                - [X] HDU
                - [X] Contest Hunter
                - [X] POJ
                - [X] Vijos
                - [X] PTA
            - [X] Retrive Status
        - [X] Problem Online Judge
            - [X] Judge Server
            - [X] Judger
            - [X] Submit to OJ
            - [X] Retrive Status
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
- [ ] Contest System
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
    - [ ] Contest Clone
    - [ ] Contest Virtual Participate
- [ ] Group System
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
        - [ ] Group Functions
            - [X] Group Announcement
            - [X] Group Contests
                - [X] Group-wide Contests
                - [X] Site-wide Contests
            - [ ] Group Own ProblemSet
                - [ ] Add Problem
            - [X] Group Settings
- [X] Admin Portal
    - [X] User Management
    - [X] Contest Management
    - [X] Problem Management


## Credit

[Laravel](https://github.com/laravel/laravel)

[Markdown](https://github.com/GrahamCampbell/Laravel-Markdown)

[Simple-HTML-Dom](https://github.com/Kub-AT/php-simple-html-dom-parser)

[JudgeServer](https://github.com/MarkLux/JudgeServer)

[HTML Purifier](https://github.com/mewebstudio/Purifier)

See `composer.json` or [Dependency List](https://app.fossa.com/attribution/263d9a48-87a3-4043-b6f4-42e0f5755351) for more info.

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2FZsgsDesign%2FCodeMaster?ref=badge_large)
