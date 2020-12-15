<style>
canvas#easter_egg {
	display:block;
	box-shadow: -2px -2px 2px #EFEFEF, 5px 5px 5px #B9B9B9;
}

#easter_egg_reset {
    margin-top: 2rem;
}

easter-egg{
    display:none;
    justify-content: center;
    flex-direction: column;
    margin-top: 2rem;
    margin-bottom: 2rem;
}
</style>

<easter-egg>
    <canvas id="easter_egg" width="450px" height="450px">{{__('errors.alphacome.notsupported')}}</canvas>
    <div class="text-center"><button class="btn btn-outline" id="easter_egg_reset">{{__('errors.alphacome.restart')}}</button></div>
</easter-egg>

<script>
    var chess = document.getElementById('easter_egg');
    var reset = document.getElementById("easter_egg_reset");
    var logo = new Image();
    logo.src = '/static/img/icon/icon-white.png';
    var context;
    var me;
    var isAvailable;
    var winTactics;
    var winCount;
    var myWin;
    var computerWin;
    var over;

    function init(){
        me = true;
        isAvailable = new Array(15);
        winTactics = [];
        winCount = 0;
        myWin = [];
        computerWin = [];
        over = false;

        context = chess.getContext('2d');
        context.clearRect(0,0,450,450);
        context.drawImage(logo,0,0,450,450);
        drawChessBoard();
        for(var i=0; i<15; i++){
            isAvailable[i] = new Array(15);
        }
        computeWinTactics();
    }

    function drawChessBoard(){
        context.strokeStyle = '#BFBFBF';
        for (var i = 0; i < 15; i++) {
            context.moveTo(15+30*i,15);
            context.lineTo(15+30*i,435);
            context.stroke();

            context.moveTo(15, 15+30*i);
            context.lineTo(435,15+30*i);
            context.stroke();
        };
    }

    function computeWinTactics() {
        var i, j, k;
        for(i=0;i<15;i++){
            winTactics[i] = [];
            for(j=0;j<15;j++){
                winTactics[i][j] = [];
            }
        }
        for(i=0;i<15;i++){
            for(j=0;j<11;j++){
                for(k=0;k<5;k++){
                    winTactics[i][j+k][winCount] = true;
                }
                winCount++;
            }
        }
        for(i=0;i<15;i++){
            for(j=0;j<11;j++){
                for(k=0;k<5;k++){
                    winTactics[j+k][i][winCount] = true;
                }
                winCount++;
            }
        }
        for(i=0;i<11;i++){
            for(j=0;j<11;j++){
                for(k=0;k<5;k++){
                    winTactics[i+k][j+k][winCount] = true;
                }
                winCount++;
            }
        }

        for(i=0;i<11;i++){
            for(j=4;j<15;j++){
                for(k=0;k<5;k++){
                    winTactics[i+k][j-k][winCount] = true;
                }
                winCount++;
            }
        }
        for (i = 0; i < winCount; i++) {
            myWin[i] = 0;
            computerWin[i] = 0;
        };
    }

    function oneStep(i, j){
        var x = 15 + i * 30 ;
        var y = 15 + j * 30 ;
        context.lineWidth=0;
        context.beginPath();
        context.arc(x,y,13,0,2*Math.PI);
        context.closePath();
        var gradient = context.createRadialGradient(x+2,y-2,13,x+2,y-2,0);
        if(me){
            gradient.addColorStop(0,"#0A0A0A");
            gradient.addColorStop(1,"#636766");
            isAvailable[i][j] = 1;
        }
        else{
            gradient.addColorStop(0,"#D1D1D1");
            gradient.addColorStop(1,"#F9F9F9");
            isAvailable[i][j] = 2;
        }
        me = !me;
        context.fillStyle = gradient;
        context.fill();
    }

    logo.onload = function(){
        init();
    }

    reset.onclick = function(){
        init();
    }

    chess.onclick = function(event){
        if( over ){
            return;
        }
        var x = event.offsetX;
        var y = event.offsetY;
        var i = Math.round((x-15)/30);
        var j = Math.round((y-15)/30);
        if( isAvailable[i][j] ){
            alert("{{__('errors.alphacome.illegal.desc')}}","{{__('errors.alphacome.illegal.title')}}","blur");
            return;
        }
        oneStep(i, j);
        for (var k = 0; k < winCount; k++) {
            if(winTactics[i][j][k]){
                myWin[k]++;
                computerWin[k] = 6;
                if(myWin[k]==5){
                    setTimeout(function(){
                        alert("{{__('errors.alphacome.lose.desc')}}","{{__('errors.alphacome.lose.title')}}","blur-off");
                    },300);
                    over = true;
                }
            }
        };
        if(!over){
            computerStep();
        }
    }

    function computerStep() {
        var u=0,v=0,maxScore=0;
        var myScore, computerScore;
        for (var i = 0; i < 15; i++) {
            for (var j = 0; j < 15; j++) {
                if( isAvailable[i][j] ){
                    continue;
                }
                myScore = 0;
                computerScore = 0;

                for (var k = 0; k < winCount; k++) {
                    if(winTactics[i][j][k]){
                        if(myWin[k]==1){
                            myScore += 200;
                        }
                        else if(myWin[k]==2){
                            myScore += 400;
                        }
                        else if(myWin[k]==3){
                            if(computerWin[k]==1){
                                myScore += 1500;
                            }
                            else{
                                myScore += 4000;
                            }
                        }
                        else if(myWin[k]==4){
                            myScore += 6000;
                        }

                        if(computerWin[k]==1){
                            computerScore += 220;
                        }
                        else if(computerWin[k]==2){
                            computerScore += 1000;
                        }
                        else if(computerWin[k]==3){
                            computerScore += 3000;
                        }
                        else if(computerWin[k]==4){
                            computerScore += 20000;
                        }

                        if(myScore>maxScore){
                            u = i;
                            v = j;
                            maxScore = myScore;
                        }

                        if(computerScore>maxScore){
                            u = i;
                            v = j;
                            maxScore = computerScore;
                        }
                    }
                }
            }
        }
        oneStep(u,v);
        for (var k = 0; k < winCount; k++) {
            if(winTactics[u][v][k]){
                computerWin[k]++;
                myWin[k] = 6;
                if(computerWin[k]==5){
                    setTimeout(function(){
                        alert("{{__('errors.alphacome.win.desc')}}","{{__('errors.alphacome.win.title')}}","blur");
                    },300);
                    over = true;
                }
            }
        };
    }
</script>
