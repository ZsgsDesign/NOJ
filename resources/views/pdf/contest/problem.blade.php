<style>
    .problem-header{
        display: block;
        text-align: center;
    }

    .problem-header h1{
        margin-bottom: 0.5rem;
    }

    .problem-header h2{
        margin: 0;
    }

    .problem-container{
        display: block;
    }

    .problem-container h3{
        margin-bottom: 0.5rem;
    }

    .problem-container img {
        max-width: 100%;
    }
</style>
<div class="problem-header">
    <h1>Problem {{$problem['index']}}</h1>
    <h2>{{$problem['title']}}</h2>
    <p>Time Limit: {{$problem['time_limit']}} ms<br>Memory Limit: {{$problem['memory_limit']}} kb</p>
</div>

<div class="problem-container">
    @unless(blank($problem["parsed"]["description"]))
        <div data-section="description">
            {!!$problem["parsed"]["description"]!!}
        </div>
    @endunless

    @unless(blank($problem["parsed"]["input"]))
        <h3>Input</h3>
        <div data-section="input">
            {!!$problem["parsed"]["input"]!!}
        </div>
    @endunless

    @unless(blank($problem["parsed"]["output"]))
        <h3>Output</h3>
        <div data-section="output">
            {!!$problem["parsed"]["output"]!!}
        </div>
    @endunless

    @foreach ($problem['testcases'] as $testcase)
        @include('pdf.contest.testcase', [
            'index'=>$loop->iteration,
            'input'=>$testcase['sample_input'],
            'output'=>$testcase['sample_output'],
        ])
    @endforeach

    @unless(blank($problem["parsed"]["note"]))
        <h3>Note</h3>
        <div data-section="note">
            {!!$problem["parsed"]["note"]!!}
        </div>
    @endunless
</div>
