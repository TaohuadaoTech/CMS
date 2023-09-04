<?php

namespace app\admin\controller\web;

use app\common\controller\Backend;
use app\common\library\handle\ConfigurationHandle;
use app\common\model\WebPaymentMethod;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 支付配置
 *
 * @icon fa fa-circle-o
 */
class PaymentMethod extends Backend
{

    /**
     * PaymentMethod模型对象
     * @var \app\admin\model\web\PaymentMethod
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\web\PaymentMethod;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('channelList', WebPaymentMethod::getAllChannel());
        $this->assignconfig('channelList', WebPaymentMethod::getAllChannel());
        $this->view->assign('imageMimetype', ConfigurationHandle::getUploadImageMimetype());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
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
        $result = false;
        Db::startTrans();
        try {
            $this->token();
            $this->modelValidate = true;
            $this->modelSceneValidate = true;
            //是否采用模型验证
            if ($this->modelValidate) {
                $validateName = $params['channel'] == WebPaymentMethod::CHANNEL_MANUALLY ? 'manually' : 'auto';
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . ".{$validateName}" : $name) : $this->modelValidate;
                $this->model->validateFailException()->validate($validate);
            }
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
            if ($row->channel == WebPaymentMethod::CHANNEL_MANUALLY) {
                $row['not_manually'] = false;
                $row['not_auto'] = true;
            } else {
                $row['not_auto'] = false;
                $row['not_manually'] = true;
            }
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $result = false;
        Db::startTrans();
        try {
            $this->token();
            $this->modelValidate = true;
            $this->modelSceneValidate = true;
            $manually_pay = $params['channel'] == WebPaymentMethod::CHANNEL_MANUALLY;
            //是否采用模型验证
            if ($this->modelValidate) {
                $validateName = $manually_pay ? 'manually' : 'auto';
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . ".{$validateName}" : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            if ($manually_pay) {
                $params['pay_param_one'] = null;
                $params['pay_param_two'] = null;
                $params['pay_param_three'] = null;
            } else {
                $params['qrcode_image'] = null;
            }
            $params['update_time'] = time();
            $result = $row->allowField(true)->save($params);
            Db::commit();
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
