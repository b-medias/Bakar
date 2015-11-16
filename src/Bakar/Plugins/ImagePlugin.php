<?php
/**
*	ImagePlugin
*	Version 1.0
*/
namespace Bakar\Plugins;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use DateTime;



class ImagePlugin extends AbstractPlugin{
	protected $imagine;
	protected $imagineGD;
	protected $imagineImagick;
	protected $imagineGmagick;
	protected $images;
	protected $dateTime;
	
	protected $uploadedImages	=	array();
	protected $imagesPrepared	=	array();
	
	
	public function setImagine($imagine = NULL){
		if($imagine !== NULL){
			$this->imagine = $imagine;
		}
		return $this;
	}
	public function getImagine(){
		if($this->imagine === NULL){
			$this->setImagine(new \Imagine\Gd\Imagine());
		}
		return $this->imagine;
	}
	
	
	public function setImagineGD($imagineGD = NULL){
		if($imagineGD !== NULL){
			$this->imagineGD = $imagineGD;
		}
		return $this;
	}
	public function getImagineGD(){
		if($this->imagineGD === NULL){
			$this->setImagineGD(new \Imagine\Gd\Imagine());
		}
		return $this->imagineGD;
	}
	public function setImagineImagick($imagineImagick = NULL){
		if($imagineImagick !== NULL){
			$this->imagineImagick = $imagineImagick;
		}
		return $this;
	}
	public function getImagineImagick(){
		if($this->imagineImagick === NULL){
			$this->setImagineImagick(new \Imagine\Imagick\Imagine());
		}
		return $this->imagineImagick;
	}
	public function setImagineGmagick($imagineGmagick = NULL){
		if($imagineGmagick !== NULL){
			$this->imagineGmagick = $imagineGmagick;
		}
		return $this;
	}
	public function getImagineGmagick(){
		if($this->imagineGmagick === NULL){
			$this->setImagineGmagick(new \Imagine\Gmagick\Imagine());
		}
		return $this->imagineGmagick;
	}
	
	public function setBox($box = NULL){
		if($box !== NULL){
			$this->box = $box;
		}
		return $this->box;
	}
	public function getBox($width, $height){
		$this->setBox(new \Imagine\Image\Box($width, $height));
		return $this->box;
	}
	
	
	public function setUploadedImages($uploadedImages =	NULL){
		if($uploadedImages !== NULL){
			if(is_array($uploadedImages)){
				foreach($uploadedImages as $image){
					array_push($this->uploadedImages, $image);
				}
			}
		}
		return $this;
	}
	public function getUploadedImages(){
		return $this->uploadedImages;
	}
	
	public function setDateTime($dateTime = NULL){
		if($dateTime !== NULL){
			$this->dateTime = $dateTime;
		}
		return $this;
	}
	public function getDateTime(){
		if($this->dateTime === NULL){
			$this->setDateTime(new DateTime());
		}
		return $this->dateTime;
	}
	
	public function isImages(){
		$return = FALSE;
		if(count($this->getImages()) > 0){
			$return = TRUE;
		}
		return $return;
	}
	
	
	public function setImages($images = NULL){
		if($images !== NULL){
			$this->images = $images;
		}
		return $this;
	}
	public function getImages(){
		return $this->images;
	}
	public function prepareImagesForDB(){
		foreach($this->getUploadedImages() as $image){
			if($image['error']	==	0){
				$data				=	$image;
				$data['imagine']	=	$this->getImagine()->open($image['tmp_name']);
				$data['extension']	=	$this->extractExtension($image['name']);
				$data['timeCreate']	=	$this->getDateTime();
				$data['timeUpdate']	=	$this->getDateTime();
				$data['token']		=	sha1(uniqid(rand(),true));
				$data['state']		=	1;
				$data['width']		=	$data['imagine']->getSize()->getWidth();
				$data['height']		=	$data['imagine']->getSize()->getHeight();
				$data['size']		=	$data['size'];
			
				array_push($this->imagesPrepared, $data);
			}
		}
		$this->setImages($this->imagesPrepared);
		return $this;
	}
	
	public function extractExtension($fileName){
		$extension		=	strrchr($fileName,'.');
		$extension		=	substr($extension, 1);
		$extension		=	strtolower($extension);
		return $extension;
	}
}
