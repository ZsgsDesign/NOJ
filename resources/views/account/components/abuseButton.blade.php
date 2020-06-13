<style>
    shadow-button.btn-group{
        position: absolute;
        top: .5rem;
        right: 1.5rem;
        z-index: 2;
        margin: 0;
    }
    shadow-button .btn::after{
        display: none;
    }
    shadow-button .btn{
        color:#fff!important;
        border-radius: 100%!important;
        padding: .5rem!important;
        line-height: 1!important;
        font-size: 1.5rem!important;
    }
    shadow-button .dropdown-item > i {
        display: inline-block;
        transform: scale(1.5);
        padding-right: 0.5rem;
        color: rgba(0,0,0,0.42);
    }

    shadow-button.btn-group .dropdown-menu {
        border-radius: .125rem;
    }

    shadow-button .dropdown-item {
        flex-wrap: nowrap!important;
    }
</style>
<shadow-button class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="MDI dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <button class="dropdown-item wemd-red-text" onclick="reportAbuse()"><i class="MDI alert-circle wemd-red-text"></i> {{__('dashboard.reportAbuse')}}</button>
    </div>
</shadow-button>

@include("js.common.abuse",[
    'category' => 'user',
    'subject_id' => $info["uid"]
])
