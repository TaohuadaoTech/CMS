<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\model\WebSites;
use app\common\model\WebStatistics;
use think\exception\DbException;
use think\response\Json;

/**
 * 数据统计
 *
 * @icon fa fa-circle-o
 */
class Statistics extends Backend
{

    /**
     * Statistics模型对象
     * @var \app\admin\model\web\Statistics
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\Statistics;

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
        if (true === $this->request->isAjax()) {
            $this->error(__('Operation failed'));
        }
        //如果发送的来源是 Selectpage，则转发到 Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        // 获取站点数据
        $site_list = [];
        $site_list['-1'] = '全部';
        $web_sites = WebSites::findWebSites();
        foreach ($web_sites as $site) {
            $site_list[$site->id] = $site->name;
        }
        // 获取查询条件
        $site_id = $this->request->get('site_id');
        $date = $this->request->get('date', 'today');
        switch ($date) {
            case 'today':
                $type = WebStatistics::SHOW_TYPE_HOUR;
                $start_time = strtotime(date('Y-m-d'));
                $end_time = time();
                break;
            case 'yesterday':
                $type = WebStatistics::SHOW_TYPE_HOUR;
                $start_time = strtotime(date('Y-m-d', strtotime('-1 day')));
                $end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
                break;
            case 'seven_days':
                $type = WebStatistics::SHOW_TYPE_DAY;
                $start_time = strtotime(date('Y-m-d', strtotime('-7 day')));
                $end_time = time();
                break;
            case 'one_month':
                $type = WebStatistics::SHOW_TYPE_DAY;
                $start_time = strtotime(date('Y-m-d', strtotime('-30 day')));
                $end_time = time();
                break;
            case 'three_months':
                $type = WebStatistics::SHOW_TYPE_DAY;
                $start_time = strtotime(date('Y-m-d', strtotime('-90 day')));
                $end_time = time();
                break;
            default:
                $this->error(__('Operation failed'));
        }
        $result_map_array = [];
        $category_array = $this->model->getAllShowType($type, $start_time, $end_time, ($date == 'today'));
        $result_array = (new WebStatistics())->findWebStatisticsByCount($site_id, $start_time, $end_time, $type);
        foreach ($result_array as $result) {
            $result_map_array[$result['category']] = $result;
        }
        // 格式化数据
        $total_pv = $total_uv = $total_rv = $total_vv = $total_mv = 0;
        $pv_array = $uv_array = $rv_array = $vv_array = $mv_array = [];
        foreach ($category_array as $category) {
            $pv = $uv = $rv = $vv = $mv = 0;
            if (isset($result_map_array[$category])) {
                $map_array = $result_map_array[$category];
                $pv = $map_array['pv'];
                $uv = $map_array['uv'];
                $rv = $map_array['rv'];
                $vv = $map_array['vv'];
                $mv = $map_array['mv'];
            }
            $total_pv += $pv;
            $total_uv += $uv;
            $total_rv += $rv;
            $total_vv += $vv;
            $total_mv += $mv;
            $pv_array[] = $pv;
            $uv_array[] = $uv;
            $rv_array[] = $rv;
            $vv_array[] = $vv;
            $mv_array[] = $mv;
        }
        // 统计数据
        $this->view->assign('totalPv', $total_pv);
        $this->view->assign('totalUv', $total_uv);
        $this->view->assign('totalRv', $total_rv);
        $this->view->assign('totalVv', $total_vv);
        $this->view->assign('totalMv', number_down($total_mv));
        // 设置图表数据
        $this->assignconfig('pvArray', $pv_array);
        $this->assignconfig('uvArray', $uv_array);
        $this->assignconfig('rvArray', $rv_array);
        $this->assignconfig('vvArray', $vv_array);
        $this->assignconfig('mvArray', $mv_array);
        $this->assignconfig('categoryList', $category_array);
        // 设置基础数据
        $this->view->assign('siteList', $site_list);
        $this->view->assign('date', $date ?: 'today');
        $this->view->assign('site_id', $site_id ?: '-1');
        $this->view->assign('dateList', ['today' => '今天', 'yesterday' => '昨天', 'seven_days' => '最近7天', 'one_month' => '最近30天', 'three_months' => '最近90天']);
        return $this->view->fetch();
    }

}
