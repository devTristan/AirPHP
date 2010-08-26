<?php
class image extends library {
private $file;
	public function __construct($image)
		{
		$this->file = $image;
		}
	private function imagick()
		{
		return new Imagick($this->file);
		}
	public function __destruct()
		{
		if (isset($this->imagick))
			{
			$this->imagick->clear();
			$this->imagick->destroy();
			}
		}
	private function width()
		{
		list($this->width, $this->height) = getimagesize($this->file);
		return $this->width;
		}
	private function height()
		{
		$this->width;
		return $this->height;
		}
	private function metadata()
		{
		$data = $this->all_metadata;
		$newdata = array();
		$vars = array('Make', 'Model', 'Software');
		
		foreach ($vars as $var)
			{
			$newdata[$var] = isset($data[$var]) ? $data[$var] : null;
			}
		
		if (isset($data['ShutterSpeedValue']))
			{
			$shutter = explode('/', $data['ShutterSpeedValue']);
			if (count($shutter))
				{
				$apex = ((float) $shutter[0]) / ((float) $shutter[1]);
				$shutter = pow(2, -$apex);
				if ($shutter >= 1)
					{
					$newdata['Shutter Speed'] = round($shutter) . 's';
					}
				elseif ($shutter != 0)
					{
					$newdata['Shutter Speed'] = '1/' . round(1 / $shutter) . 's';
					}
				}
			}
		if (isset($data['ApertureValue']))
			{
			$fstop = explode('/', $data['ApertureValue']);
			if (count($fstop))
				{
				$apex = ((float) $fstop[0]) / ((float) $fstop[1]);
				$fstop = pow(2, $apex/2);
				if ($fstop != 0)
					{
					$newdata['Aperture'] = 'f/' . round($fstop, 1);
					}
				}
			}
		if (isset($data['FocalLength']))
			{
			$focal = explode('/', $data['FocalLength']);
			if (count($focal))
				{
				$focal = ((float) $focal[0]) / ((float) $focal[1]);
				if ($focal != 0)
					{
					$newdata['Focal Length'] = round($focal, 1).'mm';
					}
				}
			}
		if (isset($data['ISOSpeedRatings']))
			{
			$newdata['ISO Speed'] = $data['ISOSpeedRatings'];
			}
		if (isset($data['DateTime']))
			{
			list($year, $month, $day, $hour, $minute, $second) = sscanf($data['DateTime'], "%d:%d:%d %d:%d:%d");
			$newdata['Date Taken'] = mktime($hour, $minute, $second, $month, $day, $year);
			}
		return $newdata;
		}
	private function all_metadata()
		{
		if ( !in_array( str::after_last($this->file, '.'), array('jpg', 'tif') ) )
			{
			return array();
			}
		return exif_read_data($this->file);
		}
	public function __get($var)
		{
		return $this->$var = $this->$var();
		}
	public function save($file = null)
		{
		$file = ($file === null) ? $this->file : $file;
		$this->imagick->writeImage($file);
		return $this;
		}
	public function blur($radius = 5, $sigma = 3)
		{
		$this->imagick->adaptiveBlurImage($radius, $sigma);
		return $this;
		}
	public function overlay($second, $x = 0, $y = 0)
		{
		$overlay = (is_string($second)) ? new Imagick($overlay) : $second->imagick;
		$this->setImageColorspace($overlay->getImageColorspace());
		$this->compositeImage($overlay, $overlay->getImageCompose(), $x, $y);
		return $this;
		}
	public function border($color = '#000000', $width = 1, $height = 1)
		{
		if (func_num_args() == 2) {$height = $width;}
		$this->imagick->borderImage($this->color($color), $width, $height);
		return $this;
		}
	public function tint($color, $opacity = 0.5)
		{
		$this->imagick->tintImage($this->color($color), $opacity);
		return $this;
		}
	public function scale($width, $height)
		{
		$this->imagick->resizeImage($width, $height, Imagick::FILTER_BOX, 1);
		return $this;
		}
	public function thumbnail($maxwidth, $maxheight)
		{
		$width_ratio = $maxwidth / $this->width;
		$height_ratio = $maxheight / $this->height;
		$ratio = ( $width_ratio > $height_ratio ) ? $height_ratio : $width_ratio;
		$this->scale( $this->width * $ratio, $this->height * $ratio );
		return $this;
		}
	public function crop($x, $y, $width, $height)
		{
		$this->imagick->cropImage($width, $height, $x, $y);
		return $this;
		}
	public function flipv()
		{
		$this->imagick->flipImage();
		return $this;
		}
	public function fliph()
		{
		$this->imagick->flopImage();
		return $this;
		}
	public function colorize($color, $opacity = 1)
		{
		$this->imagick->colorizeImage($color, $opacity);
		return $this;
		}
	private function color($color)
		{
		$pixel = new ImagickPixel();
		$pixel->setColor($color);
		return $pixel;
		}
}
