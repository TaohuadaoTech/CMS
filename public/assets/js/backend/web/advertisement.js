define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/advertisement/index' + location.search,
                    // add_url: 'web/advertisement/add',
                    edit_url: 'web/advertisement/edit',
                    // del_url: 'web/advertisement/del',
                    multi_url: 'web/advertisement/multi',
                    import_url: 'web/advertisement/import',
                    table: 'web_advertisement',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                pageSize: 15,
                pageList: [15],
                pagination: true,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'position', title: __('Position'), operate: 'LIKE'},
                        {field: 'title', title: __('Title'), operate: 'LIKE', formatter: function (value, row, index) {
                            if (value && value.indexOf('[') !== -1 && value.indexOf(']') !== -1) {
                                var result_value = '';
                                var value_array = JSON.parse(value.replace(/&quot;/g, '"'));
                                for (var i = 0, size = value_array.length; i < size; i++) {
                                    result_value += '<p>' + value_array[i] + '</p>'
                                }
                                return result_value;
                            }
                            return value;
                        }},
                        {field: 'url', title: __('Url'), operate: 'LIKE', formatter: function (value, row, index) {
                            if (value && value.indexOf('[') !== -1 && value.indexOf(']') !== -1) {
                                var result_value = '';
                                var value_array = JSON.parse(value.replace(/&quot;/g, '"'));
                                for (var i = 0, size = value_array.length; i < size; i++) {
                                    result_value += '<p>' + value_array[i] + '</p>'
                                }
                                return result_value;
                            }
                            return value;
                        }},
                        // {field: 'image_pc', title: __('Image_pc'), operate: 'LIKE'},
                        // {field: 'image_h5', title: __('Image_h5'), operate: 'LIKE'},
                        // {field: 'type', title: __('Type')},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status_Enable'), "0":__('Status_Disable')}, formatter: Table.api.formatter.toggle},
                        // {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            $(document).on('click', '.add-combination', function (event) {
                var parent = $(event.currentTarget).parents('.adverisement');
                var cloneElement = parent.clone();
                cloneElement.find('input').val('');
                cloneElement.find('ul.faupload-preview').html('');
                cloneElement.find('.faupload').removeAttr('initialized');
                elemtnt_index = parseInt(cloneElement.attr('id').substring(cloneElement.attr('id').indexOf('-') + 1)) + 1;
                cloneElement.attr('id', cloneElement.attr('id').replaceAll(/div-([\d]*)/g, 'div-' + elemtnt_index));
                ['pc', 'h5'].forEach(function (value, index, array) {
                    cloneElement.find('input.c-image-'+ value +'-input').attr('id', 'c-image_'+ value +'_'+ elemtnt_index);
                    cloneElement.find('button.c-image-'+ value +'-button').attr('data-input-id', 'c-image_'+ value +'_'+ elemtnt_index);
                    cloneElement.find('button.c-image-'+ value +'-button').attr('data-preview-id', 'p-image_'+ value +'_'+ elemtnt_index);
                    cloneElement.find('span.c-image-'+ value +'-span').attr('for', 'c-image_'+ value +'_'+ elemtnt_index);
                    cloneElement.find('ul.c-image-'+ value +'-ul').attr('id', 'p-image_'+ value +'_'+ elemtnt_index);
                });
                $('.adverisement-list').append(cloneElement);
                Form.events.plupload('#edit-form');
                $(event.currentTarget).remove();
            });
            $(document).on('click', '.del-combination', function (event) {
                var deleElement = $(event.currentTarget).parents('.adverisement');
                var parent = $(event.currentTarget).parents('.button-combination');
                var deleAddButtonNumber = deleElement.find('button.add-combination').length;
                var addButtonNumber = parent.parents('.adverisement-list').find('button.add-combination').length;
                if (addButtonNumber - deleAddButtonNumber <= 0) {
                    var add_button = $('<span><button type="button" class="btn btn-success add-combination"><i class="fa fa fa-plus"></i> '+ __('add') +'</button></span>');
                    var prev = parent.parents('.adverisement-list').children().eq(-1).prev().children().eq(-1);
                    prev.addClass('button_array');
                    prev.append(add_button);
                }
                deleElement.remove();
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
