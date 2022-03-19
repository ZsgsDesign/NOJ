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

    a:hover {
        text-decoration: none;
    }

    .form-control.is-invalid {
        border-color: none;
        background-position: inherit;
        background-size: inherit;
        background-image: linear-gradient(0deg,#f44336 2px,rgba(0,150,136,0) 0),linear-gradient(0deg,rgba(0,0,0,.26) 1px,transparent 0);
    }

    @-webkit-keyframes autofill {
        to {
            background-color: transparent;
            background: no-repeat bottom,50% calc(100% - 1px);
            background-image: linear-gradient(0deg,#03a9f4 2px,rgba(3,169,244,1) 0),linear-gradient(0deg,rgba(3,169,244,1) 1px,transparent 0);
            background-size: 0 100%,100% 100%;
        }
    }

    input:-webkit-autofill {
        animation-name: autofill;
        animation-fill-mode: both;
    }
</style>

@if (config('recaptcha.enable.user.register'))
    {!! ReCaptcha::htmlScriptTagJsApi() !!}
@endif

<div class="container mundb-standard-container">
    <div class="row justify-content-sm-center">
        <div class="col-sm-12 col-md-8 col-lg-6">
            <div class="text-center" style="margin-top:10vh;margin-bottom:20px;">
                <h1 style="padding:20px;display:inline-block;">{{config("app.name")}}</h1>
                <p>{{__("account.slogan", ["name" => config("app.name")])}}</p>
            </div>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs nav-justified nav-tabs-material" id="accountTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="false">{{__("Login")}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="register-tab" data-toggle="tab" role="tab" aria-controls="register" aria-selected="true">{{__("Register")}}</a>
                        </li>
                        <div class="nav-tabs-indicator" id="nav-tabs-indicator" style="left: 50%;"></div>
                    </ul>
                </div>
                <div class="tab-content" id="accountTabContent">
                    <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <form class="needs-validation" method="POST" action="{{ route('register') }}" id="register_form">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name" class="bmd-label-floating">{{__("Name")}}</label>
                                    <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="register_nick_name" value="{{ old('name') }}" maxlength="16" required>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="email" class="bmd-label-floating">{{__("E-Mail Address")}}</label>
                                    <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="register_email" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password" class="bmd-label-floating">{{__("Password")}}</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="register_password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password" class="bmd-label-floating">{{__("Confirm Password")}}</label>
                                    <input type="password" name="password_confirmation" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="register_password_again" required>
                                </div>

                                @if (config('recaptcha.enable.user.register'))

                                    {!! ReCaptcha::htmlFormSnippet() !!}

                                    @error('g-recaptcha-response')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                @endif

                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="agreement"><input class="form-control" type="checkbox" name="agreement" id="agreement" required><span style="transition: .2s ease-out .0s;">{{__("account.agree")}} <a href="{{route('terms.user')}}" target="_blank">{{__("account.terms")}}</a></span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-danger">{{__("Register")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load",function() {
        $('#login-tab').on('click', function (e) {
            e.preventDefault();
            location.href="/login";
        })

        $('#register-tab').on('click', function (e) {
            e.preventDefault();
        })

        @if (config('recaptcha.enable.user.register'))
            $("#register_form").submit(function(event) {
                if ($("#g-recaptcha-response").val() === "") {
                    event.preventDefault();
                    alert("{{__('validation.recaptcha')}}", "ReCaptcha", "security");
                }
            });
        @endif

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
