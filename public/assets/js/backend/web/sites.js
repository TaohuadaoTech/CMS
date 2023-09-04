define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/sites/index' + location.search,
                    add_url: 'web/sites/add',
                    edit_url: 'web/sites/edit',
                    del_url: 'web/sites/del',
                    multi_url: 'web/sites/multi',
                    import_url: 'web/sites/import',
                    table: 'web_sites',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'domain', title: __('Domain'), operate: 'LIKE'},
                        // {field: 'describe', title: __('Describe'), operate: 'LIKE'},
                        // {field: 'keyword', title: __('Keyword'), operate: 'LIKE'},
                        {field: 'site_model.model_name', title: __('Module_Name')},
                        // {field: 'js_code', title: __('Js_code'), operate: 'LIKE'},
                        // {field: 'logo', title: __('Logo'), operate: 'LIKE'},
                        // {field: 'icon', title: __('Icon'), operate: 'LIKE', formatter: Table.api.formatter.icon},
                        // {field: 'declaration', title: __('Declaration'), operate: 'LIKE'},
                        // {field: 'customer_link', title: __('Customer_link'), operate: 'LIKE'},
                        // {field: 'customer_code', title: __('Customer_code')},
                        // {field: 'android', title: __('Android'), operate: 'LIKE'},
                        // {field: 'ios', title: __('Ios'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status_Disable'), "1":__('Status_Enable')}, formatter: Table.api.formatter.toggle},
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
