<?php

namespace AlbumTest\Controller;

use Album\Model\AlbumTable;
use Album\Model\Album;
use Album\Controller\AlbumController;
use Prophecy\Argument;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AlbumControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $albumTable;

    public function setUp()
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services)
    {
        $services->setAllowOverride(true);

        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(AlbumTable::class, $this->mockAlbumTable()->reveal());

        $services->setAllowOverride(false);
    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockAlbumTable()
    {
        $this->albumTable = $this->prophesize(AlbumTable::class);
        return $this->albumTable;
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->albumTable->fetchAll()->willReturn([]);

        $this->dispatch('/album');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    public function testAddActionCanBeAccessed()
    {
        $this->dispatch('/album/add');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    public function testEditActionCanBeAccessed($id=1)
    {
        $this->albumTable->getAlbum($id)->willReturn(new Album());

        $this->dispatch('/album/edit/'.$id);
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    public function testDeleteActionCanBeAccessed($id=1)
    {
        $this->albumTable->getAlbum($id)->willReturn(new Album());
        
        $this->dispatch('/album/delete/'.$id);
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
    }


    public function testAddActionRedirectsAfterValidPost()
    {
        $this->albumTable
            ->saveAlbum(Argument::type(Album::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        ];
        $this->dispatch('/album/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testEditActionRedirectsAfterValidPost($id=1)
    {
        $this->albumTable
            ->saveAlbum(Argument::type(Album::class))
            ->shouldBeCalled();

        $this->albumTable->getAlbum($id)->willReturn(new Album());

        $postData = [
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => $id,
        ];
        $this->dispatch('/album/edit/'.$id, 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

    public function testDeleteActionRedirectsAfterValidPost($id=1)
    {
        $this->albumTable->getAlbum($id)->willReturn(new Album());

        $postData = [
            'id'     => $id,
        ];
        $this->dispatch('/album/delete/'.$id, 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/album');
    }

}
