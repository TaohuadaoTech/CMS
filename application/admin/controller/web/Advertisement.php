<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\handle\ConfigurationHandle;
use app\common\model\WebAdvertisement;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 广告管理
 *
 * @icon fa fa-circle-o
 */
class Advertisement extends Backend
{

    /**
     * Advertisement模型对象
     * @var \app\admin\model\web\Advertisement
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\Advertisement;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('imageMimetype', ConfigurationHandle::getUploadImageMimetype());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            switch ($row['type']) {
                case WebAdvertisement::TYPE_FIXED:
                    $template = 'fixed';
                    break;
                case WebAdvertisement::TYPE_JS:
                    $template = 'js';
                    break;
                case WebAdvertisement::TYPE_MULT:
                    $template = 'mult';
                    $detail_array = [];
                    if (!empty($row['title'])) {
                        $title_array = json_decode($row['title'], true);
                        $url_array = json_decode($row['url'], true);
                        $image_pc_array = json_decode($row['image_pc'], true);
                        $image_h5_array = json_decode($row['image_h5'], true);
                        $min_size = min(count($title_array), count($url_array), count($image_pc_array), count($image_h5_array));
                        for ($index = 0; $index < $min_size; $index++) {
                            $detail_array[] = [
                                'title' => $title_array[$index],
                                'url' => $url_array[$index],
                                'image_pc' => $image_pc_array[$index],
                                'image_h5' => $image_h5_array[$index],
                                'add_button' => $index == $min_size - 1,
                            ];
                        }
                        unset($row['title'], $row['url'], $row['image_pc'], $row['image_h5']);
                    } else {
                        $detail_array[] = [
                            'title' => '',
                            'url' => '',
                            'image_pc' => '',
                            'image_h5' => '',
                            'add_button' => true,
                        ];
                    }
                    $row['datail_array'] = $detail_array;
                    break;
                case WebAdvertisement::TYPE_VIDEO:
                    $template = 'video';
                    $this->view->assign('imageMimetype', ConfigurationHandle::getUploadVideoMimetype());
                    break;
            }
            switch ($row['id']) {
                case WebAdvertisement::STATIC_BANNER_ID:
                    $steer_size = ['1300*95', '390*30'];
                    break;
                case WebAdvertisement::DYNAMIC_BANNER_ID:
                    $steer_size = ['1300*450', '390*140'];
                    break;
                case WebAdvertisement::RIGHT_ID:
                case WebAdvertisement::LEFT_ID:
                    $steer_size = ['280*624', '280*624'];
                    break;
                case WebAdvertisement::PAUSE_ID:
                    $steer_size = ['650*433', '250*166'];
                    break;
                case WebAdvertisement::BOTTOM_ID:
                case WebAdvertisement::TOP_ID:
                    $steer_size = ['1300*150', '390*45'];
                    break;
            }
            if (isset($steer_size)) {
                $this->view->assign('steerSize', $steer_size);
            }
            $this->view->assign('row', $row);
            return $this->view->fetch($template);
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if (!(isset($params['image_pc']) || isset($params['image_h5']))) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            $this->token();
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            if (isset($row['type']) && $row['type'] == WebAdvertisement::TYPE_MULT) {
                $params['title'] = json_encode($params['title'], JSON_UNESCAPED_UNICODE);
                $params['url'] = json_encode($params['url'], JSON_UNESCAPED_UNICODE);
                $params['image_pc'] = json_encode($params['image_pc'], JSON_UNESCAPED_UNICODE);
                $params['image_h5'] = json_encode($params['image_h5'], JSON_UNESCAPED_UNICODE);
            }
            if (isset($row['type']) && $row['type'] == WebAdvertisement::TYPE_VIDEO) {
                $params['image_h5'] = $params['image_pc'];
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
            WebAdvertisement::findWebAdvertisementById($row['id'], null, false);
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 批量更新
     *
     * @param null $ids
     * @throws DbException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function multi($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $ids = $ids ?: $this->request->post('ids');
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }

        if (false === $this->request->has('params')) {
            $this->error(__('No rows were updated'));
        }
        parse_str($this->request->post('params'), $values);
        $values = $this->auth->isSuperAdmin() ? $values : array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
        if (empty($values)) {
            $this->error(__('You have no permission'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $count = 0;
        Db::startTrans();
        try {
            $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
            foreach ($list as $item) {
                $count += $item->allowField(true)->isUpdate(true)->save($values);
            }
            Db::commit();
            foreach ($list as $item) {
                WebAdvertisement::findWebAdvertisementById($item->id, null, false);
            }
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

    public function add()
    {
        $this->error(__('Invalid operation'));
    }

    public function del($ids = null)
    {
        $this->error(__('Invalid operation'));
    }

}
