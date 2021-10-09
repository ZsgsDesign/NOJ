<section-panel id="generate_pdf" class="d-none">
    <h3 class="tab-title">Generate PDF</h3>
    <div class="tab-body">
        <p>Current PDF</p>
        <div>
            @if($basic['pdf'])
                <file-card class="mt-4 mb-3">
                    <div>
                        <img src="/static/fonts/fileicon/svg/pdf.svg" onerror="this.src=NOJVariables.unknownfileSVG;">
                    </div>
                    <div>
                        <h5 class="mundb-text-truncate-1">{{$contest_name}}.pdf</h5>
                        <p>
                            <a class="text-info" href="{{route('ajax.contest.downloadPDF',['cid'=>$cid])}}">Download</a>
                            <a class="text-danger" onclick="removePDF()">Remove</a>
                        </p>
                    </div>
                </file-card>
            @else
                <file-card class="mt-4 mb-3">
                    <div>
                        <img src="/static/fonts/fileicon/svg/unknown.svg" onerror="this.src=NOJVariables.unknownfileSVG;">
                    </div>
                    <div>
                        <h5 class="mundb-text-truncate-1">Upload your own or generate below</h5>
                    </div>
                </file-card>
            @endif
        </div>
        <p>Generate Options</p>
        <div class="switch">
            <label><input type="checkbox" id="PDFOptionsCoverPage" checked> Cover Page</label>
        </div>
        <div class="switch">
            <label><input type="checkbox" id="PDFOptionsAdvicePage" checked> Advice Section</label>
        </div>
        <div class="mt-3" id="generatePDF_actions">
            @if(in_array($generatePDFStatus,['queued','executing']))
                <button type="button" class="btn btn-outline-info"><i class="MDI timer-sand"></i> Processing</button>
            @endif
            @if($generatePDFStatus=='failed')
                <button type="button" class="btn btn-outline-danger"><i class="MDI close-circle-outline"></i> Unable to Generate</button>
            @endif
            @if($generatePDFStatus=='finished')
                <button type="button" class="btn btn-outline-info"><i class="MDI checkbox-marked-circle-outline"></i> PDF Generating Completed</button>
            @endif
            @if(in_array($generatePDFStatus, ['finished','failed','empty']))
                <button type="button" class="btn btn-outline-success" onclick="generatePDF()"><i class="MDI file-pdf-box"></i> Generate PDF</button>
            @endif
        </div>
    </div>
</section-panel>

<script>
    var generatingPDF=false;

    function generatePDF(){
        if(generatingPDF) return;
        generatingPDF = true;
        $.ajax({
            type: 'POST',
            url: "{{route('ajax.contest.generatePDF')}}",
            data: {
                cid: {{$cid}},
                config: {
                    cover:$('#PDFOptionsCoverPage').prop('checked'),
                    advice:$('#PDFOptionsAdvicePage').prop('checked')
                }
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                // console.log(ret);
                if (ret.ret==200) {
                    alert("PDF generating in background, check status later.");
                    $('#generatePDF_actions').html(`<button type="button" class="btn btn-outline-info"><i class="MDI timer-sand"></i> Processing</button>`);
                } else {
                    alert(ret.desc);
                }
                generatingPDF=false;
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                generatingPDF=false;
            }
        });
    }

    var removingPDF=false;

    function removePDF(){
        if(removingPDF) return;
        removingPDF = true;
        $.ajax({
            type: 'POST',
            url: "{{route('ajax.contest.removePDF')}}",
            data: {
                cid: {{$cid}}
            },dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(ret){
                if (ret.ret==200) {
                    location.reload();
                } else {
                    alert(ret.desc);
                }
                removingPDF=false;
            }, error: function(xhr, type){
                console.log(xhr);
                switch(xhr.status) {
                    case 422:
                        alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                        break;

                    default:
                        alert("{{__('errors.default')}}");
                }
                console.log('Ajax error while posting to ' + type);
                removingPDF=false;
            }
        });
    }
</script>
