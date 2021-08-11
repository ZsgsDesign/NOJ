@extends('layouts.app')

@section('template')

<style>
    .form-control:focus,
    .form-control:hover {
        border-bottom-width: 2px;
    }

    form .form-group:last-of-type {
        margin-bottom: 0;
    }

    .alert>p {
        margin-bottom: 0;
    }

    .card {
        margin-bottom: 20vh;
        overflow: hidden;
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    .card .card-header {
        padding: 0;
    }

    .card .card-header>ul {
        margin: 0;
    }

    .card .card-header>ul .nav-link {
        padding: 1rem;
        border: none!important;
    }

    .card .card-header .nav-tabs .nav-link.active {
        color: #ff4081;
    }

    .nav-tabs-material .nav-tabs-indicator {
        background-color: #ff4081;
        bottom: -1px;
        display: block;
        width: 50%;
        height: .15rem;
        position: absolute;
        transition: .2s ease-out .0s;
    }

    #accountTab {
        position: relative;
    }

    .card-footer {
        border: none;
    }

    .checkbox {
        margin-top: 1rem;
    }

    form {
        margin-bottom: 0;
    }

    input {
        box-shadow: none!important;
    }

    .was-validated input[type="checkbox"].form-control:invalid+span+span {
        color: #f44336!important;
    }

    label[for="agreement"] {
        display: inline-block;
    }

    .card-bottom{
        background: linear-gradient(0deg, #f4f4f4, #efefef);
        padding:10px;
    }

    .socialite-section a{
        color: inherit;
        text-decoration: none;
        margin: 0 0.5rem;
    }

    .socialite-section button{
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .socialite-section button i{
        font-size: 2rem;
    }
</style>
<div class="container mundb-standard-container">
    <div class="row justify-content-sm-center">
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="text-center" style="margin-top:10vh;margin-bottom:20px;">
                <h1 style="padding:20px;display:inline-block;">{{config("app.name")}}</h1>
                <p>{{__("account.slogan", ["name" => config("app.name")])}}</p>
            </div>
            <div class="card">
                @if(config("function.register"))
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs nav-justified nav-tabs-material" id="accountTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">{{__("Login")}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">{{__("Register")}}</a>
                        </li>
                        <div class="nav-tabs-indicator" id="nav-tabs-indicator" style="left: 0px;"></div>
                    </ul>
                </div>
                @endif
                <div class="tab-content" id="accountTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <form class="needs-validation" action="{{ route('login') }}" method="post" id="login_form" novalidate>
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="email" class="bmd-label-floating">{{__("E-Mail Address")}}</label>
                                    <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" id="email" required>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password" class="bmd-label-floating">{{__("Password")}}</label>
                                    <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" required>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="remember"><input class="form-control" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}><span>{{ __('Remember Me') }}</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('password.request') }}"><button type="button" class="btn btn-secondary">{{__("account.forgetpassword")}}</button></a>
                                <button type="submit" class="btn btn-danger">{{__("Login")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                @unless(blank($socialites))
                    <div class="text-center card-bottom socialite-section">
                        @foreach($socialites as $socialite)
                            <a href="{{route("oauth.$socialite.index")}}"><button class="btn btn-dark bmd-btn-icon"><i class="socialicon {{$socialite}}-circle colored"></i></button></a>
                        @endforeach
                    </div>
                @endunless
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {
        $('#login-tab').on('click', function (e) {
            e.preventDefault();
        })
        $('#register-tab').on('click', function (e) {
            e.preventDefault();
            location.href="/register";
        })

        $('input:-webkit-autofill').each(function(){
            if ($(this).val().length !== "") {
                console.log($(this).siblings('label'));
                $(this).siblings('label').addClass('active');
                $(this).parent().addClass('is-filled');
            }
        });

    }, false);

</script>

@endsection
