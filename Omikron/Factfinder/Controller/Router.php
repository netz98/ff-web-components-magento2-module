<?php

namespace Omikron\Factfinder\Controller;

use Magento\Framework\App\ActionInterface;
use Omikron\Factfinder\Helper\Data;

/**
 * Class Router
 * Custom Router to realize the required factfinder url pattern
 *
 * @package Omikron\Factfinder\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $_response;

    /**
     * Router constructor.
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }

    /**
     * Test the incoming requests for matches to the factfinder url pattern
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|ActionInterface
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        // check if URL matches FACT-Finder front name defined in Data helper
        $pathRegex = "/^(\/" . Data::FRONT_NAME . "\/)/";
        if (!preg_match($pathRegex, $request->getPathInfo())) {
            return false;
        }

        // check if URL matches = FACT-Finder/result
        $identifier = trim($request->getPathInfo(), '/');
        $pos = strpos($identifier, "/");
        $path = substr($identifier, $pos + 1);

        if ($path == Data::CUSTOM_RESULT_PAGE) {
            $request->setModuleName('factfinder')->setControllerName('result')->setActionName('index');
        } else {
            $request->setModuleName('factfinder')->setControllerName('proxy')->setActionName('call');
        }

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}