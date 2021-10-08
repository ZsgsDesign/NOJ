<style>
    problem-selector{
        display: block;
    }

    problem-selector .problem-remove{
        cursor: pointer;
    }

    problem-selector table.table tbody tr td{
        vertical-align: middle;
    }

    problem-selector table.table tbody tr th{
        vertical-align: middle;
        text-align: center;
    }

    problem-selector .MDI.problem-remove:before {
        content: "\e795";
    }

    .sortable-chosen.sortable-ghost {
        opacity: 0;
    }

    .sortable-fallback {
        opacity: 1 !important;
    }

    td[data-field="title"]{
        color: rgba(0, 0, 0, 0.53);
    }

    .problem-selector-actions{
        text-align: center;
    }

    .problem-selector-actions button{
        border-radius: 200px;
    }

    input[data-field="pcode"].form-control:disabled{
        background-color: transparent;
    }

    problem-selector table.table tbody {
        counter-reset: psumber;
    }

    problem-selector table.table tbody td[data-field="index"]::before{
        counter-increment: psumber;
        content: counter(psumber);
    }
</style>

<problem-selector class="mt-3">
    <p>{{__('problem.selector.caption')}}</p>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">{{__('problem.selector.index')}}</th>
                    <th scope="col">{{__('problem.selector.code')}}</th>
                    <th scope="col">{{__('problem.selector.title')}}</th>
                    @if($editAlias) <th scope="col">{{__('problem.selector.alias')}}</th> @endif
                    <th class="d-none" scope="col">{{__('problem.selector.points')}}</th>
                    <th scope="col">{{__('problem.selector.operations')}}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="problem-selector-actions">
        <button type="button" class="btn btn-outline-info mb-0" onclick="addNewProblemToSelector()"><i class="MDI library-plus"></i> {{__('problem.selector.action.add')}}</button>
    </div>
</problem-selector>

@push('additionScript')
    <script>
        var sortableProblems = Sortable.create(document.querySelector('problem-selector tbody'), {
            handle: '.problem-selector-handle',
            animation: 200,
            easing: "var(--animation-timing-function)",
        });

        function removeProblemSelector(element) {
            $(element).parents('tr').remove();
        }

        var problemSelectorID = 1;

        function resetProblemSelector() {
            $('problem-selector tbody').html('');
            problemSelectorID = 1;
        }

        function addNewProblemToSelector({pcode = null, title = null}={}) {
            $('problem-selector tbody').append(`
                <tr data-selector-id="${problemSelectorID++}">
                    <th scope="row" class="problem-selector-handle"><i class="MDI menu"></i></th>
                    <td data-field="index"></td>
                    <td>
                        <input type="text" data-field="pcode" class="form-control form-control-sm" value="" autocomplete="off" placeholder="{{__('problem.selector.placeholder.code')}}" required>
                    </td>
                    <td data-field="title"><i class="MDI comment-question-outline"></i> <span>{{__('problem.selector.tooltip.empty')}}</span></td>
                    @if($editAlias)
                    <td>
                        <input type="text" data-field="alias" class="form-control form-control-sm" value="" autocomplete="off" placeholder="{{__('problem.selector.placeholder.alias')}}" required>
                    </td>
                    @endif
                    <td class="d-none">100</td>
                    <td><i class="MDI problem-remove wemd-red-text" onclick="removeProblemSelector(this)"></i></td>
                </tr>
            `);

            if(pcode !== null) {
                $('problem-selector tbody tr:last-of-type input[data-field="pcode"]').val(pcode.trim());
            }

            if(title !== null) {
                $('problem-selector tbody tr:last-of-type td[data-field="title"]').addClass("wemd-green-text");
                $('problem-selector tbody tr:last-of-type td[data-field="title"] i').removeClass();
                $('problem-selector tbody tr:last-of-type td[data-field="title"] i').addClass("MDI comment-check-outline");
                $('problem-selector tbody tr:last-of-type td[data-field="title"] span').text(title.trim());
            }

            $('problem-selector tbody tr:last-of-type').bootstrapMaterialDesign();
            $('problem-selector tbody tr:last-of-type input[data-field="pcode"]').blur(function(){
                checkProblemExistenceByField(this);
            }).keydown(function(e) {
                if (e.keyCode == 13) {
                    checkProblemExistenceByField(this);
                }
            });
        }

        function checkProblemExistenceByField(element) {
            $('problem-selector input[data-field="pcode"]').prop('disabled', true);
            let pcode = $(element).val().trim().toUpperCase();
            $(element).val(pcode);
            if(pcode === '') {
                return procProblemExistence(element, {
                    ret: -3
                });
            }
            try {
                $('problem-selector tbody tr').each(function() {
                    if(pcode == $(this).find('input[data-field="pcode"]').val().trim()) {
                        if($(element).parents('tr').attr('data-selector-id') != $(this).attr('data-selector-id')) {
                            throw "Duplicate Problem";
                        }
                    }
                });
            } catch (error) {
                return procProblemExistence(element, {
                    ret: -2
                });
            }
            return checkProblemExistence(element, pcode);
        }

        var checkingProblemExistence = false;

        function checkProblemExistence(element, pcode) {
            if(checkingProblemExistence) {
                return;
            }
            checkingProblemExistence = true;
            $(element).parents('tr').find('td[data-field="title"]').removeClass();
            $(element).parents('tr').find('td[data-field="title"]').addClass('refreshing');
            $(element).parents('tr').find('td[data-field="title"] span').text("{{__('problem.selector.tooltip.loading')}}");
            $.ajax({
                type: 'POST',
                url: "{{route('ajax.problemExists')}}",
                data: {
                    pcode: pcode
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function(ret){
                    checkingProblemExistence=false;
                    procProblemExistence(element, ret);
                }, error: function(xhr, type){
                    checkingProblemExistence=false;
                    procProblemExistence(element, {
                        ret: -1
                    });
                }
            });
        };

        function procProblemExistence(element, ret) {
            $(element).parents('tr').find('td[data-field="title"]').removeClass();
            $('problem-selector input[data-field="pcode"]').prop('disabled', false);
            if(ret.ret == 200) {
                $(element).parents('tr').find('td[data-field="title"]').addClass('wemd-green-text');
                $(element).parents('tr').find('td[data-field="title"] span').text(ret.data.title);
                $(element).parents('tr').find('td[data-field="title"] i').removeClass();
                $(element).parents('tr').find('td[data-field="title"] i').addClass("MDI comment-check-outline");
            } else if(ret.ret == -3) {
                $(element).parents('tr').find('td[data-field="title"] span').text("{{__('problem.selector.tooltip.empty')}}");
                $(element).parents('tr').find('td[data-field="title"] i').removeClass();
                $(element).parents('tr').find('td[data-field="title"] i').addClass("MDI comment-question-outline");
            } else if(ret.ret == -2) {
                $(element).parents('tr').find('td[data-field="title"]').addClass('wemd-red-text');
                $(element).parents('tr').find('td[data-field="title"] span').text("{{__('problem.selector.tooltip.duplicate')}}");
                $(element).parents('tr').find('td[data-field="title"] i').removeClass();
                $(element).parents('tr').find('td[data-field="title"] i').addClass("MDI comment-remove-outline");
            }  else if(ret.ret == -1) {
                $(element).parents('tr').find('td[data-field="title"]').addClass('wemd-red-text');
                $(element).parents('tr').find('td[data-field="title"] span').text("{{__('problem.selector.tooltip.error')}}");
                $(element).parents('tr').find('td[data-field="title"] i').removeClass();
                $(element).parents('tr').find('td[data-field="title"] i').addClass("MDI comment-remove-outline");
            } else {
                $(element).parents('tr').find('td[data-field="title"]').addClass('wemd-red-text');
                $(element).parents('tr').find('td[data-field="title"] span').text("{{__('problem.selector.tooltip.notfound')}}");
                $(element).parents('tr').find('td[data-field="title"] i').removeClass();
                $(element).parents('tr').find('td[data-field="title"] i').addClass("MDI comment-remove-outline");
            }
        }

        function getSelectedProblemList() {
            let problemList = [];
            try {
                $('problem-selector tbody tr').each(function() {
                    if(!$(this).find('td[data-field="title"]').hasClass('wemd-green-text')) {
                        throw "Not Ready!";
                    }
                    problemList.push({
                        pcode: $(this).find('input[data-field="pcode"]').val().trim(),
                        alias: @if($editAlias) $(this).find('input[data-field="alias"]').val().trim() @else '' @endif ,
                        points: 100,
                    });
                });
            } catch (error) {
                return false;
            }
            return problemList;
        }
    </script>
@endpush
