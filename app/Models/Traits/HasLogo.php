<?php

namespace App\Models\Traits;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Adapter\Local;

/**
 * Class HasLogo.
 */
trait HasLogo
{
    /**
     * @return null
     */
    public function getLogoRaw()
    {
        if (!$this->hasLogo()) {
            return;
        }

        $disk = $this->getLogoDisk();

        if (!$disk->exists($this->logo)) {
            return;
        }

        return $disk->get($this->logo);
    }

    /**
     * @return bool
     */
    public function hasLogo()
    {
        return !empty($this->logo);
    }

    /**
     * @param bool $cachebuster
     *
     * @return null|string
     */
    public function getLogoURL($cachebuster = false)
    {
        if (!$this->hasLogo()) {
            return;
        }

        $disk = $this->getLogoDisk();
        $adapter = $disk->getAdapter();

        if ($adapter instanceof Local) {
            // Stored locally
            $logoUrl = url('/logo/' . $this->logo);

            if ($cachebuster) {
                $logoUrl .= '?no_cache=' . time();
            }

            return $logoUrl;
        }

        return Document::getDirectFileUrl($this->logo, $this->getLogoDisk());
    }

    public function getLogoPath()
    {
        if (!$this->hasLogo()) {
            return;
        }

        $disk = $this->getLogoDisk();
        $adapter = $disk->getAdapter();

        if ($adapter instanceof Local) {
            return $adapter->applyPathPrefix($this->logo);
        }

        return Document::getDirectFileUrl($this->logo, $this->getLogoDisk());
    }

    /**
     * @return mixed|null
     */
    public function getLogoWidth()
    {
        if (!$this->hasLogo()) {
            return;
        }

        return $this->logo_width;
    }

    /**
     * @return mixed|null
     */
    public function getLogoHeight()
    {
        if (!$this->hasLogo()) {
            return;
        }

        return $this->logo_height;
    }

    /**
     * @return string|null
     */
    public function getLogoName()
    {
        if (!$this->hasLogo()) {
            return;
        }

        return $this->logo;
    }

    /**
     * @return bool
     */
    public function isLogoTooLarge()
    {
        return $this->getLogoSize() > MAX_LOGO_FILE_SIZE;
    }

    /**
     * @return float|null
     */
    public function getLogoSize()
    {
        if (!$this->hasLogo()) {
            return;
        }

        return round($this->logo_size / 1000);
    }

    public function clearLogo(): void
    {
        $this->logo = '';
        $this->logo_width = 0;
        $this->logo_height = 0;
        $this->logo_size = 0;
    }

    protected function calculateLogoDetails(): void
    {
        $disk = $this->getLogoDisk();

        if ($disk->exists($this->account_key . '.png')) {
            $this->logo = $this->account_key . '.png';
        } elseif ($disk->exists($this->account_key . '.jpg')) {
            $this->logo = $this->account_key . '.jpg';
        }

        if (!empty($this->logo)) {
            $image = imagecreatefromstring($disk->get($this->logo));
            $this->logo_width = imagesx($image);
            $this->logo_height = imagesy($image);
            $this->logo_size = $disk->size($this->logo);
        } else {
            $this->logo = null;
        }
        $this->save();
    }

    /**
     * @return mixed
     */
    public function getLogoDisk()
    {
        return Storage::disk(env('LOGO_FILESYSTEM', 'logos'));
    }
}
