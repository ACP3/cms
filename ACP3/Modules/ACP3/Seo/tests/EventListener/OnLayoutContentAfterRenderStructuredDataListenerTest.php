<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\View\Event\TemplateEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OnLayoutContentAfterRenderStructuredDataListenerTest extends TestCase
{
    private MockObject&MetaStatementsServiceInterface $metaStatementsServiceMock;
    private OnLayoutContentAfterRenderStructuredDataListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $requestMock = $this->createMock(RequestInterface::class);
        $this->metaStatementsServiceMock = $this->createMock(MetaStatementsServiceInterface::class);

        $this->listener = new OnLayoutContentAfterRenderStructuredDataListener(
            $requestMock,
            $this->metaStatementsServiceMock
        );
    }

    public function testRendersStructuredData(): void
    {
        $this->metaStatementsServiceMock->method('getStructuredData')
            ->willReturn('foo');

        $event = new TemplateEvent([]);

        ($this->listener)($event);

        $this->assertSame('<script type="application/ld+json">foo</script>', $event->getContent());
    }

    public function testRendersNothingWithNoStructuredData(): void
    {
        $this->metaStatementsServiceMock->method('getStructuredData')
            ->willReturn('');

        $eventMock = $this->createMock(TemplateEvent::class);

        $eventMock->expects(self::never())
            ->method('addContent');

        ($this->listener)($eventMock);
    }
}
