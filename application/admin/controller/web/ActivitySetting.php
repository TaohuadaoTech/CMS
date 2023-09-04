<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\Redis;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\response\Json;

/**
 * 活动设置管理
 *
 * @icon fa fa-circle-o
 */
class ActivitySetting extends Backend
{

    /**
     * ActivitySetting模型对象
     * @var \app\admin\model\web\ActivitySetting
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\ActivitySetting;
        $this->view->assign('signFlgList', $this->model->getSignFlgList());
        $this->view->assign('continuityGiveTypeList', $this->model->getContinuityGiveTypeList());
        $this->view->assign('sevenDaysGiveTypeList', $this->model->getSevenDaysGiveTypeList());
        $this->view->assign('inviteFlgList', $this->model->getInviteFlgList());
        $this->view->assign('inviteStageGiveTypeList', $this->model->getInviteStageGiveTypeList());
        $this->view->assign('inviteTotalGiveTypeList', $this->model->getInviteTotalGiveTypeList());
        $this->view->assign('ipRepeatFlgList', $this->model->getIpRepeatFlgList());
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
        $row = $this->model->find();
        if (empty($row)) {
            $_id = $this->model->insertGetId(['create_time' => time()]);
            $row = $this->model->find($_id);
        }
        $this->assign('row', $row);
        return $this->view->fetch();
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
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $ids = empty($ids) ? $params['id'] : $ids;
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->error(__('No rows were updated'));
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
            $params['update_time'] = time();
            $result = $row->allowField(true)->save($params);
            Db::commit();
            $setting = $this->model->find($row['id']);
            $setting_array = $setting->toArray();
            Redis::getInstance()->hMSet(Redis::REDIS_KEY_WEB_ACTIVITY_SETTING_DETAIL, $setting_array);
            Redis::getInstance()->expire(Redis::REDIS_KEY_WEB_ACTIVITY_SETTING_DETAIL, Redis::REDIS_KEY_EXPIRE_ONE_MONTH);
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

}
