<style>
    .fireworks {
        height: 100vh;
        width: 100vw;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .fireworks .firework {
        position: relative;
        top: 0px;
        left: 0px;
        margin: 0px 50px;
    }

    .fireworks .firework:before {
        content: "";
        display: block;
        border-radius: 5px;
        background-color: skyblue;
        width: 5px;
        height: 0px;
        will-change: transform;
        transform: translateY(1000px);
        animation: fireworkstart 3s ease-out 1;

    }

    .fireworks .firework .explosion {
        position: absolute;
        top: 0;
        width: 5px;
        height: 20px;
        opacity: 0;
        z-index: 500;
        transform-origin: top center;
        will-change: transform;
        animation: explosion 0.7s linear 3s 1;
    }

    .fireworks .firework .explosion:nth-child(1) {
        transform: rotate(0deg);
    }

    .fireworks .firework .explosion:nth-child(2) {
        transform: rotate(90deg);
    }

    .fireworks .firework .explosion:nth-child(3) {
        transform: rotate(180deg);
    }

    .fireworks .firework .explosion:nth-child(4) {
        transform: rotate(-90deg);
    }

    .fireworks .firework .explosion:nth-child(5) {
        transform: rotate(45deg);
    }

    .fireworks .firework .explosion:nth-child(6) {
        transform: rotate(-45deg);
    }

    .fireworks .firework .explosion:nth-child(7) {
        transform: rotate(135deg);
    }

    .fireworks .firework .explosion:nth-child(8) {
        transform: rotate(225deg);
    }

    .fireworks .firework .explosion .spark {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        border-radius: 5px;
        will-change: transform;
        animation: explosion2 0.7s ease-in-out 3s 1;
    }


    .fireworks .firework .explosion .spark.red {
        background-color: #E91E63;
    }

    .fireworks .firework .explosion .spark.blue {
        background-color: skyblue;
    }

    .fireworks .firework .explosion .spark.green {
        background-color: limegreen;
    }

    .fireworks .firework .explosion .spark.yellow {
        background-color: yellow;
    }

    .fireworks .firework .explosion .spark.purple {
        background-color: purple;
    }

    @-webkit-keyframes fireworkstart {
        0% {
            height: 0px;
            transform: translateY(1000px);
        }

        50% {
            height: 50px;
        }

        75% {
            height: 30px;
        }

        100% {
            height: 0;
            transform: translateY(0);
        }
    }

    @keyframes fireworkstart {
        0% {
            height: 0px;
            transform: translateY(1000px);
        }

        50% {
            height: 50px;
        }

        75% {
            height: 30px;
        }

        100% {
            height: 0;
            transform: translateY(0);
        }
    }

    @-webkit-keyframes explosion {
        0% {
            height: 0px;
            opacity: 0;
        }

        50% {
            height: 25px;
            opacity: 1;
        }

        100% {
            height: 0px;
            opacity: 0;
        }
    }

    @keyframes explosion {
        0% {
            height: 0px;
            opacity: 0;
        }

        50% {
            height: 25px;
            opacity: 1;
        }

        100% {
            height: 0px;
            opacity: 0;
        }
    }

    @-webkit-keyframes explosion2 {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(5px);
        }

        75% {
            transform: translateY(50px);
        }

        100% {
            transform: translateY(70px);
        }
    }

    @keyframes explosion2 {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(5px);
        }

        75% {
            transform: translateY(50px);
        }

        100% {
            transform: translateY(70px);
        }
    }

    #congration-animation {
        position: fixed;
        z-index: 1000;
        top: 0;
    }

    .animation-playing-background {
        filter: blur(4px);
        transition-duration: 4s;
    }
</style>

<div id="congration-animation">

</div>

<script>
    function playCongratulation(background){
        $('#'+background).addClass('animation-playing-background');
        $('#congration-animation').html(`
    <div class="fireworks">
        <div class="firework">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework" style="margin-top: -70px">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework" style="margin-top: 70px">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework" style="margin-top: -70px">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
        <div class="firework">
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark red"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
            <div class="explosion">
                <div class="spark blue"></div>
            </div>
            <div class="explosion">
                <div class="spark green"></div>
            </div>
            <div class="explosion">
                <div class="spark yellow"></div>
            </div>
        </div>
    </div>
        `);
    }

    function cleanAnimation(background){
        $('#'+background).removeClass('animation-playing-background');
        $('#congration-animation').html('');
    }
</script>
