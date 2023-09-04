define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/messages/index' + location.search,
                    add_url: 'web/messages/add',
                    edit_url: 'web/messages/edit',
                    del_url: 'web/messages/del',
                    multi_url: 'web/messages/multi',
                    import_url: 'web/messages/import',
                    table: 'web_messages',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        // {field: 'index', title: __('Index')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // {field: 'type_id', title: __('Type_id')},
                        {field: 'type.name', title: __('Type_name')},
                        // {field: 'content', title: __('Content')},
                        // {field: 'read_flg', title: __('Read_flg')},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status_Enable'),"0":__('Status_Disable')}, formatter: Table.api.formatter.toggle},
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
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
