<?php
namespace ACP3\Modules\ACP3\Newsletter\Test\Helper\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;
use ACP3\Core\Test\Helpers\ColumnRenderer\AbstractColumnRendererTest;
use ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer\AccountStatusColumnRenderer;

class AccountStatusColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;
    /**
     * @var Router|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();

        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['route'])
            ->getMock();

        $this->columnRenderer = new AccountStatusColumnRenderer(
            $this->langMock,
            $this->routerMock
        );

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['status']
        ]);
        $this->dbData = [
            'id' => 123,
            'status' => 1
        ];

        $this->primaryKey = 'id';

        $expected = '<td><i class="glyphicon glyphicon-ok text-success"></i></td>';
        $this->compareResults($expected);
    }

    public function testWithDisabledStatus()
    {
        $this->langMock->expects($this->once())
            ->method('t')
            ->with('newsletter', 'activate_account')
            ->willReturn('{NEWSLETTER_ACTIVATE_ACCOUNT}');

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('acp/newsletter/accounts/activate/id_123')
            ->willReturn('/index.php/acp/newsletter/accounts/activate/id_123/');

        $this->columnData = array_merge($this->columnData, [
            'fields' => ['status']
        ]);
        $this->dbData = [
            'id' => 123,
            'status' => 0
        ];

        $this->primaryKey = 'id';

        $expected = '<td><a href="/index.php/acp/newsletter/accounts/activate/id_123/" title="{NEWSLETTER_ACTIVATE_ACCOUNT}"><i class="glyphicon glyphicon-remove text-danger"></i></a></td>';
        $this->compareResults($expected);
    }
}
