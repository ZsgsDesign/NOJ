# Changelog

All notable changes to this project will be documented in this file.

## v0.5.1 Aphyocharacinae Build Pack 1 - 2021-08-20

This is a build version update for `0.5.0 Aphyocharacinae`.

### Update Logs
* **Compatibility:** NOJ now **mandatorily** requires PHP `^7.4` & Composer 2.
* **New:** Add `asundust/auth-captcha` package.
* **New:** Add `tlsv1.3` check for `babel:install` command.
* **New:** Add `--ignore-platform-reqs` support for `babel:update` command.
* **New:** Add reverse proxy support via env `TRUSTED_PROXIES` per #635 and *Yangzhou High School* request.
* **New:** Add theme *Material Design*, *Dominion Day* and other 5 editor themes.
* **New:** NOJ now supports **Google ReCaptcha v2** in Admin Portal due to security concerns per *relevant government department* requests.
* **New:** NOJ now uses new default dark/light theme `material-design-darker` and `material-design-lighter`.
* **New:** NOJ now supports env `APP_DEFAULT_EDITOR_THEME`.
* **New:** NOJ now respects the OJ status `0` attribute.
* **New:** Update `fonts-asset/devicon` package, see #504.
* **New:** Update `fonts-asset/langicon` package provided by @ZsgsDesign, this package provides support for language icons that devicon doesn't have, see #504.
* **New:** Add env `ADMIN_HTTPS` example.
* **Fixed:** A bug caused temp accounts' email unverified.
* **Fixed:** A bug caused cookie value null.
* **Fixed:** A bug caused vscode editor loading time too long.
* **Fixed:** A WebKit rendering bug caused the border of the user avatar to deviate from its center on the rank page.
* **Fixed:** A styling bug in image hosting.
* **Fixed:** A potential XSS bug.
* **Fixed:** Typo (why always).
* **Improved:** Composer now prioritize `composer-installers-extender`.
* **Improved:** Modify site notice icon and text.
* **Improved:** NOJ now uses `url()` to generate vscode paths.
* **Improved:** Editor themes are now fixed to editor page in blade compile stage, see #631.
* **Security:** `guzzlehttp/guzzle` is **downgraded** to `6.5.5`.

## v0.5.0 Aphyocharacinae - 2021-08-14

This is a minor version update, since this version former build version updates would now ascend to minor version updates, and former patches indexed as a patch number together with hash would be referred to as build version updates.

**Important:** Please follow NOJ Document's guide to upgrading your NOJ from `v0.4.2` to `v0.5.0`.

**Summary:** Add PHP 7.4 & NOJ_JudgeServer v0.2.1 compatibility support, compilers for `Go`, `C#`, `Rust`, `Ruby`, `Haskell`, `Free Pascal`, `Text` and `Free Basic`, OpenJudge NOI BABEL extension support, code plagiarism check beta support, scroll board beta support, compiler info highlight and dialog support, editor themes support, NOJ themes support, image hosting service support, user permissions support, system settings support, AAuth Socialite support, localization support and a brunch of Admin Portal functional update, dozens of new environment configurations and 3 helper functions. This version update also includes lots of bug fixes, functionality & UI improvements, and security updates.

## Update Logs
* **Compatibility:** NOJ now supports `PHP 7.4` per #614 requests.
* **Compatibility:** NOJ now supports `NOJ_JudgeServer v0.2.1` per #615 requests.
* **New:** Add `Golang` language support (require NOJ JudgeServer `v0.1.4` or higher).
* **New:** Add `C#` language support (require NOJ JudgeServer `v0.1.4` or higher).
* **New:** Add `Rust` language support (require NOJ JudgeServer `v0.2.0` or higher).
* **New:** Add `Ruby` language support (require NOJ JudgeServer `v0.2.0` or higher).
* **New:** Add `Haskell` language support (require NOJ JudgeServer `v0.2.0` or higher).
* **New:** Add `Free Pascal` language support (require NOJ JudgeServer `v0.2.0` or higher).
* **New:** Add `Text` special language support (require NOJ JudgeServer `v0.2.0` or higher).
* **New:** Add `Free Basic` language support (require NOJ JudgeServer `v0.2.1` or higher).
* **New:** OpenJudge NOI Group BABEL extension support per #616 requests, see [OpenJudge NOI](http://noi.openjudge.cn/).
* **New:** NOJ code plagiarism check beta support per #617 requests, only contests that are `official` and `anticheat` can use this feature.
* **New:** NOJ scroll board beta support per #618 requests, only contests that require registration, uses ICPC rules and has frozen time can use this feature, you can use <kbd>ctrl</kbd>+<kbd>enter</kbd> to auto-scroll or use <kbd>enter</kbd> to scroll manually.
* **New:** NOJ compiler info highlight and dialog support per #619 requests, you can now click CE records and view info in a more elegant way.
* **New:** NOJ editor themes support per #622 requests, you can now choose from a variety of totally 51 themes to customize your NOJ code editing experience.
* **New:** NOJ themes support per #623 requests, you can now choose from a variety of totally 15 themes, including the `classic` theme that got removed prior to NOJ `v0.1.0` update, to customize your NOJ experience, see [NOJ Docs - Theme](https://njuptaaa.github.io/docs/#/noj/guide/theme) for more information.
* **New:** NOJ tools image hosting service support per #625 requests, you can now use NOJ image hosting service to upload images.
* **New:** NOJ user permissions support per #626 requests, you can now use the command `php artisan manage:permission` to manage user permissions.
* **New:** NOJ settings support per #500 requests, you can now use the database to manage some NOJ settings.
* **New:** Socialite support for [AAuth](https://cn.aauth.link/) per Yangzhou High School requests, see #624.
* **New:** BABEL command `php artisan babel:require` now supports `--ignore-platform-reqs` option.
* **New:** Users without passwords can create new passwords on the settings page, see #588.
* **New:** Locale support for NOJ VSCode Editor.
* **New:** NOJ  site registration control support via env `FUNC_ENABLE_REGISTER` per #586 requests.
* **New:** NOJ multi-domain support via env `APP_MULTI_DOMAIN` per #620 requests.
* **New:** NOJ OAuth login temp account support via env `APP_ALLOW_OAUTH_TEMP_ACCOUNT` per Yangzhou High School requests, see #588.
* **New:** NOJ OAuth login switch support via env `GITHUB_ENABLE` and `AAUTH_ENABLE` per #621 requests.
* **New:** Dashboard system requirements check support for Admin Portal.
* **New:** Add `monaco-ace-tokenizer` package that provides an advanced highlight for many languages, NOJ used it for `Haskell`.
* **New:** Add `fonts-asset/socialicon` package provided by @ZsgsDesign, this package provides support for Socialite icons that Material Design Icons doesn't have, see #504.
* **New:** Add `fonts-asset/langicon` package provided by @ZsgsDesign, this package provides support for language icons that devicon doesn't have, see #504.
* **New:** Group management support for Admin Portal.
* **New:** Group locale support for Admin Portal.
* **New:** Contest management support for Admin Portal.
* **New:** Judge Agent management support for Admin Portal.
* **New:** Judge Agent locale support for Admin Portal.
* **New:** Judge Server management support for Admin Portal.
* **New:** Judge Server locale support for Admin Portal.
* **New:** Submission management support for Admin Portal.
* **New:** Dojo Tasks management support for Admin Portal.
* **New:** Dojo Tasks locale support for Admin Portal.
* **New:** Dojo Phase management support for Admin Portal.
* **New:** Dojo Phase locale support for Admin Portal.
* **New:** Settings support for Admin Portal.
* **New:** Settings locale support for Admin Portal.
* **New:** CodeTester support for Admin Portal.
* **New:** CodeTester locale support for Admin Portal.
* **New:** CodeTester API support for BABEL.
* **New:** User locale support for Admin Portal.
* **New:** Announcement management support for Admin Portal.
* **New:** Announcement locale support for Admin Portal.
* **New:** Sidebar locale for Admin Portal.
* **New:** Default error locale for NOJ.
* **New:** Scroll board locale for NOJ.
* **New:** Image hosting service locale for NOJ.
* **New:** OAuth locale for NOJ.
* **New:** Add `vscodeLocale()` helper function.
* **New:** Add `getTheme()` helper function.
* **New:** Add `setting()` helper function.
* **Deprecated:** MOSS code plagiarism check no longer supported.
* **Deprecated:** Remove unused `bootstrap-material-design.js` files.
* **Deprecated:** Remove `GuzzleHttp\json_decode` function usage.
* **Deprecated:** Remove duplicated `App\User` and outdated `App\Models\UserModel`, NOJ now use `App\Models\Eloquent\User` instead.
* **Deprecated:** Remove outdated `App\Models\AnnouncementModel`, NOJ now use `App\Models\Eloquent\Announcement` instead.
* **Deprecated:** Remove outdated `App\Models\CarouselModel`, NOJ now use `App\Models\Eloquent\Carousel` instead.
* **Fixed:** Concurrency problem of virtual verdict syncing, see [NOJ Docs - BABEL Judge Sync](https://njuptaaa.github.io/docs/#/noj/guide/queue?id=babel-judge-sync) for more information.
* **Fixed:** A bug that causes NOJ JudgeServer to return Wrong Answer verdict.
* **Fixed:** A bug that makes the score of submissions within ACM rules 1 if the verdict is accepted.
* **Fixed:** A bug in the contest status list that will return all submissions when applied a problem ncode filter that doesn't exist in that match.
* **Fixed:** A bug that will redirect users to the dashboard after login instead of the home page or contest page (for contest account).
* **Fixed:** A load balancing bug that causes the load balancer to pick the highest judge server.
* **Fixed:** A bug that makes the system info page display no judge server when the noj babel package was installed with an ID that is not `1`.
* **Fixed:** A bug that makes scrollbar area transparent in language select menu, this bug is caused by a Chrome update.
* **Fixed:** A bug that messed up HTML structures in NOJ Admin Portal inside `x3zvawq/noj_simplemde`.
* **Fixed:** Typo *(tons of)*.
* **Fixed:** A bug that let Eloquent ContestProblem Model disrespect frozen time.
* **Fixed:** An Admin Portal bug that causes solution uneditable.
* **Fixed:** An Admin Portal bug that causes group uncreatable.
* **Fixed:** A bug that causes frozen time inaccessible for ContestProblem Model.
* **Fixed:** A bug that causes verification email resends return `Method Not Allowed`.
* **Fixed:** A bug that makes NOJ only accepts 12 BABEL extensions/packages.
* **Fixed:** A bug that errors some `php artisan manage command default value.
* **Fixed:** A styling bug that causes the new langicon misplaced.
* **Fixed:** A bug that errors problem search.
* **Fixed:** A potential divide by 0 bug.
* **Fixed:** A typo in terms template.
* **Fixed:** PSR-4 autoload issues.
* **Fixed:** A typo that causes user contact info cannot update and display correctly.
* **Fixed:** A bug that only login users can access OAuth links.
* **Improved:** NOJ JudgeServer C compiler now compiling without `-static` option.
* **Improved:** NOJ JudgeServer PHP compiler now running with `-d error_reporting=0` option.
* **Improved:** NOJ JudgeServer C# compiler now running without unused environment variables, adjusting `C#` compile max memory accordingly.
* **Improved:** BABEL `install` command now accepts `new_code` as a valid compiler description key.
* **Improved:** Removed the auto-refresh feature at the contest detail page that may potentially cause HTTP 500 errors at the beginning of a match.
* **Improved:** Removed a lot of the useless `consle.log()` code.
* **Improved:** Compiler list now ordered by display name, in earlier version, we arrange it simply by `coid` and would be a bad choice when we have a dozen of compilers supported.
* **Improved:** Update `fonts-asset/devicon` package adding new language icons that NOJ needed.
* **Improved:** Update `monaco-editor` to `0.23.0`, the changes of platform detection algorithm forced us to abandon the `#vscode` element in editor view that we used before and renamed it to `#monaco`, see microsoft/monaco-editor#2409.
* **Improved:** Update `npm-asset/pdfobject`, supporting Chromium based Edge Browser.
* **Improved:** `JudgeServer` and `OJ` now have the proper Eloquent Models.
* **Improved:** Remove `Model` affix for Eloquent Models.
* **Improved:** Add missing foreign keys for database tables.
* **Improved:** Admin Portal general locale update.
* **Improved:** `readable_name` attribute for problem.
* **Improved:** `fillable` attribute added for some Eloquent Models.
* **Improved:** `TLS v1.3` check.
* **Improved:** VSCode worker now uses `config` to generate URL instead of original `env`.
* **Improved:** Componentify VSCode frontend.
* **Improved:** Editor style improved for light themes.
* **Improved:** Now markdown block formula supports `$$$$$$`.
* **Improved:** Remove unnecessary params when binding Eloquent Models.
* **Improved:** Rename route names fitting dot-separated standard.
* **Improved:** Colon localization on problem details.
* **Improved:** Admin badge and status now depend on the permission table.
* **Improved:** Users can now change usernames freely, see #588.
* **Improved:** Redesigned user card.
* **Improved:** Redesigned settings card.
* **Improved:** Disable `@temporarily.email` domain registration, see #588.
* **Improved:** Testcase zip files without any file inside would now be detected.
* **Improved:** Remove part of `AccountModel` methods and implement them in `User` model.
* **Improved:** Adjust socialite setting styles.
* **Improved:** Adjust socialite login styles.
* **Improved:** Adjust dropdown menu styles.
* **Security:** `mews/purifier` is now at `3.3.5`.
* **Security:** `npm-asset/codemirror` is now at `5.61.1`.
* **Security:** `rmccue/requests` is now at `1.8.1`.
* **Security:** `laravel/framework` is now at `6.20.32`.
* **Security:** `maatwebsite/excel` is now at `3.1.32`.
* **Security:** `filp/whoops` is now at `2.14.0`.
* **Security:** `npm-asset/perfect-scrollbar` is now at `1.5.2`.
* **Security:** `intervention/image` is now at `2.6.1`.
* **Security:** `encore/laravel-admin` is now at `1.8.13`.
* **Security:** `npm-asset/lodash` is now at `4.17.21`.
* **Security:** `phpoffice/phpspreadsheet` is now at `1.17.1`.
* **Security:** `npm-asset/jquery` is now at `3.6.0`.
* **Security:** `league/flysystem` is now at `1.1.4`.
* **Security:** `phpseclib/phpseclib` is now at `2.0.32`.

## v0.4.2 Pristobrycon - 2020-12-16

### Update Logs
* **New:** `font-asset` composer package management.
* **New:** Admin Portal default solution filter.
* **New:** Option to disable backup.
* **New:** Customizable contact info.
* **New:** Customizable logo and desc.
* **New:** Locale files for error pages and NOJ Desktop pages.
* **New:** Problem Add in Admin Portal.
* **New:** Problem Add now supports `NOJ makrdown`.
* **New:** Problem Add now supports sample I/O addition.
* **New:** Problem Add now supports `info` generation.
* **New:** Problem Add now partically supports SPJ.
* **New:** Contest Create now supports status visibility options.
* **New:** NOJ Babel Extension now supports SPJ submission.
* **Easter Egg:** ?!
* **Fixed:** Problem add button.
* **Fixed:** Backup compatibility.
* **Improved:** Destop notification now has a limit.
* **Improved:** Avatar with new design.
* **Improved:** Add frontend util `createNOJMarkdownEditor` and simplified `SimpleMDE` creation.
* **Improved:** Add Eloquent `Pastebin` Model and remove old `PastebinModel`.
* **Improved:** NOJ Babel Extension will now update status for unavailavle servers.
* **Security:** `albertofem/rsync-lib` is now `removed`.

## v0.4.1 Catoprion - 2020-12-09

### Update Logs
* **New:** NOJ API support.
* **New:** NOJ Desktop support.
* **New:** Multi-language support.
* **Fixed:** Contest sponsor badge no longer misplaced.
* **Fixed:** Database migration now make timestamp nullable.
* **Fixed:** Typo *(no kidding, we really fixed some typo)* .
* **Improved:** Applying Eloquent model for more occasions.
* **Security:** `laravel/framework` is now at `6.20.7`.
* **Security:** `composer` is now at `2.0.0`.
* **Security:** `symfony/http-kernel` is now at `4.4.17`.

## v0.4.0 Piranha - 2020-05-01

### Update Logs
- **New**: Grouped Message.
- **New**: Now php artisan down supports custom message.
- **New**: Submission Error resubmit inside contest.
- **New**: `babel update` & `babel install` support for Admin Portal.
- **New**: Users can now leave a group.
- **New**: NOJ Dojo support.
- **New**: Report Abuse support.
- **Fixed**: Error occured when trying to enter non-exist groups.
- **Fixed**: Group Abuse message leader name display bugs.
- **Fixed**: MDI display error.
- **Fixed**: ProblemModel `file_url` no longer requirable.
- **Fixed**: Notification while leader leaving the group.
- **Fixed**: Accpet invitation display.
- **Fixed**: GitHub Login.
- **Fixed**: Double Footer.
- **Fixed**: MarkerPen Issues.
- **Improved**: NOJ now use Eloquent for all New Models.
- **Improved**: Error Page.
- **Improved**: NOJ Standalone Queue.
- **Improved**: Problem now supports order .
- **Improved**: babel crawl now supports from option.

## v0.3.2 Meyeri - 2019-08-21

### Update Logs
* **New:** Problem Discussion Support.
* **Fixed:** Now Babel Crawler's CURL Error would not be fatal dead.
* **New:** Add Babel Manager and Marketspace View.
* **Improved:** Won't Show Sample Input or Output if Not Present.
* **New:** Marker Pen Support for Problem Detail and Problem Editor.
* **New:** Scrollboard Support.
* **New:** In-contest status filter support.
* **New:** Messagebox Support.
* **New:** Helper Function `sendMessage()` to Send Message.
* **New:** Helper Function `convertMarkdownToHtml()` to Convert Markdown to Html.
* **New:** FrontEnd Utils `delay()` for promise delaying.
* **New:** PDF Viewer support.
* **New:** File URL Field in Database.
* **New:** Golang Interface Support.
* **New:** Add an Index in Group Analysis.
* **New:** SPJ Support.
* **New:** Hide Field in Database to hide problems when needed.
* **New:** Submission codes are now properly highlighted in Admin Panel.
* **Improved:** Favicon Support for Admin Panel.
* **Improved:** Add New Font Roboto Slab and Replaced old Montserrat.
* **Improved:** Allow Juxtaposition of Rankings.
* **Improved:** Allow Member to Check Elo Change Log in Group.
* **Improved:** Google Translate will not avoid translate codes.
* **Improved:** Add Schedule to Update Group Elo.
* **Improved:** New Casual Rank Title.
* **Improved:** Add Contest Filter, Problem Filter and User Filter for Submission Admin Panel.
* **Fixed:** A Bug about Space's Display.
* **Fixed:** A Bug about Babel Sample Note, Input and Output Null Value.
* **Fixed:** A Bug causing No Available Judger.
* **Fixed:** Bug regarding Babel Monitor.
* **Fixed:** Bug regarding Encoding.
* **Fixed:** Fix a Bug When Calc Elo After Kick a Member.
* **Fixed:** Bug regarding Analysis Download.
* **Fixed:** Permission about Group Analysis.
* **Fixed:** Sync Contest Judger Name.
* **Fixed:** Bugs about Account Generate.
* **Fixed:** Bug regarding Public Contest Auditing.
* **Fixed:** Bug regarding Contest Account Social Bind.
* **Fixed:** Bug regarding Default Rank.
* **Fixed:** Right Click for MathJax is now disabled.
* **Fixed:** Increase `max_real_time` values to avoid certain misjudgment about Real Time Limit Exceed.
* **Security:**  NOJ now requires `goodnesskay/laravelpdfviewer ^1.0`.
* **Security:**  NOJ now requires `npm-asset/fileicon-svg ^1.0`.
* **Security:**  NOJ now requires `npm-asset/lodash ^4.17`.
* **Security:**  NOJ now requires `npm-asset/monaco-editor 0.17.1` instead of `npm-asset/monaco-editor ^0.16.2`.
* **Security:**  `npm-asset/monaco-editor` is now at `0.17.1`.
* **Security:**  `phpoffice/phpspreadsheet` is now at `1.9.0`.
* **Security:**  `laravel/framework` is now at `5.8.32`.

## v0.3.1 Citoniensis - 2019-08-05

### Update Logs
* **New:** ZOJ support.
* **New:** Now we have our own unique error page instead of a weak and helpless 404 page.
* **New:** Subgroup support in a group.
* **New:** It is now possible to create a site-wide contest within a group.
* **New:** Strikethrough support for markdown.
* **New:** Definable languages for markdown codes.
* **New:** We now support resubmitting code that failed to submit.
* **New:** In-contest status filter support.
* **New:** Onmi Search support over contests, groups, users and problems.
* **New:** Formula support on solution page's markdown editor.
* **New:** Microsoft Application tile color support.
* **Deprecated:** Microsoft Application Metadata, which is part of Win10 Tiles would be removed from Windows 10 19H2, we will no longer maintain those meta tags.
* **New:** Babel Extension Manager support.
* **New:** NOJ Installer support.
* **New:** Babel now supports `Monitor` interface for Online Judges.
* **Improved:** NOJ Statics menu in the admin panel.
* **Improved:** Remove permission system, role system, operation log system inside the admin panel.
* **Improved:** Use MDI rather than Font Awesome icons in the admin portal.
* **Improved:** More resources are now supported in the admin portal.
* **Improved:** Now you can choose whether you want to skip to the contest details page after creating one.
* **Improved:** add Reset Password support(`php artisan manage:resetpass --uid=1 --digit=9`) in artisan command.
* **Improved:** It is now possible to manually refresh rank in a contest and the Elo in a group. 
* **Fixed:** Admin Panel Bugs about Eloquent Models were fixed in this version.
* **Fixed:** The ranking and Elo points within the group will no longer be counted incorrectly for Synchronized Contests between remote teams.
* **Fixed:** Images and descriptions that do not conform to the rules will now be prompted when creating a group. 
* **Fixed:** Fixed a Fatal Syntax Error that might have been caused by calculating Elo Rate.
* **Fixed:** Now any member of the group can view the Analysis of the members and the results of the Practice Contest.
* **Fixed:** The problems in Contest Management will now be sorted correctly.
* **Fixed:** Now we will delete old submission when removing problems from the contest.
* **Fixed:** Now we will generate contest rank table every minute.
* **Fixed:** The problems in the group can only be seen by the contest assignee.
* **Security:** The package `php-simple-html-dom-parser` with a version higher than `1.7.1` has severe bugs and we had to rollback from `1.9.0` to `1.7.1`.
* **Security:**  NOJ now requires `mysql ^5.7` instead of `mysql ^5.5.3`.
* **Security:**  NOJ now requires `graham-campbell/markdown ^11.0` instead of `graham-campbell/markdown ^10.2`.
* **Security:**  NOJ now requires `phpunit/phpunit ^7.5` instead of `phpunit/phpunit ^7.0` though they pose likely no change at all.
* **Security:**  `laravel/framework` is now at `5.8.30`.
* **Security:**  `league/commonmark` is now at `1.0.0`.

## v0.3.0 Orca - 2019-07-21

### Update Logs
* **New:** Third-party Login (Github supported).
* **New:** Judge Server Status.
* **New:** Now we will ask users to submit solutions when they got an accepted.
* **New:** Group creation is now available through the web page.
* **New:** Group member invitation is also available.
* **New:** Now we have group settings. Group leaders can view and change group information in the settings page.
* **New:** Contest filter is available.
* **New:** Group announcement is available.
* **New:** Now we have a contest admin portal.
* **New:** Permission Updated: The manager could arrange their own contest but not others now.
* **New:** Group contest management is completed.
* **New:** We now have synchronized contests support for HDU.
* **New:** Introducing a new contest category: Practices.
* **New:** Now we can add tags for problems inside your group, it's independent and could be used to track group users' status by combine tagging with Practices Mode.
* **New:** Wilder Elo Rating supported, as so, now all groups have it's own Elo Rating, it's the result of all group's practice contests.
* **New:** Introducing Babel. Babel, the name of a tower tall enough to reach heaven, is the source of the world's different languages. And in the land of Online Judges there exists differentials too. Now, with project Babel, we are able to build a unification model to once again unite all OJs.
* **New:** We have carousel supported on the homepage now.
* **New:** Modify the schedule execution time from 0 o'clock to the peak-load shifting.
* **Improved:** Feature group algorithms are now improved.
* **Improved:** Independent contest manager, it responses to a single contest and have full access to it just like group admin, normally the manager created the contest who be the initial manager.
* **Improved:** Provide `SimpleMDE` instead of `Monaco Editor` to most of the cases, including arrange contests, submit solutions or broadcast clarifications.
* **Improved:** The contest rank is permanently stored in MySQL.
* **Improved:** We now have a separate database and site backup.
* **Improved:** Now the leader of a group can also manage permission of users, promote users or simply demote them.
* **Improved:** Now `animate.css` and `datetimepicker` are imported as packages.
* **Improved:** Now generating contest accounts could be done nicely with excel download option available.
* **Improved:** Sitewide Announcements in the past have only one slot, we make it feed-like in this version.
* **Fixed:** Wrong paginator in the contest index page.
* **Fixed:** The problem cannot be displayed after the contest.
* **Fixed:** Remove the cancel button for the search box.
* **Security:** NOJ now requires `php ^7.3` instead of `php ^7.1.3`.
* **Security:** In the last version, users who logged in as a contest account could access problem solution page even it's forbidden. We fixed that in this version.

## v0.2.3 Barracuda - 2019-06-12

### Update Logs
* **New:** UVa Live support.
* **New:** HDU support.
* **New:** Group member management, approve, decline or kick them.
* **Improved:** Contest rank logic can now be loaded instantly, we introduced Redis cache to help cache the scoreboard.
* **Improved:** Now all OJs have supported multiple accounts in case one account might be blocked for having too many requests/submits in a short period.
* **New:** We now have utils for prompt and confirm.
* **Improved:** Now all contests' clarifications are broadcasted via global, systemwide notifications as well.
* **New:** Add a float size delimiter for NOJ editor.
* **Improved:** Now a group's contests list has paginations.
* **New:** Now we can change a group's name and other attributes through Group Settings Page.
* **Fixed:** The feedbacks for Login are now in the correct format.
* **Fixed:** The MySQL Database migration are now forced to use InnoDB engine and we have fixed the migration files.

## v0.2.2 Gator - 2019-05-28

### Update Logs
* **New:** Problem solution page support.
* **New:** Filter for status.
* **New:** Add user center and settings page.
* **New:** We now have contest registration.
* **New:** Introduce professional rating system uses Elo Rating Model.
* **New:** Introduce casual ranking system and rank page.
* **New:** NOJ Feed.
* **Improved:** We now can print problems elegantly.
* **Improved:** Users can now view their problem status in the problem list page.
* **New:** EMail Verification.
* **Security:** Password Minimal Digits are now 8 instead of 6.
.* **New:** NOJ Feed.
* **Improved:** We now supports Google Analytics.
* **Improved:** We now have Bootstrap Material Design as a package.
* **Improved:** Down an Online Judge, making it unavailable.

## v0.2.1 Great White - 2019-05-06

### Update Logs
* **New:** UVa support.
* **New:** Code Sharing via Pastebin.
* **New:** Search with problem code.
* **New:** Report bugs via GitHub.
* **New:** Daily Backup.
* **New:** User Center with Bing Daily Pic.
* **New:** Sitemap support.
* **New:** Baidu verification support.
* **New:** Code Sharing via NOJ.
* **New:** Pastebin support.
* **Fixed:** CodeForces & UVa bugs.
* **Improved:** New sorting algorithm for Problem Page.

## v0.2.0 Hammerhead - 2019-04-26

### Update Logs
* **New:** Admin Portal.
* **New:** Redis Cache.
* **New:** Detailed Status.
* **New:** User Dashboard.
* **Fixed:** Fixed bug that some images in POJ may fail to cache.
* **New:** Utils for notification.
* **Fixed:** Contest Clarification bugs.
* **Improved:** Users can now upload avatars.
* **Improved:** Upgrade to Laravel 5.8.
* **New:** Google verification support.
* **Improved:** Deleted confusing letters in `generatepassword()`.

## v0.1.3 Jaws - 2019-04-07

### Update Logs
* **Improved:** Choose users' preference compiler based on submission history.
* **New:** Submission history support.
* **New:** Contest pagination.
* **New:** Problem pagination.
* **New:** System info page.
* **New:** Rejudge support.
* **New:** View submission details.
* **Improved:** CE can now return info.
* **Fixed:** PTA Interface is now up-to-date with the latest version.
* **Improved:** Modify NOJ color scheme and rebuild NOJ footer.

## v0.1.2 Mako - 2019-03-21

### Update Logs
* **New:** General status page support.
* **New:** Image for contest detail page.
* **Improved:** Limited submit frequency.
* **New:** Clarification submission.
* **New:** Custom brand for sponsored contests.
* **New:** Code download.

## v0.1.1 Moray - 2019-03-15

### Update Logs
* **New:** Contest announcement broadcast.
* **New:** Add copy function of input examples.
* **New:** Forze board support.
* **New:** Status Board support.
* **Performance:** Add a MySQL auto reload when the server's down.
* **Improved:** Queue optimization for judger.

## v0.1.0 Coelacanth - 2019-03-14

This is the first major version of NOJ, so there is nothing to update to this version, thus the log shall remain empty.

### Update Logs
* **Initial:** This is the first major version of NOJ, anything before this was not logged.
