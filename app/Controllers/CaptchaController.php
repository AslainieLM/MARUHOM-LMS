<?php

namespace App\Controllers;

use App\Models\CaptchaModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CaptchaController extends BaseController
{
    protected $captchaModel;

    public function __construct()
    {
        $this->captchaModel = new CaptchaModel();
    }

    public function image(): ResponseInterface
    {
        $code = $this->captchaModel->generateCode();
        $ipAddress = (string) $this->request->getIPAddress();
        $captchaId = $this->captchaModel->createCaptcha($code, $ipAddress);

        session()->set('captcha_id', $captchaId);

        $canvasWidth = 220;
        $canvasHeight = 70;
        $backgroundPath = WRITEPATH . 'uploads/captcha/captcha_image.jpg';

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);

        if (is_file($backgroundPath)) {
            $background = @imagecreatefromjpeg($backgroundPath);

            if ($background !== false) {
                $bgWidth = imagesx($background);
                $bgHeight = imagesy($background);
                imagecopyresampled($canvas, $background, 0, 0, 0, 0, $canvasWidth, $canvasHeight, $bgWidth, $bgHeight);
                imagedestroy($background);
            } else {
                $fallback = imagecolorallocate($canvas, 245, 245, 245);
                imagefill($canvas, 0, 0, $fallback);
            }
        } else {
            $fallback = imagecolorallocate($canvas, 245, 245, 245);
            imagefill($canvas, 0, 0, $fallback);
        }

        for ($i = 0; $i < 10; $i++) {
            $lineColor = imagecolorallocate($canvas, random_int(120, 180), random_int(120, 180), random_int(120, 180));
            imageline($canvas, random_int(0, $canvasWidth), random_int(0, $canvasHeight), random_int(0, $canvasWidth), random_int(0, $canvasHeight), $lineColor);
        }

        $textColor = imagecolorallocate($canvas, 30, 30, 30);
        imagestring($canvas, 5, 50, 26, $code, $textColor);

        ob_start();
        imagepng($canvas);
        $imageData = ob_get_clean();
        imagedestroy($canvas);

        return $this->response
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($imageData ?: '');
    }
}
