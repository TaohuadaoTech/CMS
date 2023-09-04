<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\model\WebMessageRead;
use app\common\model\WebMessages;

class Message extends Frontend
{

    protected $showTopAdvertising = false;
    protected $showBottomAdvertising = false;
    protected $showLeftAdvertising = false;
    protected $showRightAdvertising = false;

    /**
     * 消息列表
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $user = $this->auth->getUser();
        $pn = $this->getUrlPathParamsInt(0) ?: 1;
        $limit = $this->getUrlPathParamsInt(1) ?: 15;
        $page = WebMessages::findWebMessagesPage($pn, $limit);
        $message_array = $page->items();
        foreach ($message_array as &$message) {
            $message->create_time_at = date('Y-m-d', $message->create_time);
            if (mb_strlen($message->content) > 50) {
                $message->content = mb_substr($message->content, 0, 50) . '……';
            }
        }
        WebMessageRead::checkWebMessageReadFlg($user->id, $message_array);
        $page_html = $this->getPageHtml($pn, $limit, $page->total(), '/index/message/index');
        $this->view->assign('messageInfo', ['messageArray' => $message_array, 'pageHtml' => $page_html]);
        return $this->view->fetch();
    }

    /**
     * 消息详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $user = $this->auth->getUser();
        $message_id = $this->request->post('messageId');
        $message = WebMessages::find($message_id);
        $this->view->assign('message', $message);
        WebMessageRead::addWebMessageRead($user->id, $message_id);
        $this->success('success', '', ['message' => $message]);
    }

    /**
     * 消息已读
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read()
    {
        $message_id = $this->request->post('messageId');
        WebMessageRead::addWebMessageRead($this->auth->getUserId(), $message_id);
        $this->success('success');
    }

}