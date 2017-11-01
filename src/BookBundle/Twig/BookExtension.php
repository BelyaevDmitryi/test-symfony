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

    public function showImage(/*Путь до картинки включая имя файла:*/ $pathImage,/*параметры картинки*/ $widthImage = 100, $heigthImage = 100, /*надпись при наведении*/ $alt="", $isLoad)
    {
        if ($isLoad == 1) {
            if (isset($pathImage)) {
                $posDot = strrpos($pathImage, ".");

                $type = substr($pathImage, $posDot + 1, strlen($pathImage));
                if ($type == ".png" || $type == ".jpg" || $type == "jpeg") {
                    $out = '<img src="' . $pathImage . '" width="' . $widthImage . '" height="' . $heigthImage . '"  alt="' . $alt . '">';
                } else {
                    $out = "Ошибка! Тип файла автоматически не распознан";
                }
            } else {
                $out = "Проблема в пути (см переменную pathImage)";
            }
        } else {
            $out = "Скачивание не разрешено";
        }
        echo $out;
    }
}