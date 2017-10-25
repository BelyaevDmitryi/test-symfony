<?php

namespace BookBundle\Twig;

class BookExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('showImage', array($this, 'showImage')),
        );
    }

    public function showImage(/*Путь до картинки включая имя файла:*/ $pathImage,/*параметры картинки*/ $widthImage = 100, $heigthImage = 100, /*надпись при наведении*/ $alt="")
    {
        $out = "";
        if(isset($pathImage)) {
            $type = substr($pathImage,strlen($pathImage)-4, 4);
            if ($type == ".png" || $type == ".jpg" || $type == "jpeg") {
                $out = '<img src="'.$pathImage.'" width="'.$widthImage.'" height="'.$heigthImage.'"  alt="'.$alt.'">';
                //$out = "<img src= {{ asset('$pathImage') }}" . " width=" . "$widthImage" . " height=" . "$heigthImage "."alt='$alt'>";
            } else {
                $out = "Ошибка! Тип файла автоматически не распознан";
            }
        } else {
            $out = "Проблема в пути (см переменную pathImage)";
        }
        echo $out;
    }
}