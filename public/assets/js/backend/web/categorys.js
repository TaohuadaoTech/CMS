define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/categorys/index' + location.search,
                    add_url: 'web/categorys/add',
                    edit_url: 'web/categorys/edit',
                    del_url: 'web/categorys/del',
                    multi_url: 'web/categorys/multi',
                    import_url: 'web/categorys/import',
                    table: 'web_categorys',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                escape: false,
                sortName: 'id',
                sortOrder: 'ASC',
                pagination: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'index', title: __('Index')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // {field: 'pinyin', title: __('Pinyin'), operate: 'LIKE'},
                        // {field: 'belong_to', title: __('Belong_to')},
                        // {field: 'logo', title: __('Logo'), operate: 'LIKE'},
                        // {field: 'front', title: __('Front'), operate: 'LIKE'},
                        // {field: 'map_to', title: __('Map_to')},
                        {field: 'map_name', title: __('Map_name'), operate: false},
                        {field: 'mode', title: __('Mode'), searchList: {"0":__('Mode_free'), "1":__('Mode_vip'), "2":__('Mode_reflect')}, formatter: Table.api.formatter.normal},
                        // {field: 'integral', title: __('Integral')},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status_Enable'), "0":__('Status_Disable')}, formatter: Table.api.formatter.toggle},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                var form = $("form[role=form]");
                form.find('select[name="row[belong_to]"]').on('change', function (event) {
                    var value = $(event.currentTarget).val();
                    if (value === '0') {
                        form.find('.top-level').addClass('hidden');
                    } else {
                        form.find('.top-level').removeClass('hidden');
                    }
                });
                Form.api.bindevent(form);
            }
        }
    };
    return Controller;
});
