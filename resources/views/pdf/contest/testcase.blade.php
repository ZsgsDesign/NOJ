<style>
    div.sample-container{
        display: block;
    }

    div.sample-content table{
        width: 100%;
    }

    div.sample-content th{
        text-align: left;
        margin: 0;
    }

    div.sample-content tr > td{
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        border-top: 1px solid #000;
    }

    div.sample-content tr > td:first-of-type{
        border-left: 1px solid #000;
    }

    div.sample-content th,
    div.sample-content td{
        padding: 0.5rem;
    }

    .sample-container pre{
        margin:0;
    }
</style>

<div class="sample-container">
    <div class="sample-content">
        <table cellspacing="0">
            <tr>
                <th><h4>Sample Input {{$index}}</h4></th>
                <th><h4>Sample Output {{$index}}</h4></th>
            </tr>
            <tr>
                <td valign="top"><pre>{!!$input!!}</pre></td>
                <td valign="top"><pre>{!!$output!!}</pre></td>
            </tr>
        </table>
    </div>
</div>
