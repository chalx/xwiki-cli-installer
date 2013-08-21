<?php

namespace XWiki\Config;

use XWiki\Exceptions\FileNotFoundException;
use XWiki\Exceptions\VersionNotFoundException;

class XWikiVersion {
    private $versions;

    /**
     * @throws FileNotFoundException
     */
    public function __construct() {
        $versionFile = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "versions.json";
        if (is_readable($versionFile)) {
            $this->versions = json_decode(file_get_contents($versionFile), true);
        } else {
            throw new FileNotFoundException();
        }

    }

    /**
     * Get the download url for XWiki
     * @param $version
     * @param bool $xem
     * @return mixed
     * @throws \XWiki\Exceptions\VersionNotFoundException
     */
    public function getXWikiVersionUrl($version, $xem = false) {
        $sub = $xem?"xem":"xe";
        if(isset($this->versions['version'][$sub][$version])) {
            return $this->versions['version'][$sub][$version];
        }

        throw new VersionNotFoundException("Version {$version} not found");
    }
}