<?php

use XWiki\Config\XWikiVersion;

class XWikiVersionTest extends PHPUnit_Framework_TestCase {
    /** @var  $xwikiVersion XWikiVersion */
    private $xwikiVersion;

    public function setUp() {
        $this->xwikiVersion = new XWikiVersion();
    }

    /**
     * @dataProvider xwikiVersions
     */
    public function testGetVersion($version, $xem, $expectedURL) {
        $this->assertEquals($expectedURL, $this->xwikiVersion->getXWikiVersionUrl($version, $xem));
    }

    public function xwikiVersions() {
        return array(
            array('5.1', false, 'http://download.forge.objectweb.org/xwiki/xwiki-enterprise-jetty-hsqldb-5.1.zip'),
            array('4.5.4', false, 'http://download.forge.objectweb.org/xwiki/xwiki-enterprise-jetty-hsqldb-4.5.4.zip')
        );
    }

    /**
     * @expectedException \XWiki\Exceptions\VersionNotFoundException
     * @expectedExceptionMessage Version 5.2 not found
     */
    public function testVersionNotFoundException() {
        $this->xwikiVersion->getXWikiVersionUrl(5.2);
    }
}