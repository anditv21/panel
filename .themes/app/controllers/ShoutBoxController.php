<?php


require_once SITE_ROOT . '/app/models/ShoutBoxModel.php';

class ShoutBoxController extends ShoutBox
{
    public function postmsg($user, $uid, $msg)
    {
        return $this->sendmsg($user, $uid, $msg);
    }

    public function getmsg()
    {
        return $this->getmsgs();
    }
}
