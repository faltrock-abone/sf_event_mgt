<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace DERHANSEN\SfEventMgt\Tests\Unit\Domain\Model;

use DERHANSEN\SfEventMgt\Domain\Model\CustomNotificationLog;
use DERHANSEN\SfEventMgt\Domain\Model\Event;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for class \DERHANSEN\SfEventMgt\Domain\Model\CustomNotificationLog
 */
class CustomNotificationLogTest extends UnitTestCase
{
    /**
     * @var CustomNotificationLog
     */
    protected $subject;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $this->subject = new CustomNotificationLog();
    }

    /**
     * Teardown
     */
    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * Test if event can be set
     *
     * @test
     */
    public function setEventForEventSetsEvent()
    {
        $event = new Event();
        $this->subject->setEvent($event);
        self::assertEquals($event, $this->subject->getEvent());
    }

    /**
     * Test if details can be set
     *
     * @test
     */
    public function setDetailsForStringSetsDetails()
    {
        $this->subject->setDetails('Description');
        self::assertEquals('Description', $this->subject->getDetails());
    }

    /**
     * Test if emailsSent can be set
     *
     * @test
     */
    public function setEmailsSentForIntSetsEmailsSent()
    {
        $this->subject->setEmailsSent(100);
        self::assertEquals(100, $this->subject->getEmailsSent());
    }

    /**
     * Test if tstamp can be set
     *
     * @test
     */
    public function setTstampForDateTimeSetsTstamp()
    {
        $tstamp = new \DateTime('01.01.2014 10:00:00');
        $this->subject->setTstamp($tstamp);
        self::assertEquals($tstamp, $this->subject->getTstamp());
    }

    /**
     * Test if backend user can be set to field cruser_id
     *
     * @test
     */
    public function setCruserIdForBackendUserSetsBackendUserId()
    {
        $this->subject->setCruserId(1);
        self::assertEquals(1, $this->subject->getCruserId());
    }

    /**
     * @test
     */
    public function getMessageReturnsInitialValueForString()
    {
        self::assertEquals('', $this->subject->getMessage());
    }

    /**
     * @test
     */
    public function setMessageSetsMessage()
    {
        $this->subject->setMessage('test');
        self::assertEquals('test', $this->subject->getMessage());
    }
}
