<?php

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace DERHANSEN\SfEventMgt\Controller;

use DERHANSEN\SfEventMgt\Domain\Model\Dto\EventDemand;
use DERHANSEN\SfEventMgt\Domain\Repository\CategoryRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\EventRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\LocationRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\OrganisatorRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\Registration\FieldRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\RegistrationRepository;
use DERHANSEN\SfEventMgt\Domain\Repository\SpeakerRepository;
use DERHANSEN\SfEventMgt\Pagination\NumberedPagination;
use DERHANSEN\SfEventMgt\Service\CalendarService;
use DERHANSEN\SfEventMgt\Service\ICalendarService;
use DERHANSEN\SfEventMgt\Service\NotificationService;
use DERHANSEN\SfEventMgt\Service\PaymentService;
use DERHANSEN\SfEventMgt\Service\RegistrationService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * EventController
 */
abstract class AbstractController extends ActionController
{
    protected array $ignoredSettingsForOverwriteDemand = ['storagepage', 'orderfieldallowed'];

    protected EventRepository $eventRepository;
    protected RegistrationRepository $registrationRepository;
    protected CategoryRepository $categoryRepository;
    protected LocationRepository $locationRepository;
    protected OrganisatorRepository $organisatorRepository;
    protected SpeakerRepository $speakerRepository;
    protected NotificationService $notificationService;
    protected ICalendarService $icalendarService;
    protected RegistrationService $registrationService;
    protected CalendarService $calendarService;
    protected PaymentService $paymentService;
    protected FieldRepository $fieldRepository;

    public function injectCalendarService(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function injectEventRepository(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function injectIcalendarService(ICalendarService $icalendarService)
    {
        $this->icalendarService = $icalendarService;
    }

    public function injectLocationRepository(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function injectNotificationService(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function injectOrganisatorRepository(OrganisatorRepository $organisatorRepository)
    {
        $this->organisatorRepository = $organisatorRepository;
    }

    public function injectSpeakerRepository(SpeakerRepository $speakerRepository)
    {
        $this->speakerRepository = $speakerRepository;
    }

    public function injectPaymentService(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function injectRegistrationRepository(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    public function injectRegistrationService(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function injectFieldRepository(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Returns an array with variables for the pagination. An array with pagination settings should be passed.
     * Applies default values if settings are not available:
     * - pagination disabled
     * - itemsPerPage = 10
     * - maxNumPages = 10
     *
     * @param QueryResultInterface $events
     * @param array $settings
     * @return array
     */
    protected function getPagination(QueryResultInterface $events, array $settings): array
    {
        $pagination = [];
        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        if (($settings['enablePagination'] ?? false) && (int)$settings['itemsPerPage'] > 0) {
            $paginator = new QueryResultPaginator($events, $currentPage, (int)($settings['itemsPerPage'] ?? 10));
            $pagination = new NumberedPagination($paginator, (int)($settings['maxNumPages'] ?? 10));
            $pagination = [
                'paginator' => $paginator,
                'pagination' => $pagination,
            ];
        }

        return $pagination;
    }

    /**
     * Overwrites a given demand object by an propertyName =>  $propertyValue array
     *
     * @param \DERHANSEN\SfEventMgt\Domain\Model\Dto\EventDemand $demand
     * @param array $overwriteDemand
     *
     * @return \DERHANSEN\SfEventMgt\Domain\Model\Dto\EventDemand
     */
    protected function overwriteEventDemandObject(EventDemand $demand, array $overwriteDemand): EventDemand
    {
        foreach ($this->ignoredSettingsForOverwriteDemand as $property) {
            unset($overwriteDemand[$property]);
        }

        foreach ($overwriteDemand as $propertyName => $propertyValue) {
            if (in_array(strtolower($propertyName), $this->ignoredSettingsForOverwriteDemand, true)) {
                continue;
            }
            ObjectAccess::setProperty($demand, $propertyName, $propertyValue);
        }

        return $demand;
    }

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?: null;
    }
}
