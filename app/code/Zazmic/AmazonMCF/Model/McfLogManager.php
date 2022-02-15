<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\Api\DataObjectHelper;
use Zazmic\AmazonMCF\Api\McfLogRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogInterfaceFactory;
use Zazmic\AmazonMCF\Api\McfLogManagerInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class McfLog Manager
 * Manager class to handle add log
 */
class McfLogManager implements McfLogManagerInterface
{
    /**
     * @var Session
     */
    private $authSession;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var McfLogRepositoryInterface
     */
    private $mcfLogRepositoryInterface;
    /**
     * @var McfLogRepositoryInterface
     */
    private $mcfLogRepository;
    /**
     * @var McfLogInterfaceFactory
     */
    private $mcfLogInterfaceFactory;
    /**
     * @param Session $authSession
     * @param DataObjectHelper $dataObjectHelper
     * @param McfLogRepositoryInterface $mcfLogRepository
     * @param McfLogInterfaceFactory $mcfLogInterfaceFactory
     */
    public function __construct(
        Session $authSession,
        DataObjectHelper $dataObjectHelper,
        McfLogRepositoryInterface $mcfLogRepository,
        McfLogInterfaceFactory $mcfLogInterfaceFactory
    ) {
        $this->authSession = $authSession;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->mcfLogRepository = $mcfLogRepository;
        $this->mcfLogInterfaceFactory = $mcfLogInterfaceFactory;
    }

    /**
     * Add AMCF Store programmatically
     *
     * @param   array $data
     * @return  string
     */
    public function addLog($data)
    {

        $data['user'] = "Cron Job";
        if ( !in_array("user",$data) ) {
            if (null !== $this->authSession->getUser()){
                $data['user'] = $this->authSession->getUser()->getUsername();
            }
        }
        try {
            $collection = $this->mcfLogInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $collection,
                $data,
                McfLogInterface::class
            );
            $result = $this->mcfLogRepository->save($collection);
        } catch (LocalizedException $e) {
            return $e->getMessage();
        }
        return $result;
    }
}
