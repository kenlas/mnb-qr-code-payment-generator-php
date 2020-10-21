<?php

declare(strict_types=1);

namespace MnbQrCodePayment;

use Endroid\QrCode\QrCode;

class QrCodeImage
{
    protected $qrString;
    protected $renderer;

    public function __construct(string $qrString = null)
    {
        if (!is_null($qrString)) {
            $this->setQrString($qrString);
        }
    }

    public function setQrString(string $qrString)
    {
        $this->qrString = $qrString;

        return $this;
    }

    public function setRenderer(QrCode $renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    protected function initRenderer()
    {
        if (empty($this->qrString)) {
            throw new \Exception('empty QR string');
        }

        if (empty($this->renderer)) {
            $this->renderer = new QrCode();
            $this->renderer->setSize(300);
            $this->renderer->setMargin(10);
            $this->renderer->setWriterByName('png');
            $this->renderer->setEncoding('UTF-8');
        }

        $this->renderer->setText($this->qrString);
    }

    public function display()
    {
        $this->initRenderer();

        header('Content-Type: ' . $this->renderer->getContentType());
        echo $this->renderer->writeString();
        exit;
    }

    public function saveTo($path)
    {
        $this->initRenderer();

        $this->renderer->writeFile($path);
    }

    public function asDataUri()
    {
        $this->initRenderer();

        return $this->renderer->writeDataUri();
    }
}
