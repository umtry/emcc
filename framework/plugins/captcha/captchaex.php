<?php
/**
 * Lotus Captcha component is inspired by "cool-php-captcha" project
 * http://code.google.com/p/cool-php-captcha
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."captcha.php");

class SimpleCaptchaEx extends SimpleCaptcha
{

	public function CreateImage() {
        $ini = microtime(true);

        /** Initialization */
        $this->ImageAllocate();
        
        /** Text insertion */
        $text = $this->GetCaptchaText();
        $fontcfg  = $this->fonts[array_rand($this->fonts)];
        $this->WriteText($text, $fontcfg);

		//存session
        $_SESSION[$this->session_var.$text] = $text;

        /** Transformations */
        $this->WaveImage();
        if ($this->blur) {
            //var_dump($this->im);exit;
        	imagefilter($this->im, IMG_FILTER_SELECTIVE_BLUR);
        }
        $this->ReduceImage();


        if ($this->debug) {
            imagestring($this->im, 1, 1, $this->height-8,
                "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
                $this->GdFgColor
            );
        }


        /** Output */
        $this->WriteImage();
        $this->Cleanup();
		return $text;
    }

	
	/*protected function DisturbImage() {
		for($i=0;$i<500;$i++){
			$te2 = imagecolorallocate($this->im,rand(0,225),rand(0,225),rand(0,225));
			if ($i<3) {
				$x1=rand(-$this->conf->width*$this->conf->scale*(1/4),$this->conf->width*$this->conf->scale*(3/4));
				$y1=rand(0,$this->conf->height*$this->conf->scale);
				$x2=rand(-$this->conf->width*$this->conf->scale*(1/4),$this->conf->width*$this->conf->scale*(3/4));
				$y2=rand(0,$this->conf->height*$this->conf->scale);
				imageline($this->im,$x1,$y1,$x2,$y2,$te2);//��
				imageline($this->im,$x1+1,$y1+1,$x2+1,$y2+1,$te2);//��
				imageline($this->im,$x1+2,$y1+2,$x2+2,$y2+2,$te2);//��
				imageline($this->im,$x1+3,$y1+3,$x2+3,$y2+3,$te2);//��
			}
			//imagesetpixel($this->im,rand()%($this->conf->width)*$this->conf->scale,rand()%($this->conf->height)*$this->conf->scale,$te2);//��
			imagestring($this->im,1, mt_rand(-10, ($this->conf->width)*$this->conf->scale), mt_rand(-10, ($this->conf->height)*$this->conf->scale), chr(mt_rand(33, 48)), $te2);
		}
	}*/

}
