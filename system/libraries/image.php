<?php
class image extends library {
public $imagick;
private $file;
	public function __construct($image)
		{
		$this->file = $image;
		$this->imagick = new Imagick($image);
		}
	public function __destruct()
		{
		$this->imagick->clear();
		$this->imagick->destroy();
		}
	public function save($file = null)
		{
		$file = ($file === null) ? $this->file : $file;
		$this->imagick->writeImage($file);
		}
	public function blur($radius = 5, $sigma = 3)
		{
		$this->imagick->adaptiveBlurImage($radius, $sigma);
		}
	public function overlay($second, $x = 0, $y = 0)
		{
		$overlay = (is_string($second)) ? new Imagick($overlay) : $second->imagick;
		$this->setImageColorspace($overlay->getImageColorspace());
		$this->compositeImage($overlay, $overlay->getImageCompose(), $x, $y);
		}
	public function border($color = '#000000', $width = 1, $height = 1)
		{
		if (func_num_args() == 2) {$height = $width;}
		$this->imagick->borderImage($this->color($color), $width, $height);
		}
	public function tint($color, $opacity = 0.5)
		{
		$this->imagick->tintImage($this->color($color), $opacity)
		}
	public function scale($width, $height)
		{
		$this->imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS);
		}
	public function crop($x, $y, $width, $height)
		{
		$this->imagick->cropImage($width, $height, $x, $y);
		}
	public function flipv()
		{
		$this->imagick->flipImage();
		}
	public function fliph()
		{
		$this->imagick->flopImage();
		}
	public function colorize($color, $opacity = 1)
		{
		$this->imagick->colorizeImage($color, $opacity);
		}
	private function color($color)
		{
		$pixel = new ImagickPixel();
		$pixel->setColor($color);
		return $pixel;
		}
}
