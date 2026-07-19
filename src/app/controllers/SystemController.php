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

    public function vaildateCaptcha($data = [])
    {
        $captcha_service = $this->getCapService();

        $secret = $this->getSecret();
        if ($captcha_service == 1) {
            $captcha_response = isset($data['cf-turnstile-response']) ? $data['cf-turnstile-response'] : '';

            if (empty($captcha_response)) {
                return false;
            }

            $hdata = array(
                'secret' => $secret,
                'response' => $captcha_response
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($verify, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($verify, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($verify);
            if ($response === false) {
                return false;
            }

            $responseData = json_decode($response);
            if (is_object($responseData) && !empty($responseData->success)) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 2) {
            $captcha_response = isset($data['h-captcha-response']) ? $data['h-captcha-response'] : '';

            if (empty($captcha_response)) {
                return false;
            }

            $hdata = array(
                'secret' => $secret,
                'response' => $captcha_response
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($verify, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($verify, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($verify);
            if ($response === false) {
                return false;
            }

            $responseData = json_decode($response);
            if (is_object($responseData) && !empty($responseData->success)) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 3) {
            $captcha_response = isset($data['g-recaptcha-response']) ? $data['g-recaptcha-response'] : '';

            if (empty($captcha_response)) {
                return false;
            }

            $hdata = array(
                'secret' => $secret,
                'response' => $captcha_response
            );
            $verify = curl_init();
            curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($verify, CURLOPT_POST, true);
            curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hdata));
            curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($verify, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($verify, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($verify);
            if ($response === false) {
                return false;
            }

            $responseData = json_decode($response);
            if (is_object($responseData) && !empty($responseData->success)) {
                return true;
            } else {
                return false;
            }
        } elseif($captcha_service == 0) {
            return true;
        }

        return false;
    }
}
