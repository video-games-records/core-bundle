<?php

namespace VideoGamesRecords\CoreBundle\File;

use Exception;
use GdImage;
use VideoGamesRecords\CoreBundle\Contracts\PictureInterface;

class Picture implements PictureInterface
{
    protected $image = null;

    protected array $fonts = [];
    protected array $colors = [];

    protected ?int $activeColor = null;
    protected ?string $activeFont = null;


    /**
     * Picture constructor.
     * @param      $width
     * @param      $height
     */
    public function __construct($width, $height)
    {
        $width = (int) $width;
        $height = (int) $height;

        $this->image = imagecreatetruecolor($width, $height);
    }

    /**
     * @param GdImage $image
     * @return Picture
     */
    public static function create(GdImage $image): Picture
    {
        $oSelf = new self(imagesx($image), imagesy($image));
        $oSrc = new self(imagesx($image), imagesy($image));
        $oSrc->setImage($image);
        $oSelf->copyResized($oSrc, 0, 0);
        unset($oSrc);
        return $oSelf;
    }

    /**
     *
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image): Picture
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth():int
    {
        return imagesx($this->image);
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    /**
     * @return GdImage|null
     */
    public function getImage(): ?GdImage
    {
        return $this->image;
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
            $color = imagecolorallocatealpha($this->image, $red, $green, $blue, $alpha);
        } else {
            $color = imagecolorallocate($this->image, $red, $green, $blue);
        }
        $this->colors[$name] = $color;
        $this->activeColor = $color;
        return $this;
    }

    /**
     * @param Picture $pic
     * @param int             $dstX
     * @param int             $dstY
     * @param int             $srcX
     * @param int             $srcY
     * @param int|null        $dstW
     * @param int|null        $dstH
     * @param int|null        $srcW
     * @param int|null        $srcH
     * @return Picture
     */
    public function copyResized(
        Picture $pic,
        int $dstX,
        int $dstY,
        int $srcX = 0,
        int $srcY = 0,
        int $dstW = null,
        int $dstH = null,
        int $srcW = null,
        int $srcH = null
    ): Picture {
        $dstW = is_null($dstW) ? $pic->getWidth() : $dstW;
        $dstH = is_null($dstH) ? $pic->getHeight() : $dstH;
        $srcW = is_null($srcW) ? $pic->getWidth() : $srcW;
        $srcH = is_null($srcH) ? $pic->getHeight() : $srcH;

        imagecopyresized($this->image, $pic->getImage(), $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
        return $this;
    }

    public function scale(int $width, int $height): void
    {
        $dstRatio = $width / $height;
        $srcW = $this->getWidth();
        $srcH = $this->getHeight();
        $srcRatio = $srcW / $srcH;

        // Is picture size < dest size do nothing
        if (($srcW <= $width) && ($srcH <= $height)) {
            return;
        }

        if ($srcRatio === $dstRatio) {
            $this->image = imagescale($this->image, $width, $height);
        } elseif ($srcRatio < $dstRatio) {
            $this->image = imagescale($this->image, $srcW * ($height / $srcH), $height);
        } else {
            $this->image = imagescale($this->image, $width, $srcH * ($width / $srcW),);
        }
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
        imagefilledrectangle($this->image, $xA, $yA, $xB, $yB, $color);
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
        imagettftext($this->image, $size, $angle, $x, $y, $color, $font, $message);
        return $this;
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
        $method = self::getGererateMethod($type);
        $contentType = self::getMimeType($type);

        header('Content-Type: ' . $contentType);
        $method($this->image);
        exit;
    }


    public function getStream($type): bool|string
    {
        $method = $this->getGererateMethod($type);
        ob_start();
        $method($this->image);
        return ob_get_clean();
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

        $method($this->image, $filename);
        return $this;
    }

    /**
     * @param $type
     * @return string
     * @throws Exception
     */
    protected static function getGererateMethod($type): string
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
    public static function getCreateMethod($type): string
    {
        if (!array_key_exists($type, self::EXTENSIONS)) {
            throw new Exception('Unknown picture type.');
        }
        return self::EXTENSIONS[$type]['create'];
    }

    /**
     * @param $extension
     * @return string
     */
    public static function getMimeType($extension): string
    {
        if (!isset(self::MIME_TYPES[$extension])) {
            return 'application/octet-stream';
        }
        return self::MIME_TYPES[$extension];
    }
}
