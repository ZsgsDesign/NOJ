<style>
    sample-container{
        display: block;
    }

    sample-content table{
        width: 100%;
    }

    sample-content th{
        text-align: left;
    }

    sample-content tr > td{
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        border-top: 1px solid #000;
    }

    sample-content tr > td:first-of-type{
        border-left: 1px solid #000;
    }

    sample-content th,
    sample-content td{
        padding: 0.5rem;
    }
</style>

<sample-container>
    <sample-content>
        <table cellspacing="0">
            <tr>
                <th>Sample Input {{$index}}</th>
                <th>Sample Output {{$index}}</th>
            </tr>
            <tr>
                <td><pre>{!!$input!!}</pre></td>
                <td><pre>{!!$output!!}</pre></td>
            </tr>
        </table>
    </sample-content>
</sample-container>
