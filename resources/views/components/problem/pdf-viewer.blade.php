@props(['src', 'displayOnSight'])

<div id="pdfView" class="{{ $displayOnSight ? '' : 'd-none' }}" style="width:100%;height:800px;"></div>

<script>
    var PDF_SRC = "{{ $src }}";
</script>

@if ($displayOnSight)
    <script>
        window.addEventListener("load", function() {
            PDFObject.embed(PDF_SRC, "#pdfView");
        });
    </script>
@endif
