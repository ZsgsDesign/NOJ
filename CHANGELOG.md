# Changelog

All notable changes to this project will be documented in this file.

## NOJ 0.17.4 Characinae Build Pack 4 - 2021-11-03
This is a build version update for `0.17.0 Characinae`.

**Important:** Rerun `npm ci` and `composer install` then `npm run production`.

### Update Logs
* **New:** NOJ now supports `contest.exsits` middleware.
* **New:** NOJ now uses Github markdown style to render messages.
* **Fixed:** A PHP7.4 compatibility bug causing contest details to return 500 when users don't have clearance.
* **Improved:** Remastered contest routers.
* **Security:** `encore/laravel-admin` is now at `1.8.16`.
* **Security:** `laravel/framework` is now at `8.69.0`.
* **Security:** `laravel/passport` is now at `10.2.0`.
* **Security:** `laravel/ui` is now at `3.3.1`.
* **Security:** `phpseclib/phpseclib` is now at `3.0.11`.
* **Security:** `symfony/console` is now at `5.3.10`.
* **Security:** `symfony/http-foundation` is now at `5.3.10`.
* **Security:** `symfony/http-kernel` is now at `5.3.10`.
* **Security:** `symfony/string` is now at `5.3.10`.
* **Security:** `symfony/translation` is now at `5.3.10`.
* **Security:** `symfony/var-dumper` is now at `5.3.10`.

## NOJ 0.17.3 Characinae Build Pack 3 - 2021-10-30
This is a build version update for `0.17.0 Characinae`.

**Important:** All previous messages stored in the `message` table are not compatible with this update, please remove them manually.

### Update Logs
* **Compatibility:** This update no longer supports old message format, please remove all old messages manually.
* **New:** NOJ now supports BABEL Extension **CodeForces Gym**.
* **New:** Add locale for official messages.
* **New:** Add environment variable `APP_OFFICIAL_SENDER`, maintainers can now set this variable to a certain user id and make that user the sender of all official messages.
* **New:** Messages now have levels like `info`, `success`, `warning`, `error`, and `question`.
* **New:** NOJ now notifies users when a submitted solution got accepted.
* **New:** NOJ now notifies users of new homework.
* **New:** NOJ now notifies users when the global rank in or out top 100.
* **Deprecated:** NOJ no longer uses old **WKHTMLTOPDF** configs.
* **Deprecated:** NOJ no longer uses using Solution model, using ProblemSolution model instead.
* **Fixed:** A PHP7.4 compatibility bug causing problem detail page returns 500 when problem not found.
* **Fixed:** A bug causing Admin Portal contest practice field unchangeable.
* **Fixed:** A bug causing blink does not generate pdf URLs.
* **Fixed:** A missing exception type bug causing exception catch invalid for NOJ BABEL.
* **Fixed:** A NOJ BABEL Extension **CodeForces** bug causing crawled problem missing some irregular fields.
* **Fixed:** A NOJ BABEL Extension **CodeForces** bug causing crawled problem with `div` tags display wrongly.
* **Fixed:** Typo (special thanks to @gtn1024).
* **Improved:** Remastered message box view.
* **Security:** `doctrine/inflector` is now at `2.0.4`.
* **Security:** `facade/ignition` is now at `2.16.0`.
* **Security:** `laravel/framework` is now at `8.68.1`.
* **Security:** `nanoid` is now at `3.1.30`.
* **Security:** `perfect-scrollbar` is now at `1.5.3`.
* **Security:** `postcss` is now at `8.3.11`.
* **Security:** `sass` is now at `1.43.4`.

## NOJ 0.17.2 Characinae Build Pack 2 - 2021-10-21
This is a build version update for `0.17.0 Characinae`.

**Important:** Rerun `npm ci` and `composer install` then `npm run production`.

### Update Logs
* **Compatibility:** This update no longer uses wkhtmltopdf.
* **New:** NOJ now greatly improves PDF generation speed and quality by using 2 new approaches: CPDF or Blink with Skia.
* **New:** NOJ now uses `nesk/puphpeteer`.
* **New:** NOJ now uses `barryvdh/laravel-dompdf`.
* **Deprecated:** NOJ no longer uses old `LatexModel` and `LatexController`.
* **Deprecated:** Helper function `latex2image` is no longer supported.
* **Fixed:** A bug causing PDF-generated Chinese character replaced by blank square.
* **Fixed:** A bug causing PDF generation exit without error.
* **Fixed:** Typo (only 1 this time).
* **Improved:** PDF generation now does not wait 20 seconds then proceed, it quits when complete.
* **Security:** `fonts-asset/dejavu` is now at `1.0.4`.
* **Security:** `fonts-asset/simsun` is now at `1.0.2`.
* **Security:** `graham-campbell/result-type` is now at `1.0.3`.
* **Security:** `laravel/framework` is now at `8.65.0`.
* **Security:** `laravel/passport` is now at `10.1.4`.
* **Security:** `league/oauth2-server` is now at `8.3.3`.
* **Security:** `mews/purifier` is now at `3.3.6`.
* **Security:** `phpdocumentor/reflection-docblock` is now at `5.3.0`.
* **Security:** `swiftmailer/swiftmailer` is now at `6.3.0`.

## NOJ 0.17.1 Characinae Build Pack 1 - 2021-10-12
This is a build version update for `0.17.0 Characinae`.

**Important:** Rerun scheduling for update site rank or run `php artisan scheduling:updateSiteRank` after this upgrade.

### Update Logs
* **Compatibility:** The update site rank scheduling is now set at 1 am everyday.
* **New:** Add custom favicon, logo, and navicon support per #737 requests.
* **New:** Add `LikeScope` trait support.
* **New:** Add dynamic rank feature, see #649.
* **New:** Add a series of artisan commands with prefix `scheduling`, see #743.
* **Deprecated:** NOJ no longer uses old `RankModel` and `SiteMapModel`.
* **Fixed:** A bug causing Admin Portal ajax pagination returns only the first 15 records, see #738.
* **Fixed:** A bug causing Admin Portal ajax pagination memory overflows on a large amount of data and queries slowly, see #739.
* **Fixed:** A bug causing PDF generator cannot locate dejaVu font.
* **Fixed:** A Symphony 5 compatibility bug causing anti-cheat malfunction, see #740.
* **Fixed:** A Symphony 5 compatibility bug causing PDF generation malfunction, see #741.
* **Fixed:** A `laravel-admin-ext/scheduling` bug causing Admin Portal schedule running returns 419 on Windows platform, see laravel-admin-extensions/scheduling#20.
* **Fixed:** Typo (only 1 this time).
* **Improved:** Optimized site rank calculation performance, see #649.
* **Improved:** Optimized sitemap performance to process a tremendous amount of data, see #742
* **Improved:** Use artisan commands for the scheduling system, see #743.
* **Improved:** Use dot-separated router for problem and status index.
* **Security:** `doctrine/dbal` is now at `2.13.4`.
* **Security:** `facade/ignition` is now at `2.15.0`.
* **Security:** `laravel-admin-ext/log-viewer` is now at `1.0.4`.
* **Security:** `laravel-admin-ext/scheduling` is now at `1.2`.
* **Security:** `ramsey/collection` is now at `1.2.2`.

## v0.17.0 Characinae - 2021-10-09
This is a minor version update. As mentioned in `0.5.0` logs, the new version system would merge the old major and minor version numbers into new minor version numbers, thus as the 17th minor version update since NOJ `0.1.0`, this version would be numbered as major version `0`, minor version `17`, build pack `0` and patch number `0`.

**Important:** Please follow NOJ Document's guide to upgrading your NOJ from `v0.16.x` to `v0.17.0`.

**Summary:** Update Laravel from `6.x` to `8.5`, update Laravel Mix to `6.0`, requires `WKHTMLPDF` installed, uses new logo design, supports multiple log channels, supports group homework, supports strong password, supports `C11`, `C++14` and `C++17`, supports new contest rejudge interface, supports new contest pdf generation interface, supports problem lazy load that increases site rank and group loading time, supports new material preloader, supports new `sortable.js` as a sortable method, supports Carbon as time processer, supports 2 fonts and 5 environment variables. This version update also includes lots of query optimizations, bug fixes, functionality & UI improvements, and security updates.

### Update Logs
* **Compatibility:** NOJ now supports Laravel `8.5` per #672 requests, the latest version of Laravel. `8.x` uses Symphony 4, which introduced a lot of changes to the base code.
* **Compatibility:** NOJ now deprecates **DomPDF** and uses **WKHTMLPDF** instead.
* **Compatibility:** NOJ now uses `lax` for the same site verification.
* **Compatibility:** NOJ now uses `Throwable` instead of `Exception` for the error handler.
* **Compatibility:** NOJ now uses Laravel Mix `6.0`.
* **Compatibility:** NOJ now separate log channels, `group_elo_update` and `babel_judge_sync` are now stored in different files apart from `app.log` (which originally named `laravel.log`) and expired after 7 days. 
* **New:** Group homework support per #667 requests, this feature can be seen as the privatized lite version of **NOJ Dojo**.
* **New:** Add `createHomework` AJAX API.
* **New:** Use the third edition of the NOJ logo materialized design per #676 requests. While replacing all old logos, the third edition also modifies its dark and light flattened variant.
* **New:** Strong password support via environment variable `FUNC_STRONG_PASSWORD`.
* **New:** Add `C11` language support per #663 requests (require NOJ JudgeServer `v0.2.1` or higher).
* **New:** Add `C++14` language support per #663 requests (require NOJ JudgeServer `v0.2.1` or higher).
* **New:** Add `C++17` language support per #663 requests (require NOJ JudgeServer `v0.2.1` or higher).
* **New:** Add new problem selector component per #664 requests.
* **New:** Remastered contest rejudge feature per #673 requests. This function now opt-in for the beta test.
* **New:** Contest rejudge now supports custom verdict to rejudge.
* **New:** Remastered contest pdf generation feature by using **WKHTMLPDF** per #670 requests. This function now opt-in for the beta test and can only apply to contests in which all problems are self-hosted and you need to install **WKHTMLPDF** first then setup 2 environment variables, `WKHTML_PDF_BINARY` and `WKHTML_IMG_BINARY`.
* **New:** Contest pdf generation now supports math formula rendering.
* **New:** Contest pdf generation now supports removing after generation.
* **New:** Add site-wide `delayProblemLoad` function for image lazy loading.
* **New:** NOJ now uses the new material preloader component.
* **New:** NOJ now uses `sortable.js` for sortable elements.
* **New:** Add group left-to-right layout.
* **New:** Add `users_latest_submission` query builder for problem model.
* **New:** Add `problems_latest_submission` query builder for user model.
* **New:** NOJ now uses `lluminate\Support\Carbon` and its alias `Carbon` for time processing.
* **New:** Add `fonts-asset/simsun` package, see #504.
* **New:** Add `fonts-asset/dejavu` package, see #504.
* **New:** Add `barryvdh/laravel-snappy` package.
* **New:** Add `_declaration` as an scss component.
* **New:** Add `_mathjax` as an scss component.
* **New:** Add `_refreshing` as an scss component.
* **New:** Add `defaultAvatarPNG` as `NOJVariables` in typescript.
* **New:** PDF generation locale support for contest admin panel.
* **New:** Group homework locale support.
* **Deprecated:** Remove `EloquentModel` alias for all eloquent models from now on, eloquent models will be referred to directly.
* **Deprecated:** Remove `barryvdh/laravel-dompdf` package.
* **Deprecated:** Remove `jquery-ui` and `noj-jquery-ui-sortable` package.
* **Fixed:** Duplicate `lodash` package.
* **Fixed:** A bug causing `babel:install` to fail when making directories.
* **Fixed:** A bug causing email verification hidden when social login is disabled.
* **Fixed:** A bug causing congratulation and other dialogs dismissable via ESC key while exit logic not triggered, see #722.
* **Fixed:** A bug causing math formula overflow, see #723.
* **Fixed:** A bug causing sample note blank, see #662.
* **Fixed:** A bug causing admin portal contest registant_type field mapped wrongly.
* **Fixed:** A bug causing SPJ uploading to fail.
* **Fixed:** Typo (we mean it).
* **Improved:** NOJ Dojo is now greatly optimized by using the new dojo status method, see #725.
* **Improved:** Optimized site rank page performance, see #726.
* **Improved:** Optimized group detail page performance, see #728.
* **Improved:** Multidomain config now does not take effect in the console environment.
* **Improved:** Eloquent contest model `getProblemSet` method now optimized.
* **Improved:** PDF generation advice page now uses pure latex formula.
* **Improved:** PDF generation cover page now uses the latest ICPC standard.
* **Improved:** Contest admin panel now separates section-panel to own blade template.
* **Improved:** Problem pagination is now configurable via environment variable `PAGINATION_PROBLEM_PER_PAGE`.
* **Improved:** NOJ now uses blade stack to replace outdated `additionJS` yield.
* **Improved:** NOJ ico file now bundles `16*16`, `48*48` and `256*256` resolution.
* **Improved:** NOJ now uses a new refreshing button design.
* **Improved:** NOJ now uses the new problem selector component for contest editing and arranging per #664 requests.
* **Improved:** Redesign `ajax/problemExists` AJAX API.
* **Improved:** Group function block now has a color scheme.
* **Improved:** Group left-to-right layout now applies to all group detail-related pages.
* **Improved:** NOJ now uses the new left-to-right layout for the analysis page, see #719.
* **Improved:** Eloquent problem model `getProblemStatus` method.
* **Improved:** NOJ now uses the dot-separated router for problems.
* **Improved:** **Google Recaptcha** now defaults to false.
* **Improved:** Admin Portal now displays babel mirror as part of the environment info.
* **Security:** `beyondcode/laravel-dump-server` is now at `1.7.0`.
* **Security:** `dragonmantank/cron-expression` is now at `3.1.0`.
* **Security:** `encore/laravel-admin` is now at `1.8.14`.
* **Security:** `filp/whoops` is now at `2.14.4`.
* **Security:** `graham-campbell/markdown` is now at `13.1.1`.
* **Security:** `intervention/image` is now at `2.7.0`.
* **Security:** `laravel/framework` is now at `8.63.0`.
* **Security:** `laravel/passport` is now at `10.1.3`.
* **Security:** `laravel/socialite` is now at `5.2.5`.
* **Security:** `laravel/tinker` is now at `2.6.2`.
* **Security:** `laravelium/sitemap` is now at `8.0.1`.
* **Security:** `league/mime-type-detection` is now at `1.8.0`.
* **Security:** `mockery/mockery` is now at `1.4.4`.
* **Security:** `monolog/monolog` is now at `2.3.5`.
* **Security:** `nesbot/carbon` is now at `2.53.1`.
* **Security:** `nikic/php-parser` is now at `4.13.0`.
* **Security:** `nunomaduro/collision` is now at `5.10.0`.
* **Security:** `phar-io/manifest` is now at `2.0.3`.
* **Security:** `phar-io/version` is now at `3.1.0`.
* **Security:** `phpdocumentor/type-resolver` is now at `1.5.1`.
* **Security:** `phpoption/phpoption` is now at `1.8.0`.
* **Security:** `phpseclib/phpseclib` is now at `3.0.10`.
* **Security:** `phpspec/prophecy` is now at `1.14.0`.
* **Security:** `phpunit/php-code-coverage` is now at `9.2.7`.
* **Security:** `phpunit/php-file-iterator` is now at `3.0.5`.
* **Security:** `phpunit/php-text-template` is now at `2.0.4`.
* **Security:** `phpunit/php-timer` is now at `5.0.3`.
* **Security:** `phpunit/phpunit` is now at `9.5.10`.
* **Security:** `predis/predis` is now at `1.1.9`.
* **Security:** `psy/psysh` is now at `0.10.8`.
* **Security:** `ramsey/uuid` is now at `4.2.3`.
* **Security:** `sebastian/code-unit-reverse-lookup` is now at `2.0.3`.
* **Security:** `sebastian/comparator` is now at `4.0.6`.
* **Security:** `sebastian/diff` is now at `4.0.4`.
* **Security:** `sebastian/environment` is now at `5.1.3`.
* **Security:** `sebastian/exporter` is now at `4.0.3`.
* **Security:** `sebastian/global-state` is now at `5.0.3`.
* **Security:** `sebastian/object-enumerator` is now at `4.0.4`.
* **Security:** `sebastian/object-reflector` is now at `2.0.4`.
* **Security:** `sebastian/recursion-context` is now at `4.0.4`.
* **Security:** `sebastian/resource-operations` is now at `3.0.3`.
* **Security:** `sebastian/version` is now at `3.0.2`.
* **Security:** `symfony/console` is now at `5.3.7`.
* **Security:** `symfony/dom-crawler` is now at `5.3.7`.
* **Security:** `symfony/error-handler` is now at `5.3.7`.
* **Security:** `symfony/event-dispatcher` is now at `5.3.7`.
* **Security:** `symfony/event-dispatcher-contracts` is now at `2.4.0`.
* **Security:** `symfony/finder` is now at `5.3.7`.
* **Security:** `symfony/http-foundation` is now at `5.3.7`.
* **Security:** `symfony/http-kernel` is now at `5.3.9`.
* **Security:** `symfony/mime` is now at `5.3.8`.
* **Security:** `symfony/process` is now at `5.3.7`.
* **Security:** `symfony/routing` is now at `5.3.7`.
* **Security:** `symfony/translation` is now at `5.3.9`.
* **Security:** `symfony/var-dumper` is now at `5.3.8`.
* **Security:** `vlucas/phpdotenv` is now at `5.3.1`.
* **Security:** `webpack` is now at `5.52.1`.
* **Security:** `typescript` is now at `4.4.3`.
* **Security:** `sass` is now at `1.42.1`.
* **Security:** `pdfobject` is now at `2.2.7`.
* **Security:** `codemirror` is now at `5.63.1`.
* **Security:** `axios` is now at `0.21.4`.
* **Security:** `dompurify` is now at `2.3.2`.
* **Security:** `postcss` is now at `8.3.9`.

## v0.16.0 Bryconinae - 2021-08-27
This is a minor version update. As mentioned in `0.5.0` logs, the new version system would merge the old major and minor version numbers into new minor version numbers, thus as the 16th minor version update since NOJ `0.1.0`, this version would be numbered as major version `0`, minor version `16`, build pack `0` and patch number `0`.

**Important:** Please follow NOJ Document's guide to upgrading your NOJ from `v0.5.x` to `v0.16.0`.

**Summary:** Requires at least Chrome 69 or equivalent to support `NOJ 0.16.0`, requires `npm` package management from now on, supports Webpack and Laravel Mix, supports native VSCode coding experiences by supporting 24 VSCode grammars, configs, and 27 themes, supports partial update without test cases in the problem admin portal, supports test case input/output CRLF conversion, supports filters for the problem of admin portal, support image zoom when inside editor, support dynamic content loading to increase performance on admin portal, supports Poppins font, support carousels and dojo pass record admin portal, supports large test cases import up to 200MB. This version update also includes lots of bug fixes, functionality & UI improvements, and security updates.

### Update Logs
* **Compatibility:** NOJ now requires **at least** Chrome 69, Firefox 62, or Safari 13.1, NOJ recommends using modern browsers for better *ECMA2015* and *WebAssembly* support.
* **Compatibility:** From now on, every NOJ upgrade requires running `npm ci` and `npm run production`, for more information, see NOJ Documentation.
* **Compatibility:** From now on, NOJ will record all changes in `CHANGELOG.md`.
* **New:** NOJ now uses `npm` to track most of the npm modules, original `composer` packages `npm-asset/*` are mostly removed but still remaining some.
* **New:** NOJ now uses TypeScript and SCSS for static resources coding, some old JS and CSS codes still remaining.
* **New:** NOJ now uses `Webpack` to compile TypeScript and SCSS resources and pack them into JS and CSS bundle per #636 requests.
* **New:** NOJ now uses `Laravel Mix` to interacting with `Webpack`, all static resources except MathJax and fonts are now packed into a bundle per #636 requests.
* **New:** NOJ now supports `vscode-oniguruma` and `vscode-textmate`.
* **New:** NOJ now supports native VSCode coding experiences by supporting VSCode grammars and configs per #637 requests.
* **New:** NOJ Editor now supports grammars and configurations of the following **24** languages: `Plain Text`, `Python`, `C`, `C++`, `CUDA C++`, `C#`, `Kotlin`, `CSS`, `HTML`, `JavaScript`, `PHP`, `Java`, `Go`, `Haskell`, `Elixir`, `Ruby`, `Rust`, `Swift`, `Erlang`, `Racket`, `Scala`, `TypeScript`, `Visual Basic` and `Pascal`, acutal useable languages depend on each of the settings of the BABEL Extensions.
* **New:** NOJ now supports Native VSCode Coding Experiences by supporting VSCode Themes per #637 requests.
* **New:** NOJ Editor now supports the following **27** themes: `Default`, `Default (White)`, `Default (High Contrast)`, `Abyss`, `Chrome DevTools`, `Dracula`, `GitHub`, `GitHub Dark`, `Kimbie Dark`, `Material Design`, `Monokai Pro`, `Monokai Classic`, `One Half Dark`, `One Half Light`, `Quiet Light`, `Red`, `Solarized Dark`, `Solarized Light`, `SynthWave 84`, `Tomorrow Night Blue`, `Tomorrow Night Bright`, `Tomorrow Night Eighties`, `Tomorrow Night`, `Tomorrow`, `Visual Studio`, `Visual Studio (White)` and `Winter is Coming`.
* **New:** Admin Portal no longer requires test cases uploaded when editing per #628 requests.
* **New:** NOJ now uses `laravel-admin-asset` composer package type for admin assets.
* **New:** NOJ now uses `noj-language-services` composer package type for NOJ language services supports per #637 requests.
* **New:** NOJ now uses `NOJVariables` for unknownfileSVG and consoleSVG support.
* **New:** NOJ applies `CRLF` to `LF` conversion to input and output files of test cases.
* **New:** NOJ now supports env `ADMIN_RECAPTCHAV2_TIMEOUT` for **Google ReCaptcha v2** timeout settings.
* **New:** NOJ now has a locale for **Google ReCaptcha**.
* **New:** Arrange contest dialog now have a proper warning when submit fields are empty.
* **New:** Add proper eloquent bindings for OJ and Problem models.
* **New:** Port error messages to `403` and `404` errors view.
* **New:** Add filters for the problem of Admin Portal per #630 requests.
* **New:** Add image zoom when inside editor per #651 requests.
* **New:** Add Admin Portal API for dynamic content loading.
* **New:** Admin Portal now doesn't load all problems or users when creating, editing, or filtering per #643 requests.
* **New:** Admin Portal now adds unit label for time and memory limit per #642 requests.
* **New:** Update `fonts-asset/MDI` package, see #504.
* **New:** Add `fonts-asset/poppins` package, see #504.
* **New:** NOJ now uses font `poppins` on certain places of the website.
* **New:** NOJ now has icons for general layout.
* **New:** Styles now have version number added.
* **New:** Carousel model now proceeds image link in `saving()` stage.
* **New:** Carousels support for Admin Portal.
* **New:** Carousels locale support for Admin Portal.
* **New:** Dojo Pass Records support for Admin Portal per #646 requests.
* **New:** Dojo Pass Records locale support for Admin Portal.
* **New:** NOJ now has a `temp` disk.
* **New:** NOJ Admin Portal problem test cases import now supports large file upload up to `10GB` per #639 requests, as a beta function it is now limited to `200MB`.
* **New:** Add `njuptaaa/noj-large-file-upload` package.
* **New:** Add `njuptaaa/laravel-admin-assets` package.
* **New:** Add `noj-language-service/configurations` package.
* **New:** Add `noj-language-service/grammars` package.
* **New:** Add `noj-language-service/themes` package.
* **New:** Add `npm-asset/vscode-oniguruma` package.
* **Deprecated:** NOJ no longer uses `vue` in typescript.
* **Deprecated:** NOJ no longer use `public/css` and `public/js` folders.
* **Deprecated:** NOJ no longer uses `public/static/css` and `public/static/js` folders for static resources coding, they are now compiling only.
* **Deprecated:** NOJ no longer use `js`, `json`, `css` and `html` workers.
* **Deprecated:** NOJ Web Installer no longer supported.
* **Deprecated:** NOJ Editor no longer supports the `toggleHCTheme` command, now use `hc-black` themes instead.
* **Deprecated:** NOJ no longer tracks the `public/vendor` folder, all static resources are now loaded via composer package management.
* **Deprecated:** NOJ no longer tracks the `public/svg` folder since the new errors page doesn't require them anymore.
* **Deprecated:** NOJ no longer uses `js.common.vscode` blade components, now use `components.vscode` blade components instead.
* **Deprecated:** NOJ no longer uses `layouts.errors` blade template, now use `errors.general` blade template instead.
* **Deprecated:** NOJ no longer uses `fileicon-svg` npm package and `npm-asset/fileicon-svg` composer package, now use `font-asset/fileicon` composer package instead.
* **Deprecated:** Remove `njuptaaa/moss` package.
* **Deprecated:** NOJ Admin Portal no longer supports BABEL commands on browser interface, use actual console instead.
* **Fixed:** A bug causing rank table reversed.
* **Fixed:** A bug causing group cannot pick the same gcode when editing per #644 requests.
* **Fixed:** A bug causing error messages resolved while continuing to execute codes.
* **Fixed:** A bug causing group editing resulting in a duplicate `null` leader per #629 requests.
* **Fixed:** A bug causing zip file with 0 to validate test cases proceed without error returned.
* **Fixed:** A bug causing zip file input test case file filter not working.
* **Fixed:** A bug causing exception pages Auth middleware returns `null`.
* **Fixed:** Typo (*cliche isn't it*).
* **Fixed:** A bug causing user profile return `500` when encountered outbound network restrictions.
* **Fixed:** NOJ no longer tracks `mix-manifest.json`.
* **Fixed:** A missing en locale in problem solution.
* **Fixed:** A misplaced en locale in the rank filter.
* **Fixed:** A potential divide by 0 bugs.
* **Fixed:** A Dojo Admin Portal filter bug.
* **Improved:** Admin Portal now uses a switch to replace select in problem `hide` gird field and submissions `share` gird field.
* **Improved:** Contest problems now sort via `number` instead of `ncode`.
* **Improved:** User model now has `readable_name` attribute.
* **Improved:** Carousel now sorted by `updated_at`.
* **Improved:** NOJ now uses `Auth::guard('web')` to specify guard on certain conditions.
* **Improved:** Highlight.js now uses `vs` as the default light theme and `atom-one-dark` as the default dark theme.
* **Improved:** Composer no longer sorts packages alphabetically, this helps when we want to prioritize `composer-installers-extender`.
* **Improved:** Admin Portal layout improved.
* **Improved:** NOJ now uses app name for Open Search Configs.
* **Improved:** NOJ now limits `pcode` and `scode` format.
* **Improved:** Modify console info style.
* **Security:** `monaco-editor` is now at `0.25.2`.
* **Security:** `asundust/auth-captcha` is now at `2.0.14`.
* **Security:** `guzzlehttp/guzzle` is now at `7.3.0`.
* **Security:** `highlight.js` is now at `11.2.0`.
* **Security:** `axios` is now at `0.21.1`.
* **Security:** `x3zvawq/noj_simplemde` is now at `1.0.1`.
* **Security:** `zsgsdesign/noj-admin-clike-editor` is now at `1.0.0`.
* **Security:** `dompurify` is now at `2.3.1`.
* **Security:** `marked` is now at `0.7.0`.

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

## v0.5.0 Aphyocharacinae - 2021-08-14

This is a minor version update, since this version former build version updates would now ascend to minor version updates, and former patches indexed as a patch number together with hash would be referred to as build version updates.

**Important:** Please follow NOJ Document's guide to upgrading your NOJ from `v0.4.2` to `v0.5.0`.

**Summary:** Add PHP 7.4 & NOJ_JudgeServer v0.2.1 compatibility support, compilers for `Go`, `C#`, `Rust`, `Ruby`, `Haskell`, `Free Pascal`, `Text` and `Free Basic`, OpenJudge NOI BABEL extension support, code plagiarism check beta support, scroll board beta support, compiler info highlight and dialog support, editor themes support, NOJ themes support, image hosting service support, user permissions support, system settings support, AAuth Socialite support, localization support and a brunch of Admin Portal functional update, dozens of new environment configurations and 3 helper functions. This version update also includes lots of bug fixes, functionality & UI improvements, and security updates.

### Update Logs
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
