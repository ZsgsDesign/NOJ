{{-- Cover Page --}}
@if($conf['cover']) @include('pdf.contest.cover') @endif

{{-- Advice Page --}}
@if($conf['advice']) @include('pdf.contest.advice') @endif

{{-- ProblemSet --}}
@foreach ($problemset as $problem)

    @include('pdf.contest.problem', ['problem'=>$problem['details']])

    @foreach ($problem['testcases'] as $testcase)
        @include('pdf.contest.testcase', ['testcase'=>$testcase])
    @endforeach

@endforeach
