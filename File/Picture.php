<?php

namespace VideoGamesRecords\CoreBundle\File;

class Picture
{

    protected $picture = null;

    protected $fonts = array();
    protected $colors = array();

    protected $activeColor = null;
    protected $activeFont = null;

    protected $mimeTypes = array(
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm' => 'image/x-xbitmap',
    );

    /**
     * Picture constructor.
     * @param      $width
     * @param      $height
     * @param bool $trueColor
     */
    public function __construct($width, $height, $trueColor = true)
    {
        $width = (int)$width;
        $height = (int)$height;

        if ($trueColor) {
            $this->picture = imagecreatetruecolor($width, $height);
        } else {
            $this->picture = imagecreate($width, $height);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        imagedestroy($this->picture);
    }

    /**
     * @param $picture
     * @return $this
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return false|int
     */
    public function getWidth()
    {
        return imagesx($this->picture);
    }

    /**
     * @return false|int
     */
    public function getHeight()
    {
        return imagesy($this->picture);
    }

    /**
     * @return false|resource|null
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getColor($name)
    {
        if (!isset($this->colors[$name])) {
            throw new \Exception('Unknown color ' . $name . '.');
        }
        $this->activeColor = $this->colors[$name];
        return $this->colors[$name];
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getFont($name)
    {
        if (!isset($this->fonts[$name])) {
            throw new \Exception('Unknown font ' . $name . '.');
        }
        $this->activeFont = $this->fonts[$name];
        return $this->fonts[$name];
    }

    /**
     * @param $name
     * @param $filePath
     * @return $this
     * @throws \Exception
     */
    public function addFont($name, $filePath)
    {
        $fontPath = realpath($filePath);
        if ($fontPath === false) {
            throw new \Exception('Unable to load font file. The file does not exists.');
        }
        $this->fonts[$name] = $fontPath;
        $this->activeFont = $fontPath;
        return $this;
    }

    /**
     * @param      $name
     * @param      $red
     * @param      $green
     * @param      $blue
     * @param null $alpha
     * @return $this
     */
    public function addColor($name, $red, $green, $blue, $alpha = null)
    {
        $red %= 256;
        $green %= 256;
        $blue %= 256;
        if ($alpha !== null) {
            $alpha %= 128;
            $color = imagecolorallocatealpha($this->picture, $red, $green, $blue, $alpha);
        } else {
            $color = imagecolorallocate($this->picture, $red, $green, $blue);
        }
        $this->colors[$name] = $color;
        $this->activeColor = $color;
        return $this;
    }

    /**
     * @param Picture $pic
     * @param                   $dstX
     * @param                   $dstY
     * @param int               $srcX
     * @param int               $srcY
     * @param null              $dstW
     * @param null              $dstH
     * @param null              $srcW
     * @param null              $srcH
     * @return $this
     */
    public function copyResized(
        Picture $pic,
        $dstX,
        $dstY,
        $srcX = 0,
        $srcY = 0,
        $dstW = null,
        $dstH = null,
        $srcW = null,
        $srcH = null
    )
    {
        $dstW = is_null($dstW) ? $pic->getWidth() : $dstW;
        $dstH = is_null($dstH) ? $pic->getHeight() : $dstH;
        $srcW = is_null($srcW) ? $pic->getWidth() : $srcW;
        $srcH = is_null($srcH) ? $pic->getHeight() : $srcH;

        imagecopyresized($this->picture, $pic->getPicture(), $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
        return $this;
    }

    /**
     * @param      $xA
     * @param      $yA
     * @param      $xB
     * @param      $yB
     * @param null $color
     * @return $this
     * @throws \Exception
     */
    public function addRectangle($xA, $yA, $xB, $yB, $color = null)
    {
        if ($color === null) {
            $color = $this->activeColor;
            if ($color === null) {
                throw new \Exception('No active color defined.');
            }
        }
        imagefilledrectangle($this->picture, $xA, $yA, $xB, $yB, $color);
        return $this;
    }

    /**
     * @param      $message
     * @param      $size
     * @param      $x
     * @param      $y
     * @param int  $angle
     * @param null $color
     * @param null $font
     * @return $this
     * @throws \Exception
     */
    public function write($message, $size, $x, $y, $angle = 0, $color = null, $font = null)
    {
        if ($color === null) {
            $color = $this->activeColor;
            if ($color === null) {
                throw new \Exception('No active color defined.');
            }
        }
        if ($font === null) {
            $font = $this->activeFont;
            if ($font === null) {
                throw new \Exception('No active font defined.');
            }
        }
        imagettftext($this->picture, $size, $angle, $x, $y, $color, $font, $message);
        return $this;
    }

    /**
     * @param      $file
     * @param bool $keepTrueColor
     * @return Picture
     * @throws \Exception
     */
    public static function loadFile($file, $keepTrueColor = false)
    {
        $file = realpath($file);
        if ($file === false) {
            throw new \Exception('Unable to load picture file. The file does not exists.');
        }

        $sExtension = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($sExtension) {
            case 'gd':
                $picture = imagecreatefromgd($file);
                break;
            case 'gd2':
                $picture = imagecreatefromgd2($file);
                break;
            case 'gif':
                $picture = imagecreatefromgif($file);
                break;
            case 'jpeg':
            case 'jpg':
                $picture = imagecreatefromjpeg($file);
                break;
            case 'png':
                $picture = imagecreatefrompng($file);
                break;
            case 'wbmp':
            case 'bmp':
                $picture = imagecreatefromwbmp($file);
                break;
            case 'xbm':
                $picture = imagecreatefromxbm($file);
                break;
            default:
                throw new \Exception('Unknown extension of file when converting to PHP resource.');
                break;
        }

        if ($keepTrueColor) {
            $oSelf = new self(imagesx($picture), imagesy($picture));
            $oSrc = new self(imagesx($picture), imagesy($picture));
            $oSrc->setPicture($picture);
            $oSelf->copyResized($oSrc, 0, 0);
            unset($oSrc);
        } else {
            $oSelf = new self(imagesx($picture), imagesy($picture));
            $oSelf->setPicture($picture);
        }
        return $oSelf;
    }

    /**
     * @param      $type
     * @param null $filename
     * @throws \Exception
     */
    public function downloadPicture($type, $filename = null)
    {
        if ($filename !== null) {
            header('Content-Disposition: "attachement"; filename="' . $filename . '"');
        } else {
            header('Content-Disposition: "attachement"');
        }
        $this->showPicture($type);
    }

    /**
     * @param $type
     * @throws \Exception
     */
    public function showPicture($type)
    {
        $method = $this->getMethod($type);
        $contentType = $this->getMimeType($type);

        header('Content-Type: ' . $contentType);
        $method($this->picture);
        exit;
    }

    /**
     * @param $filename
     * @return $this
     * @throws \Exception
     */
    public function savePicture($filename)
    {
        $sExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $method = $this->getMethod($sExtension);

        $method($this->picture, $filename);
        return $this;
    }

    /**
     * @param $type
     * @return string
     * @throws \Exception
     */
    protected function getMethod($type)
    {
        switch ($type) {
            case 'gd':
                $method = 'imagegd';
                break;
            case 'gd2':
                $method = 'imagegd2';
                break;
            case 'gif':
                $method = 'imagegif';
                break;
            case 'jpeg':
            case 'jpg':
                $method = 'imagejpeg';
                break;
            case 'png':
                $method = 'imagepng';
                break;
            case 'wbmp':
            case 'bmp':
                $method = 'imagewbmp';
                break;
            case 'xbm':
                $method = 'imagexbm';
                break;
            default:
                throw new \Exception('Unknown picture type.');
                break;
        }
        return $method;
    }

    /**
     * @param $extension
     * @return mixed|string
     */
    public function getMimeType($extension)
    {
        if (!isset($this->mimeTypes[$extension])) {
            return 'application/octet-stream';
        }
        return $this->mimeTypes[$extension];
    }
}