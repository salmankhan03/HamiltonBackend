@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
    $items = [];
    if($edit){
        $items = $dataTypeContent->items;
        $items = json_decode($items,true);
    }

@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                          class="form-edit-add"
                          action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                          method="POST" enctype="multipart/form-data" onsubmit="javascript:formSubmit(this)">
                        <!-- PUT Method if we are editing -->
                    @if($edit)
                        {{ method_field("PUT") }}
                    @endif

                    <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                            <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}"
                                            style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div
                                    class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 4 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label"
                                           for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                @if($row->field == "items")
                                    @foreach($all_categories as $key => $value)
                                        <div class="form-group padding-left-right">
                                            <label class="control-label" for="name">{!! ucfirst($key) !!}</label>
                                            <br/>
                                            <input class="toggle-one type_checkbox float-left" data-type="{{ $key }}"
                                                   id="menu_toggle_{{ $key }}" type="checkbox" value="menu_{{ $key }}">
                                            <input type="text" class="form-control float-right"
                                                   placeholder="Search item.." style="width: 200px"
                                                   onkeyup="javascript:searchItem('menu_{{ $key }}',this)">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="padding-left-right menu_outer @if($items) @endif hidden"
                                             id="menu_{{ $key }}">
                                            <div class="row equal" style="margin: 0px">
                                                <div class="col-md-3" style="  background: #d6d6d6;">
                                                    <div class="list-group">
                                                        @foreach($value as $v)
                                                            <button type="button"
                                                                    onclick="javascript:addItems('{!! $v->id !!}','#menu_{{ $key }}',this,'{{ $key }}')"
                                                                    class="list-group-item">{!! $v->cat_name !!}</button>                                                          @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-9" style="background: #f0f0f0;">
                                                    <div class="item-container">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    @endforeach
                                    @if ($errors->has("items"))
                                        <span class="help-block">{{ $errors->get("items")[0] }}</span>
                                    @endif
                                @endif
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit"
                                        class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;
                    </button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                    </h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'
                    </h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger"
                            id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <script>
        var params = {};
        var $file;
        var item_json = '{!! addslashes(json_encode($all_items)) !!}';
        item_json = JSON.parse(item_json);
        var selected_items = '{!! $edit ? $dataTypeContent->items : '{"breakfast":[],"lunch":[],"dinner":[]}' !!}';
        if (selected_items != "") {
            selected_items = JSON.parse(selected_items);
        }

        function searchItem(menu_id, div) {
            var search_text = $.trim($(div).val().toLowerCase());
            if (search_text != "") {
                $('#' + menu_id).find(".item-container").find('.single_item').each(function () {
                    var title = $(this).find('.item_name').attr('title').toLowerCase();
                    if (title.indexOf(search_text) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }

                });
            }else{
                $('#' + menu_id).find(".item-container").find('.single_item').each(function () {
                    $(this).show();
                })
            }
        }

        function deleteHandler(tag, isMulti) {
            return function () {
                $file = $(this).siblings(tag);

                params = {
                    slug: '{{ $dataType->slug }}',
                    filename: $file.data('file-name'),
                    id: $file.data('id'),
                    field: $file.parent().data('field-name'),
                    multi: isMulti,
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text(params.filename);
                $('#confirm_delete_modal').modal('show');
            };
        }

        function addItems(id, container, div, parent) {
            var items = item_json[id];
            $(container + " .item-container").html("");
            if (items != undefined) {
                for (var i in items) {
                    $(container + " .item-container").append('<div class="single_item" data-type="' + parent + '" data-id="' + items[i].item_id + '"><div class="item-thumbnail' + (selected_items[parent].indexOf(items[i].item_id) > -1 ? " active" : "") + '"><div class="item_name" title="' + items[i].item_name + '">' + items[i].truncated_name + '</div></div></div>')
                }
            }
            $(container).find(".list-group-item").removeClass("active");
            $(div).addClass("active");
        }

        function formSubmit(form) {
            console.log("formSubmit");
            $(form).append("<input type='hidden' name='items' value='" + JSON.stringify(selected_items) + "'/>");
            return true;
        }

        $('document').ready(function () {
            $(document).on("click", ".single_item", function () {
                $(this).find(".item-thumbnail").toggleClass("active");
                if ($(this).find(".item-thumbnail").hasClass("active")) {
                    selected_items[$(this).data("type")].push($(this).data("id"));
                } else {
                    selected_items[$(this).data("type")].splice(selected_items[$(this).data("type")].indexOf($(this).data("id")), 1);
                }
            });
            $(document).on("change", ".type_checkbox", function () {
                if ($(this).is(":checked")) {
                    $('#' + $(this).val()).removeClass("hidden").addClass("show");
                    $('#' + $(this).val()).find(".list-group-item:first-child").click();
                } else {
                    $('#' + $(this).val()).removeClass("show").addClass("hidden");
                    $('#' + $(this).val()).find(".list-group-item.active").click();
                    selected_items[$(this).data("type")] = [];
                }
            });
            $('.toggleswitch,.type_checkbox').bootstrapToggle();
            @if(count($items) > 0)
            @foreach($items as $key => $value)
            @if(count($value) > 0)
            $("#menu_{{ $key }}").removeClass("hidden");
            $("#menu_toggle_{{ $key }}").bootstrapToggle("on");
            @endif
            @endforeach
            @endif
            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: ['YYYY-MM-DD']
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
            $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function (i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function () {
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if (response
                        && response.data
                        && response.data.status
                        && response.data.status == 200) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function () {
                            $(this).remove();
                        })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
