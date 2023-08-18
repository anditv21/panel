<?php

// Extends to class Admin
// Only Public methods

require_once SITE_ROOT . '/app/models/AdminModel.php';

class AdminController extends Admin
{
    //
    public function getUserArray()
    {
        return $this->UserArray();
    }

    //
    public function giftsub($name, $sub, $time)
    {
        return $this->subgift($name, $sub, $time);
    }
    //
    public function setnews($news)
    {
        return $this->updatenews($news);
    }
    //
    public function subcheckbyusername($name)
    {
        return $this->checksubbyun($name);
    }

    //
    public function getbannedArray()
    {
        return $this->bannedArray();
    }

    //
    public function getInvCodeArray()
    {
        return $this->invCodeArray();
    }

    //
    public function getSubCodeArray()
    {
        return $this->subCodeArray();
    }

    //
    public function resetpw($hashedPassword, $username)
    {
        return $this->pwreset($hashedPassword, $username);
    }

    //
    public function getInvCodeGen($username)
    {
        $code = Util::randomCode(20);
        return $this->invCodeGen($code, $username);
    }
    
    public function delInvCode($delinv)
    {
        return $this->deleteinvcode($delinv);
    }

    public function flushInvCode()
    {
        return $this->flushinvs();
    }

    public function delsubcode($delsub)
    {
        return $this->deletesub($delsub);
    }
    public function flushsubcodes()
    {
        return $this->flushsubs();
    }


    //
    public function getSubCodeGen($username)
    {
        $code = '1m-' . Util::randomCode(20);
        return $this->subCodeGen($code, $username);
    }

    //
    public function getSubCodeGen3M($username)
    {
        $code = '3m-' . Util::randomCode(20);
        return $this->subCodeGen($code, $username);
    }
    //
    public function getSubCodeGentrail($username)
    {
        $code = 'Trail-' . Util::randomCode(20);
        return $this->subCodeGen($code, $username);
    }

    //
    public function resetHWID($uid)
    {
        return $this->HWID($uid);
    }

    //
    public function setBannreason($reason, $uid)
    {
        return $this->bannreason($reason, $uid);
    }

    //
    public function setBanned($uid)
    {
        return $this->banned($uid);
    }

    //
    public function setAdmin($uid)
    {
        return $this->administrator($uid);
    }

    //
    public function setsupp($uid)
    {
        return $this->supporter($uid);
    }

    public function setMute($uid)
    {
        return $this->mute($uid);
    }

    //
    public function setSystemStatus()
    {
        return $this->SystemStatus();
    }

    //
    public function setSystemMaint()
    {
        return $this->SystemMaint();
    }

    public function setDiscordLink()
    {
        return $this->DiscordLink();
    }

    public function setDiscordLogging()
    {
        return $this->DiscordLogging();
    }
    //
    public function setSystemVersion($data)
    {
        return $this->SystemVersion($data);
    }

    //
    public function setSystemfreeze()
    {
        return $this->Systemfreeze();
    }

    //
    public function setinvite()
    {
        return $this->Systeminvite();
    }

    //
    public function flushchat()
    {
        return $this->chatflush();
    }

    //
    public function setshoutbox()
    {
        return $this->shoutbox();
    }

    public function giftinvites($username, $invites)
    {
        return $this->invgift($username, $invites);
    }

    public function invwave()
    {
        return $this->giftallinvs();
    }
}
