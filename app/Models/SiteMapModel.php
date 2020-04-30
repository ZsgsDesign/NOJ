<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class SiteMapModel extends Model
{
    public function __construct()
    {
        $sitemap=App::make("sitemap");

        $sitemap->add(URL::to(""), date("Y-m-d H:i:s"), '1.0', 'daily');
        $sitemap->add(URL::to("problem"), date("Y-m-d H:i:s"), '1.0', 'daily');
        $sitemap->add(URL::to("status"), date("Y-m-d H:i:s"), '1.0', 'daily');
        $sitemap->add(URL::to("contest"), date("Y-m-d H:i:s"), '1.0', 'daily');
        $sitemap->add(URL::to("group"), date("Y-m-d H:i:s"), '1.0', 'daily');

        $problems=DB::table('problem')->get()->all();
        foreach ($problems as $p) {
            $sitemap->add(URL::to("problem/{$p['pcode']}"), $p['update_date'], '0.8', 'monthly');
        }

        $contests=DB::table('contest')->where(["public"=>1, "audit_status"=>1])->get()->all();
        foreach ($contests as $c) {
            $sitemap->add(URL::to("contest/{$c['cid']}"), $c['created_at'], '0.8', 'monthly');
        }

        $groups=DB::table('group')->where(["public"=>1])->get()->all();
        foreach ($groups as $g) {
            $sitemap->add(URL::to("group/{$g['gcode']}"), $g['created_at'], '0.8', 'monthly');
        }

        $sitemap->store('xml', 'sitemap');
    }
}
