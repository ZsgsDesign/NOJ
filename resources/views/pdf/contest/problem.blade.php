<style>
    .problem-header {
        display: block;
        text-align: center;
    }

    .problem-header h1 {
        margin-bottom: 0.5rem;
    }

    .problem-header h2 {
        margin: 0;
    }

    .problem-container {
        display: block;
    }

    .problem-container h3 {
        margin-bottom: 0.5rem;
    }

    .problem-container img {
        max-width: 100%;
    }

</style>
<div class="problem-header">
    <h1>Problem {{ $contestProblem->ncode }}</h1>
    <h2>{{ $contestProblem->problem->title }}</h2>
    <p>Time Limit: {{ $contestProblem->problem->time_limit }} ms<br>Memory Limit: {{ $contestProblem->problem->memory_limit }} kb</p>
</div>

<div class="problem-container">
    @unless(blank($dialect->description))
        <div data-section="description">
            {!! $dialect->description !!}
        </div>
    @endunless

    @unless(blank($dialect->input))
        <h3>Input</h3>
        <div data-section="input">
            {!! $dialect->input !!}
        </div>
    @endunless

    @unless(blank($dialect->output))
        <h3>Output</h3>
        <div data-section="output">
            {!! $dialect->output !!}
        </div>
    @endunless

    @foreach ($contestProblem->problem->samples as $testcase)
        @include('pdf.contest.testcase', [
            'index' => $loop->iteration,
            'input' => $testcase->input,
            'output' => $testcase->output,
        ])
    @endforeach

    @unless(blank($dialect->note))
        <h3>Note</h3>
        <div data-section="note">
            {!! $dialect->note !!}
        </div>
    @endunless
</div>
