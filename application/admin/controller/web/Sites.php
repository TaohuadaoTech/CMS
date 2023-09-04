<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\handle\ConfigurationHandle;
use app\common\library\Redis;
use app\common\model\WebModelDownload;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 站点记录
 *
 * @icon fa fa-circle-o
 */
class Sites extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'name,domain';

    /**
     * Sites模型对象
     * @var \app\admin\model\web\Sites
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\Sites;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('iconMimetype', ConfigurationHandle::getUploadIconMimetype());
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
            ->with('site_model')
            ->where($where)
            ->order($sort, $order)
            ->paginate($limit);
        $result = ['total' => $list->total(), 'rows' => $list->items()];
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
            $this->view->assign('modelList', $this->getModelList());
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if ((!isset($params['model_id'])) || empty($params['model_id'])) {
            $this->error(__('Module_id_empty'));
        }
        $params = $this->preExcludeFields($params);

        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
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
            // 判断域名是否已存在
            $site = $this->model->where('domain', $params['domain'])->find();
            if (!empty($site)) {
                $this->error(__('Domain_unique'));
            }
            // 保存站点信息
            $model_id = $params['model_id'];
            $download_model = WebModelDownload::find($model_id);
            $params['model_id'] = $download_model->id;
            $params['model_path'] = $download_model->model_path;
            $params['model_theme'] = $download_model->model_theme;
            $params['create_time'] = time();
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
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
            $this->view->assign('modelList', $this->getModelList());
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        if ((!isset($params['model_id'])) || empty($params['model_id'])) {
            $this->error(__('Module_id_empty'));
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
            // 验证域名是否已存在
            $site = $this->model->where('id', '<>', $row['id'])->where('domain', $params['domain'])->find();
            if (!empty($site)) {
                $this->error(__('Domain_unique'));
            }
            // 保存站点信息
            $model_id = $params['model_id'];
            if ($row->model_id != $model_id) {
                $download_model = WebModelDownload::find($model_id);
                $params['model_id'] = $download_model->id;
                $params['model_path'] = $download_model->model_path;
                $params['model_theme'] = $download_model->model_theme;
            }
            $params['update_time'] = time();
            $result = $row->allowField(true)->save($params);
            Db::commit();
            Redis::getInstance()->del(Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $row['domain']);
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
            $del_redis_key = [];
            foreach ($list as $item) {
                $count += $item->delete();
                $del_redis_key[] = Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $item['domain'];
            }
            Db::commit();
            Redis::getInstance()->del($del_redis_key);
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
     * @param $ids
     * @return void
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
            $del_redis_key = [];
            $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
            foreach ($list as $item) {
                $count += $item->allowField(true)->isUpdate(true)->save($values);
                $del_redis_key[] = Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $item['domain'];
            }
            Db::commit();
            Redis::getInstance()->del($del_redis_key);
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
     * 获取模板列表
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getModelList()
    {
        $result_array = [];
        $model_list = WebModelDownload::findWebModelDownloadList('id, model_name');
        foreach ($model_list as $model) {
            $result_array[$model->id] = $model->model_name;
        }
        return $result_array;
    }

}
