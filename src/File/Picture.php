<?php

namespace VideoGamesRecords\CoreBundle\File;

use Exception;
use GdImage;

class Picture
{
    protected $picture = null;

    protected array $mimeTypes = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm'  => 'image/x-xbitmap',
    ];

    private const EXTENSIONS = [
        'gd'   => ['generate' => 'imagegd', 'create' => 'imagecreatefromgd'],
        'gd2'  => ['generate' => 'imagegd2', 'create' => 'imagecreatefromgd2'],
        'gif'  => ['generate' => 'imagegif', 'create' => 'imagecreatefromgif'],
        'jpeg' => ['generate' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'],
        'jpg'  => ['generate' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'],
        'png'  => ['generate' => 'imagepng', 'create' => 'imagecreatefrompng'],
        'wbmp' => ['generate' => 'imagewbmp', 'create' => 'imagecreatefromwbmp'],
        'bmp'  => ['generate' => 'imagewbmp', 'create' => 'imagecreatefromwbmp'],
        'xbm'  => ['generate' => 'imagexbm', 'create' => 'imagecreatefromxbm']
    ];

    protected array $fonts = [];
    protected array $colors = [];

    protected ?string $activeColor = null;
    protected ?string $activeFont = null;


    /**
     * Picture constructor.
     * @param      $width
     * @param      $height
     * @param bool $trueColor
     */
    public function __construct($width, $height, bool $trueColor = true)
    {
        $width = (int) $width;
        $height = (int) $height;

        if ($trueColor) {
            $this->picture = imagecreatetruecolor($width, $height);
        } else {
            $this->picture = imagecreate($width, $height);
        }
    }

    /**
     * @param bool    $keepTrueColor
     * @param GdImage $picture
     * @return self
     */
    public static function create(bool $keepTrueColor, GdImage $picture): Picture
    {
        $oSelf = new self(imagesx($picture), imagesy($picture));
        if ($keepTrueColor) {
            $oSrc = new self(imagesx($picture), imagesy($picture));
            $oSrc->setPicture($picture);
            $oSelf->copyResized($oSrc, 0, 0);
            unset($oSrc);
        } else {
            $oSelf->setPicture($picture);
        }
        return $oSelf;
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
     * @return Picture
     */
    public function setPicture($picture): Picture
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth():int
    {
        return imagesx($this->picture);
    }

    /**
     * @return int
     */
    public function getHeight(): int
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
     * @throws Exception
     */
    public function getColor($name): mixed
    {
        if (!isset($this->colors[$name])) {
            throw new Exception('Unknown color ' . $name . '.');
        }
        $this->activeColor = $this->colors[$name];
        return $this->colors[$name];
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getFont($name): mixed
    {
        if (!isset($this->fonts[$name])) {
            throw new Exception('Unknown font ' . $name . '.');
        }
        $this->activeFont = $this->fonts[$name];
        return $this->fonts[$name];
    }

    /**
     * @param $name
     * @param $filePath
     * @return Picture
     * @throws Exception
     */
    public function addFont($name, $filePath): Picture
    {
        $fontPath = realpath($filePath);
        if ($fontPath === false) {
            throw new Exception('Unable to load font file. The file does not exists.');
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
     * @param      $alpha
     * @return Picture
     */
    public function addColor($name, $red, $green, $blue, $alpha = null): Picture
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
     * @return Picture
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
    ): Picture {
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
     * @return Picture
     * @throws Exception
     */
    public function addRectangle($xA, $yA, $xB, $yB, $color = null): Picture
    {
        if ($color === null) {
            $color = $this->activeColor;
            if ($color === null) {
                throw new Exception('No active color defined.');
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
     * @return Picture
     * @throws Exception
     */
    public function write($message, $size, $x, $y, int $angle = 0, $color = null, $font = null): Picture
    {
        if ($color === null) {
            $color = $this->activeColor;
            if ($color === null) {
                throw new Exception('No active color defined.');
            }
        }
        if ($font === null) {
            $font = $this->activeFont;
            if ($font === null) {
                throw new Exception('No active font defined.');
            }
        }
        imagettftext($this->picture, $size, $angle, $x, $y, $color, $font, $message);
        return $this;
    }

    /**
     * @param      $file
     * @param bool $keepTrueColor
     * @return Picture
     * @throws Exception
     */
    public static function loadFile($file, bool $keepTrueColor = false): Picture
    {
        $file = realpath($file);
        if ($file === false) {
            throw new Exception('Unable to load picture file. The file does not exists.');
        }

        $extension = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!array_key_exists($extension, self::EXTENSIONS)) {
            throw new Exception('Unknown extension of file when converting to PHP resource.');
        }

        $method = self::getCreateMethod($extension);
        $picture = $method($file);

        return self::create($keepTrueColor, $picture);
    }

    /**
     * @param string $data
     * @param bool $keepTrueColor
     * @return Picture
     * @throws Exception
     */
    public static function loadFileFromStream(string $data, bool $keepTrueColor = false): Picture
    {
        $picture = imagecreatefromstring($data);

        return self::create($keepTrueColor, $picture);
    }

    /**
     * @param        $type
     * @param string $filename
     * @throws Exception
     */
    public function downloadPicture($type, string $filename): void
    {
        header('Content-Disposition: "attachement"; filename="' . $filename . '"');
        $this->showPicture($type);
    }

    /**
     * @param $type
     * @throws Exception
     */
    public function showPicture($type)
    {
        $method = $this->getGererateMethod($type);
        $contentType = $this->getMimeType($type);

        header('Content-Type: ' . $contentType);
        $method($this->picture);
        exit;
    }

    /**
     * @param $filename
     * @return $this
     * @throws Exception
     */
    public function savePicture($filename): static
    {
        $sExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $method = $this->getGererateMethod($sExtension);

        $method($this->picture, $filename);
        return $this;
    }

    /**
     * @param $type
     * @return string
     * @throws Exception
     */
    protected function getGererateMethod($type): string
    {
        if (!array_key_exists($type, self::EXTENSIONS)) {
            throw new Exception('Unknown picture type.');
        }
        return self::EXTENSIONS[$type]['generate'];
    }

    /**
     * @param $type
     * @return string
     * @throws Exception
     */
    protected static function getCreateMethod($type): string
    {
        if (!array_key_exists($type, self::EXTENSIONS)) {
            throw new Exception('Unknown picture type.');
        }
        return self::EXTENSIONS[$type]['create'];
    }

    /**
     * @param $extension
     * @return mixed|string
     */
    public function getMimeType($extension): mixed
    {
        if (!isset($this->mimeTypes[$extension])) {
            return 'application/octet-stream';
        }
        return $this->mimeTypes[$extension];
    }
}
