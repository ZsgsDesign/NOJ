<style>
    material-preloader > div > div {
        position: relative;
        margin: 0 auto;
        width: 6rem;
    }

    material-preloader > div > div:before {
        content: "";
        display: block;
        padding-top: 100%;
    }

    material-preloader > div > p {
        margin: 1rem 0 0;
        /* font-weight: 300; */
        text-align: center;
    }

    material-preloader > div > div > svg {
        -webkit-animation: preloader-rotate 2s linear infinite;
                animation: preloader-rotate 2s linear infinite;
        height: 100%;
        transform-origin: center center;
        width: 100%;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }

    material-preloader > div > div > svg > circle {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0;
        -webkit-animation: preloader-dash 1.5s ease-in-out infinite, preloader-color 6s ease-in-out infinite;
                animation: preloader-dash 1.5s ease-in-out infinite, preloader-color 6s ease-in-out infinite;
        stroke-linecap: round;
    }

    material-preloader {
        display: flex;
        z-index: 999;
        position: fixed;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        justify-content: center;
        align-items: center;
        background: #fafafa;
        opacity: 1;
    }

    material-preloader.loaded{
        transition: .2s ease-out .0s;
        pointer-events: none;
        opacity: 0;
    }

    @-webkit-keyframes preloader-rotate {
        100% {
        transform: rotate(360deg);
        }
    }

    @keyframes preloader-rotate {
        100% {
        transform: rotate(360deg);
        }
    }

    @-webkit-keyframes preloader-dash {
        0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0;
        }
        50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px;
        }
        100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px;
        }
    }

    @keyframes preloader-dash {
        0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0;
        }
        50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px;
        }
        100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px;
        }
    }

    @-webkit-keyframes preloader-color {
        100%, 0% {
        stroke: #d62d20;
        }
        40% {
        stroke: #0057e7;
        }
        66% {
        stroke: #008744;
        }
        80%, 90% {
        stroke: #ffa700;
        }
    }

    @keyframes preloader-color {
        100%, 0% {
        stroke: #d62d20;
        }
        40% {
        stroke: #0057e7;
        }
        66% {
        stroke: #008744;
        }
        80%, 90% {
        stroke: #ffa700;
        }
    }
</style>
<material-preloader>
    <div>
        <div>
            <svg viewBox="25 25 50 50">
                <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
            </svg>
        </div>
        <p>{{__('splash.loading', ['name' => config("app.name")])}}</p>
    </div>
</material-preloader>
<script>
    var startLoadingTimestamp=Date.now();
    var endLoadingTimestamp=startLoadingTimestamp;
</script>
