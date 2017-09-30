<?php
/**
 * @link      https://github.com/dukt/rest
 * @copyright Copyright (c) 2017, Dukt
 * @license   https://github.com/dukt/restdocs/license
 */

namespace Craft;

/**
 * Rest Plugin controller
 */
class Rest_InstallController extends BaseController
{
    // Public Methods
    // =========================================================================

    /**
     * Install Index
     *
     * @return null
     */
    public function actionIndex()
    {
        $missingDependencies = craft()->rest->getMissingDependencies();
        
        $this->renderTemplate('rest/_special/install/index', [
            'missingDependencies' => $missingDependencies,
        ]);
    }
}
