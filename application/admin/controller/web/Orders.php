<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use think\exception\DbException;
use think\response\Json;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Orders extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'order_sn';

    /**
     * Orders模型对象
     * @var \app\admin\model\web\Orders
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\Orders;
        $this->assignconfig('typeList', $this->model->getTypeList());
        $this->assignconfig('payStatusList', $this->model->getPayStatusList());
        $this->assignconfig('payMethodNameList', $this->getPayMethodNameList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     *
     * @return string|Json
     * @throws \think\Exception
     * @throws DbException
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if (false === $this->request->isAjax()) {
            return $this->view->fetch();
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        [$where, $sort, $order, $offset, $limit] = $this->buildparams();
        $list = $this->model
            ->with(['user'])
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
        return json($result);
    }

    public function getPayMethodNameList()
    {
        $pay_method_name_list = [];
        $method_list = \app\admin\model\web\PaymentMethod::all();
        foreach ($method_list as $method) {
            $pay_method_name_list[$method['id']] = $method['name'];
        }
        return $pay_method_name_list;
    }

}
