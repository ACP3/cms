<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Mailer;

use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Mailer\MessageProcessor;
use ACP3\Core\View;
use InlineStyle\InlineStyle;
use PHPMailer\PHPMailer\PHPMailer;

class MessageProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MessageProcessor
     */
    private $messageProcessor;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\InlineStyle\InlineStyle
     */
    private $inlineStyleMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StringFormatter
     */
    private $stringFormatterMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|View
     */
    private $viewMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PHPMailer
     */
    private $phpMailerMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->messageProcessor = new MessageProcessor(
            $this->inlineStyleMock,
            $this->stringFormatterMock,
            $this->viewMock
        );
    }

    private function setUpMockObjects()
    {
        $this->inlineStyleMock = $this->getMockBuilder(InlineStyle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stringFormatterMock = $this->getMockBuilder(StringFormatter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->phpMailerMock = $this->getMockBuilder(PHPMailer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testTextMessageWithoutSignature()
    {
        $message = (new MailerMessage())
            ->setSubject('Foo')
            ->setBody('Foobarbaz');

        $this->phpMailerMock->expects($this->never())
            ->method('html2text');

        $this->messageProcessor->process($this->phpMailerMock, $message);

        $this->assertEquals('Foobarbaz', $this->phpMailerMock->Body);
    }

    public function testTextMessageWithSignature()
    {
        $message = (new MailerMessage())
            ->setSubject('Foo')
            ->setBody('Foobarbaz')
            ->setMailSignature('Mail-Signature');

        $this->phpMailerMock->expects($this->once())
            ->method('html2text')
            ->with('Mail-Signature')
            ->willReturn('Mail-Signature');

        $this->messageProcessor->process($this->phpMailerMock, $message);

        $this->assertEquals("Foobarbaz\n-- \nMail-Signature", $this->phpMailerMock->Body);
    }
}
