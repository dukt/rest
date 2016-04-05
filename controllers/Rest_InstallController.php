<?php
/**
 * @link      https://dukt.net/craft/rest/
 * @copyright Copyright (c) 2016, Dukt
 * @license   https://dukt.net/craft/rest/docs/license
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
