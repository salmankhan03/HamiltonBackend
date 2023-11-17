@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')

    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if(isset($dataType->order_column) && isset($dataType->order_display_column))
                <a href="{{ route('voyager.'.$dataType->slug.'.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes" data-toggle="toggle"
                       data-on="{{ __('voyager::bread.soft_deletes_off') }}"
                       data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach($actions as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')


    <style>
        .select,
        #locale {
            width: 100%;
        }

        .bootstrap-table .fixed-table-container .table thead th, .bootstrap-table .fixed-table-container .table tbody td {
            vertical-align: middle !important;
            text-align: center !important;
        }

        .like {
            margin-right: 10px;
        }

        .search {
            padding-top: 5px;
        }
    </style>

    <div class="form-group col-md-2">

        <label class="control-label" for="name">Date</label>
        <input type="date" class="form-control" id="search_date" placeholder="Date"
               value="{!! $last_date !!}" onchange="javascript:dateChange(this);">


    </div>
    <table
        id="table"
        data-toolbar="#toolbar"
        data-search="false"
        data-show-refresh="true"
        data-show-toggle="false"
        data-show-fullscreen="false"
        data-show-columns="true"
        data-show-columns-toggle-all="true"
        data-detail-view="false"
        data-show-export="true"
        data-click-to-select="true"
        {{--        data-detail-formatter="detailFormatter"--}}
        data-minimum-count-columns="2"
        data-pagination="false"
        data-card-view="false"
        data-id-field="id"
        data-show-footer="false"
        data-detail-view-icon="false"
        data-query-params="queryParams"
        data-ajax="ajaxRequest"
        {{--        data-url="{!! url("/admin/item-wise-report") !!}"--}}
        {{--        data-url="https://examples.wenzhixin.net.cn/examples/bootstrap_table/data"--}}
        data-response-handler="responseHandler">
    </table>


@stop

@section('css')
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">

    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table-locale-all.min.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/export/bootstrap-table-export.min.js"></script>
    <script>
        var $table = $('#table')
        var $remove = $('#remove')
        var selections = []

        function getIdSelections() {
            return $.map($table.bootstrapTable('getSelections'), function (row) {
                return row.id
            })
        }

        function responseHandler(res) {
            $.each(res.rows, function (i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        function detailFormatter(index, row) {
            var html = []
            $.each(row, function (key, value) {
                html.push('<p><b>' + key + ':</b> ' + value + '</p>')
            })
            return html.join('')
        }

        function operateFormatter(value, row, index) {
            return [
                '<a class="like" href="javascript:void(0)" title="Like">',
                '<i class="fa fa-heart"></i>',
                '</a>  ',
                '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="fa fa-trash"></i>',
                '</a>'
            ].join('')
        }

        window.operateEvents = {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row))
            },
            'click .remove': function (e, value, row, index) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [row.id]
                })
            }
        }

        function ajaxRequest(params) {
            var url = "{!! url("/admin/item-wise-report") !!}"
            $.get(url + '?' + $.param(params.data)).then(function (res) {
                res = JSON.parse(res);
               
                let columns = res.columns;
                let string = '';
                for (let c in columns) {
                    let inner_column = columns[c];

                    string += '<tr>';
                    let content = "";
                    for (let ic in inner_column) {

                        string += '<th align="center"';
                        let styles = inner_column[ic];
                        content = styles.title;
                        string += 'data-title="' + styles.title + '"';
                        if (styles.field) {
                            string += ' data-field="' + styles.field + '"';
                        }
                        if (styles.rowspan) {
                            string += ' rowspan="' + styles.rowspan + '"';
                        }
                        if (styles.colspan) {
                            string += ' colspan="' + styles.colspan + '"';
                        }
                        if (styles.tooltip) {
                            string += ' title="' + styles.tooltip + '"';
                        }
                        string += '><div class="th-inner">' + content + '</div><div class="fht-cell"></div></th>';
                    }

                    string += '</tr>';

                }
             
                $table.find("thead").html("").append(string);
                //  $table.bootstrapTable('load',res.result);
                params.success(res.result);
            })
        }

        function queryParams(params) {
            params.search_date = $("#search_date").val();
            return params;
        }

        function dateChange(ref) {
            $table.bootstrapTable('refresh');
        }

        function totalTextFormatter(data) {
            return 'Total'
        }

        function totalNameFormatter(data) {
            return data.length
        }

        function totalPriceFormatter(data) {
            var field = this.field
            return '$' + data.map(function (row) {
                return +row[field].substring(1)
            }).reduce(function (sum, i) {
                return sum + i
            }, 0)
        }

        function initTable() {
            $table.bootstrapTable('destroy').bootstrapTable({
                // data:JSON.parse(t),
                height: 550,
                 columns: [{!! json_encode($table_column[0]) !!},{!! json_encode($table_column[1]) !!} ],
                locale: "en",
                exportTypes: ['csv', 'excel'],
                // columns: [
                //     [{
                //         field: 'state',
                //         checkbox: true,
                //         rowspan: 2,
                //         align: 'center',
                //         valign: 'middle'
                //     }, {
                //         title: 'Item ID',
                //         field: 'id',
                //         rowspan: 2,
                //         align: 'center',
                //         valign: 'middle',
                //         sortable: true,
                //         footerFormatter: totalTextFormatter
                //     }, {
                //         title: 'Item Detail',
                //         colspan: 3,
                //         align: 'center'
                //     }],
                //     [{
                //         field: 'name',
                //         title: 'Item Name',
                //         sortable: true,
                //         footerFormatter: totalNameFormatter,
                //         align: 'center'
                //     }, {
                //         field: 'price',
                //         title: 'Item Price',
                //         sortable: true,
                //         align: 'center',
                //         footerFormatter: totalPriceFormatter
                //     }, {
                //         field: 'operate',
                //         title: 'Item Operate',
                //         align: 'center',
                //         clickToSelect: false,
                //         events: window.operateEvents,
                //         formatter: operateFormatter
                //     }]
                // ]
            })
            $table.on('check.bs.table uncheck.bs.table ' +
                'check-all.bs.table uncheck-all.bs.table',
                function () {
                    $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

                    // save your data, here just save the current page
                    selections = getIdSelections()
                    // push or splice the selections if you want to save all data selections
                })
            $table.on('all.bs.table', function (e, name, args) {
                //   console.log(name, args)
            })
            $remove.click(function () {
                var ids = getIdSelections()
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                })
                $remove.prop('disabled', true)
            })
        }

        $(function () {
            initTable()

            $('#locale').change(initTable)
        })
    </script>
    <script>
        $(document).ready(function () {
                @if (!$dataType->server_side)
            var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => $orderColumn,
                        "language" => __('voyager::datatable'),
                        "columnDefs" => [['targets' => -1, 'searchable' =>  false, 'orderable' => false]],
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
            $('#search-input select').select2({
                minimumResultsForSearch: Infinity
            });
            @endif

            @if ($isModelTranslatable)
            $('.side-body').multilingual();
            //Reinitialise the multilingual features when they change tab
            $('#dataTable').on('draw.dt', function () {
                $('.side-body').data('multilingual').init();
            })
            @endif
            $('.select_all').on('click', function (e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            $('#delete_form')[0].action = '{{ route('voyager.'.$dataType->slug.'.destroy', '__id') }}'.replace('__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if($usesSoftDeletes)
        @php
            $params = [
                's' => $search->value,
                'filter' => $search->filter,
                'key' => $search->key,
                'order_by' => $orderBy,
                'sort_order' => $sortOrder,
            ];
        @endphp
        $(function () {
            $('#show_soft_deletes').change(function () {
                if ($(this).prop('checked')) {
                    $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 1]), true)) }}"></a>');
                } else {
                    $('#dataTable').before('<a id="redir" href="{{ (route('voyager.'.$dataType->slug.'.index', array_merge($params, ['showSoftDeleted' => 0]), true)) }}"></a>');
                }

                $('#redir')[0].click();
            })
        })
        @endif
        $('input[name="row_id"]').on('change', function () {
            var ids = [];
            $('input[name="row_id"]').each(function () {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>
@stop
