<?php

namespace bitby73bit\craftsubmissionschecker;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\guestentries\controllers\SaveController;
use craft\guestentries\events\SaveEvent;
use yii\base\Event;
use c10d\crafthcaptcha\CraftHcaptcha;

/**
 * submissoins-checker plugin
 *
 * @method static Plugin getInstance()
 */
class Plugin extends BasePlugin
{

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

    }

    //
    // Attach event handlers
    //
    private function attachEventHandlers(): void
    {

        //
        // Guest entries
        //
        Event::on(
            SaveController::class,
            SaveController::EVENT_BEFORE_SAVE_ENTRY,
            function(SaveEvent $e) {

                // Get a reference to the entry object:
                $entry = $e->entry;

                // Validate the captcha in the response object
                $captcha = Craft::$app->getRequest()->getParam('h-captcha-response');
                $isValid = CraftHcaptcha::$plugin->hcaptcha->verify($captcha);

                // If not valid, return error
                if (!$isValid) {
                    $e->isValid = false;
                    $entry->addError('hcaptcha','Invalid captcha response. Please try again.');
                    return false;
                }

            }
        );

    }
}
