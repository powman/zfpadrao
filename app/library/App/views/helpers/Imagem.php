<?php 
class Zend_View_Helper_Imagem extends Zend_View_Helper_Abstract
{
    /**
     * Helper para redimensionar imagem cropada
     *
     * @param $file - Caminho da Imagem
     * @param $width - Largura em pixels
     * @param $height - Altura em pixels
     * @param bool $keepRatio - Será que precisamos de manter a relação de aspecto para o novo imagem?
     * @return string - Retorna a miniatura
     */
    public function Imagem($file, $max_width, $max_height){
        if(!$file) return "http://placehold.it/{$max_width}x{$max_width}";
        ob_start();
        
        $imgsize = getimagesize($file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];
        
        switch($mime){
            case 'image/gif':
                $image_create = imagecreatefromgif($file);
                $func = "imagegif";
                break;
        
            case 'image/png':
                $image_create = imagecreatefrompng($file);
                $func = "imagepng";
                break;
        
            case 'image/jpeg':
                $image_create = imagecreatefromjpeg($file,null,100);
                $func = "imagejpeg";
                break;
        
            default:
                return false;
                break;
        }
        
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        imagealphablending($dst_img, false);
        imagesavealpha($dst_img, true);
         
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $image_create, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $image_create, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
        
        $baseImage = $func($dst_img);
        $imagedata = ob_get_contents();
        
       ob_end_clean();
       if($dst_img)imagedestroy($dst_img);
       return "data:".$mime.";base64,".base64_encode($imagedata);
        
    }
}