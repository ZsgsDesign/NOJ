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
            <p>Your submissions will be run multiple times, on several different input files. If your submission is incorrect, the error message you get will be the error exhibited on the first input file on which you failed. E.g., if your instance is prone to crash but also incorrect, your submission may be judged as either $$$wrong\;answer$$$ or $$$run\;time\;error$$$, depending on which is discovered first.</p>
        </li>
        <li>
            <p>For problems with floating point output, we only require that your output is correct up to some error tolerance.</p>
            <p>For example, if the problem requires the output to be within either absolute or relative error of $$$10^{-4}$$$, this means that</p>
            <ul class="dashed">
                <li>If the correct answer is $$$0.05$$$, any answer between $$$0.0499$$$ and $$$0.0501$$$ will be accepted.</li>
                <li>If the correct answer is $$$500$$$, any answer between $$$499.95$$$ and $$$500.05$$$ will be accepted.</li>
            </ul>
            <p>Any reasonable format for floating point numbers is acceptable. For instance, $$$17.000000$$$, $$$0.17e2$$$, and $$$17$$$ are all acceptable ways of formatting the number $$$17$$$. For the definition of reasonable, please use your common sense.</p>
        </li>
    </ul>
</div>
<div class="page-breaker"></div>
