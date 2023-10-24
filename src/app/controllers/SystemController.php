<?php

// Extends to class System
// Only Public methods

require_once SITE_ROOT . '/app/models/SystemModel.php';

class SystemController extends System
{
    // Get number of users
    public function getSystemData()
    {
        return $this->SystemData();
    }

    public function getCaptchaImports()
    {
        return $this->getCaptcha();
    }

    private function getSecret()
    {
        return $this->getCaptchaSecret();
    }

    protected function getCapService()
    {
        return $this->getCaptchaService();
    }

    public function vaildateCaptcha()
    {
        $captcha_service = $this->getCapService();

        $secret = $this->getSecret();
        if ($captcha_service == 1) {
            $hdata = array(
                'secret' => $secret,
                'response' =>  Util::securevar($_POST['cf-turnstile-response'])
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            // var_dump($response);
            $responseData = json_decode($response);
            if ($responseData->success) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 2) {
            $hdata = array(
                'secret' => $secret,
                'response' =>  Util::securevar($_POST['h-captcha-response'])
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            // var_dump($response);
            $responseData = json_decode($response);
            if ($responseData->success) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 3) {
            $hdata = array(
                'secret' => $secret,
                'response' => Util::securevar($_POST['g-recaptcha-response'])
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($verify);
            // var_dump($response);
            $responseData = json_decode($response);
            if ($responseData->success) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 0) {
            return true;
        }
    }
}
