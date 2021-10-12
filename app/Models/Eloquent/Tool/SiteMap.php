<?php

namespace App\Models\Eloquent\Tool;

use App\Models\Eloquent\Problem;
use App\Models\Eloquent\Contest;
use App\Models\Eloquent\Group;
use Carbon;
use App;

class SiteMap
{
    public static function generate()
    {
        $sitemap=App::make("sitemap");

        $sitemap->add(route('home'), Carbon::now(), '1.0', 'daily');
        $sitemap->add(route('problem.index'), Carbon::now(), '1.0', 'daily');
        $sitemap->add(route("status.index"), Carbon::now(), '1.0', 'daily');
        $sitemap->add(route("contest.index"), Carbon::now(), '1.0', 'daily');
        $sitemap->add(route("group.index"), Carbon::now(), '1.0', 'daily');

        Problem::chunk(200, function($problems) use ($sitemap) {
            foreach ($problems as $problem) {
                $sitemap->add(route('problem.detail', [
                    'pcode' => $problem->pcode
                ]), Carbon::parse($problem->update_date), '0.8', 'monthly');
            }
        });

        Contest::where(["public" => 1, "audit_status" => 1])->chunk(200, function($contests) use ($sitemap) {
            foreach ($contests as $contest) {
                $sitemap->add(route('contest.detail', [
                    'cid' => $contest->cid
                ]), Carbon::parse($contest->created_at), '0.8', 'monthly');
            }
        });

        Group::where(["public" => 1])->chunk(200, function($groups) use ($sitemap) {
            foreach ($groups as $group) {
                $sitemap->add(route('group.detail', [
                    'gcode' => $group->gcode
                ]), Carbon::parse($group->created_at), '0.8', 'monthly');
            }
        });

        $sitemap->store('xml', 'sitemap');
    }
}
