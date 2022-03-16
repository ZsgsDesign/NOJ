<?php

namespace App\Utils\Contest\Rules;

class Rule
{
    /**
     * Whether or not supports partial score. If set to `true`, supports `Partially Accepted` verdict.
     * Note that supports `Partially Accepted` verdict basically means all other verdicts like `Wrong Answer` would be disabled.
     */
    public bool $partial_score = false;

    /**
     * Whether or not returns verdict to participants. If set to `false`, return verdicts as `Judged`.
     */
    public bool $return_verdicts = true;

    /**
     * Define some verdicts to behave contradicts `$return_verdicts` settings.
     * For example, set to `['Compile Error']` and `Compile Error` would return to users as `Judged` even if `$return_verdicts` are set to `true`.
     * For example, set to `['Accepted', 'Wrong Answer']` and `Accepted` would return to users as `Judged` even if `$return_verdicts` are set to `false`.
     */
    public array $return_verdicts_exception = [];

    /**
     * Whether or not participants can view rankboard during contest. If set to `false`, only admin can view rankboard.
     * Note that it differs from contest `show_rankboard` attribute, which defines whether or not users can view rankboard **afterwards**.
     */
    public bool $participants_rankboard = true;

    /**
     * Which of the verdicts are viewed as solved.
     */
    public array $solved_verdicts = ['Accepted'];

    /**
     * Whether or not applies penalty system.
     */
    public bool $penalty = false;

    /**
     * Interval value for each penalty. ICPC uses 1200 seconds for example.
     * Unit: seconds.
     */
    public int $penalty_interval = 1200;

    /**
     * Define some verdicts to skip penalty calculation.
     */
    public array $penalty_verdicts_exception = ['Compile Error', 'color:black', 'color:blue'];

    /**
     * Whether or not ignores submission penalty calculation after Accepted submissions.
     */
    public bool $penalty_ignores_after_ac = true;

    /**
     * Which fields are to be displayed, can be picked from `[tot_score, solved, penalty]`.
     */
    public array $rankboard_general_display = [];

    /**
     * Which detail fields are to be displayed on header, can be picked from `[solved_count_vs_submission_count]`.
     */
    public array $rankboard_detail_header_display = [];

    /**
     * Which detail fields are to be displayed on ceils, can be picked from `[first_solved_at, penalty, tries, score]`.
     * Note that the first item would be green and have highlighted varients.
     */
    public array $rankboard_detail_content_display = [];

    /**
     * Whether or not sorts rankboard. If set to `false`, sort would be based on usernames.
     */
    public bool $rankboard_sort = false;
}
