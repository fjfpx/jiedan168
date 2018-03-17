<?php
namespace Think;
class ImageResize
{
    private $image;
    private $img_des;
    private $image_type;
    private $permissions;
    private $compression;
 
    /**
     * 构造函数
     * @param string $img_src     源图片
     * @param string $img_des     保存图片对象
     * @param int    $compression 压缩比率，范围0-100(0:压缩比最高，100：压缩比最低)
     * @param null   $permissions 改变文件模式
     *                            mode 参数由 4 个数字组成：
     *                            第一个数字永远是 0
     *                            第二个数字规定所有者的权限
     *                            第二个数字规定所有者所属的用户组的权限
     *                            第四个数字规定其他所有人的权限
     *                            1 - 执行权限，2 - 写权限，4 - 读权限
     */
    function __construct($img_src, $img_des = NULL, $compression = 75, $permissions = null)
    {
        $image_info = getimagesize($img_src);
 
        $this->img_des = $img_des;
        $this->image_type = $image_info[2];
        $this->compression = $compression;
        $this->permissions = $permissions;
 
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = ImageCreateFromJPEG($img_src);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = ImageCreateFromGIF($img_src);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = ImageCreateFromPNG($img_src);
        }
    }
 
    /**
     * 保存图片
     */
    function save()
    {
        if ($this->image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $this->img_des, $this->compression);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $this->img_des);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $this->img_des);
        }
 
        if ($this->permissions != null) {
            chmod($this->image, $this->compression);
        }
    }
 
    /**
     * 做为图片流直接输出
     */
    function output()
    {
        
        if ($this->image_type == IMAGETYPE_JPEG) {
            header('Content-Type: image/jpg');
            imagejpeg($this->image);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            header('Content-Type: image/gif');
            imagegif($this->image);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            header('Content-Type: image/png');
            imagepng($this->image);
        }
    }
 
    /**
     * 获取图片宽度
     * @return int 图片宽度
     */
    function getWidth()
    {
        return imagesx($this->image);
    }
 
    /*
     * 获取图片高度
     * @return int 图片高度
     */
    function getHeight()
    {
        return imagesy($this->image);
    }
 
    /**
     * 按照固定高度缩放图片
     * @param $height 需要改变大小的高度
     */
    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }
 
    /**
     * 按照固定宽度缩放图片
     * @param $width 指定宽度
     */
    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        echo $height."嗯哼";
        $this->resize($width, $height);
    }
 
    /**
     * 等比缩放图片
     * @param int $scale 缩放比例
     */
    function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;
        $this->resize($width, $height);
    }
 
 
    /**
     * 指定宽度和高度缩放图片
     * @param int $width  缩放宽度
     * @param int $height 缩放高度
     * @return 缩放后图片对象
     */
    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
 
        if ($this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG) {
            $current_transparent = imagecolortransparent($this->image);
            if ($current_transparent != -1) {
                $transparent_color = imagecolorsforindex($this->image, $current_transparent);
                $current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($new_image, 0, 0, $current_transparent);
                imagecolortransparent($new_image, $current_transparent);
            } elseif ($this->image_type == IMAGETYPE_PNG) {
                imagealphablending($new_image, false);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $color);
                imagesavealpha($new_image, true);
            }
        }
 
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
}
 
?>
