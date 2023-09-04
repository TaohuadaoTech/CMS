<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\handle\ConfigurationHandle;
use app\common\model\WebCategorys;
use fast\Pinyin;
use fast\Tree;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 分类信息
 *
 * @icon fa fa-circle-o
 */
class Categorys extends Backend
{

    /**
     * Categorys模型对象
     * @var \app\admin\model\web\Categorys
     */
    protected $model = null;
    protected $syncModel = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\Categorys;
        $this->syncModel = new \app\common\model\SyncCategories;
        $this->view->assign('modelList', $this->model->getModelList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('imageMimetype', ConfigurationHandle::getUploadImageMimetype());
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
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        foreach ($list->items() as &$item) {
            $item['name'] = xss_clean($item['name']);
        }
        Tree::instance()->init($list);
        $rows = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
        foreach ($rows as &$row) {
            if (empty($row['belong_to'])) {
                $row['mode'] = '-';
            }
        }
        $result = ['total' => $list->total(), 'rows' => $rows];
        return json($result);
    }

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            $this->view->assign('categorys', $this->getStandCategorys());
            $this->view->assign('belongCategorys', $this->getBelongCategorys());
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        if (WebCategorys::MODE_REFLECT == $params['mode']) {
            if (empty($params['integral']) || $params['integral'] <= 0) {
                $this->error(__('Please enter the points required for the score'));
            }
        }
        $result = false;
        Db::startTrans();
        try {
            $this->token();
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
            if ($params['belong_to']) {
                $map_to_array = explode('-', $params['map_to']);
                $params['map_to'] = intval($map_to_array[0]);
                $params['map_name'] = strval($map_to_array[1]);
            } else {
                unset($params['map_to'], $params['map_name']);
            }
            $params['create_time'] = time();
            $params['pinyin'] = Pinyin::get($params['name']);
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
            (new WebCategorys())->findWebCategorysByCache(false);
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

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
            if ($row->mode != WebCategorys::MODE_REFLECT) {
                $row['integral'] = '';
            }
            $this->view->assign('row', $row);
            $this->view->assign('selectCategorys', [$row->map_to]);
            $this->view->assign('categorys', $this->getStandCategorys($row));
            $this->view->assign('selectBelongCategorys', [$row->belong_to]);
            $this->view->assign('belongCategorys', $this->getBelongCategorys($row->id));
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if (WebCategorys::MODE_REFLECT == $params['mode']) {
            if (empty($params['integral']) || $params['integral'] <= 0) {
                $this->error(__('Please enter the points required for the score'));
            }
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
            if ($params['belong_to']) {
                $map_to_array = explode('-', $params['map_to']);
                $params['map_to'] = intval($map_to_array[0]);
                $params['map_name'] = strval($map_to_array[1]);
            } else {
                unset($params['map_to'], $params['map_name']);
            }
            $params['update_time'] = time();
            $params['pinyin'] = Pinyin::get($params['name']);
            $result = $row->allowField(true)->save($params);
            Db::commit();
            (new WebCategorys())->findWebCategorysByCache(false);
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
     * 删除
     *
     * @param $ids
     * @return void
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                WebCategorys::deleteWebCategorysByBelongTo($item->id);
                $count += $item->delete();
            }
            Db::commit();
            (new WebCategorys())->findWebCategorysByCache(false);
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }

    /**
     * 批量更新
     *
     * @param null $ids
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \think\db\exception\BindParamException
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
                WebCategorys::multiWebCategorysByBelongTo($item->id, $values);
                $count += $item->allowField(true)->isUpdate(true)->save($values);
            }
            Db::commit();
            (new WebCategorys())->findWebCategorysByCache(false);
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

    /**
     * 获取一级分类
     * @param null $ignore_id
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getBelongCategorys($ignore_id = null)
    {
        $result_array = [0 => __('Top_category')];
        $belong_categorys = WebCategorys::getBelongCategorysOneLevel($ignore_id);
        foreach ($belong_categorys as $category) {
            $result_array[$category->id] = $category->name;
        }
        return $result_array;
    }

    /**
     * 获取总分类信息
     * @param null $row
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function getStandCategorys($row = null)
    {
        $disabled_map_to_array = [];
        $map_to = empty($row) ? 0 : $row['map_to'];
        $web_categories = WebCategorys::getBelongCategorysTwoLevel();
        foreach ($web_categories as $web_category) {
            if ($map_to !== $web_category['map_to']) {
                $disabled_map_to_array[] = $web_category['map_to'];
            }
        }
        $categories = $this->syncModel->all();
        foreach ($categories as &$category) {
            $category['disabled'] = in_array($category['id'], $disabled_map_to_array);
        }
        return $categories;
    }

}
