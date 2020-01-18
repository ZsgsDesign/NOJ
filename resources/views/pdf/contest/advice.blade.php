<style>
    .advice p.advice-title{
        font-size: 1.4rem;
    }

    .advice li > p:first-of-type {
        margin-top: 0;
    }

    .advice ul.dashed {
        list-style-type: none;
    }

    .advice ul.dashed > li {
        text-indent: -5px;
    }

    .advice ul.dashed > li:before {
        content: "- ";
        text-indent: -5px;
    }
</style>
<div class="advice">
    <p class="advice-title">Advice, hints, and general information</p>
    <ul>
        <li>
            <p>Your submissions will be run multiple times, on several different input files. If your submission is incorrect, the error message you get will be the error exhibited on the first input file on which you failed. E.g., if your instance is prone to crash but also incorrect, your submission may be judged as either <code>wrong answer</code> or <code>run time error</code>, depending on which is discovered first.</p>
        </li>
        <li>
            <p>For problems with floating point output, we only require that your output is correct up to some error tolerance.</p>
            <p>For example, if the problem requires the output to be within either absolute or relative error of <span><img src="{{route('latex.png',['ltxsource'=>'$10^{-4}$'])}}" style="display: inline-block;" class="rendered-tex" width="40" height="18"></span>, this means that</p>
            <ul class="dashed">
                <li>If the correct answer is <img src="{{route('latex.png',['ltxsource'=>'$0.05$'])}}" style="display: inline-block;" class="rendered-tex" width="34" height="14">, any answer between <img src="{{route('latex.png',['ltxsource'=>'$0.0499$'])}}" style="display: inline-block;" class="rendered-tex" width="54" height="14"> and <img src="{{route('latex.png',['ltxsource'=>'$.0501$'])}}" style="display: inline-block;" class="rendered-tex" width="44" height="14"> will be accepted.</li>
                <li>If the correct answer is <img src="{{route('latex.png',['ltxsource'=>'$500$'])}}" style="display: inline-block;" class="rendered-tex" width="30" height="14">, any answer between <img src="{{route('latex.png',['ltxsource'=>'$499.95$'])}}" style="display: inline-block;" class="rendered-tex" width="56" height="14"> and <img src="{{route('latex.png',['ltxsource'=>'$500.05$'])}}" style="display: inline-block;" class="rendered-tex" width="54" height="14"> will be accepted.</li>
            </ul>
            <p>Any reasonable format for floating point numbers is acceptable. For instance, <code>17.000000</code>, <code>0.17e2</code>, and <code>17</code> are all acceptable ways of formatting the number <img src="{{route('latex.png',['ltxsource'=>'$17$'])}}" style="display: inline-block;" class="rendered-tex" width="20" height="14">. For the definition of reasonable, please use your common sense.</p>
    </li>
    </ul>
</div>
<div class="page-breaker"></div>
