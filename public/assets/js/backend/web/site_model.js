define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/site_model/index' + location.search,
                    // add_url: 'web/site_model/add',
                    // edit_url: 'web/site_model/edit',
                    // del_url: 'web/site_model/del',
                    multi_url: 'web/site_model/multi',
                    import_url: 'web/site_model/import',
                    table: 'web_model_download',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                pageSize: 5,
                search: false,
                sortName: 'id',
                fixedColumns: false,
                fixedRightNumber: 1,
                commonSearch: false,
                pageList: [5, 10, 15, 20],
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'model_id', title: __('Mode_id')},
                        {field: 'model_cover', title: __('Model_cover'), operate: false, formatter: function (value, row, index) {
                            if (value) {
                                return '<a href="'+ value +'" target="_blank"><img src="'+ value +'" alt="'+ value +'" style="height: 150px;" /></a>'
                            }
                            return '-';
                        }},
                        {field: 'model_name', title: __('Mode_name'), operate: 'LIKE'},
                        {field: 'model_version', title: __('Mode_version'), operate: false},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE'},
                        {field: 'model_status', title: __('Model_status')},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'download',
                                    text: __('Model_download'),
                                    title: __('Model_download'),
                                    classname: 'btn btn-xs btn-success btn-ajax',
                                    icon: 'fa fa-arrow-down',
                                    url: 'web/site_model/download',
                                    confirm: __('Model_download_remind'),
                                    visible: function (row) {
                                        return !row.model_is_download;
                                    },
                                    success: function (data, row) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    error: function (data, row) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                },
                                {
                                    name: 'detail',
                                    text: __('Model_select'),
                                    title: __('Model_select'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: 'fa fa-search',
                                    url: 'web/site_model/detail',
                                    success: function (data, row) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    error: function (data, row) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                }
                            ]
                        }
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
