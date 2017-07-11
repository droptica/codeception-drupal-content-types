<?php
/**
 * @file
 * Represents a media module browser widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal\ContentTypeRegistry\Widgets;

/**
 * Class MediaBrowserWidget
 *
 * Same as MediaWidget, but has a different label.
 *
 * @package Codeception\Module\Drupal\ContentTypeRegistry\Widgets
 */
class MediaBrowserWidget extends MediaWidget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'Media browser';
    }

    /**
     * This will search for a file in the media browser library and attach it.
     *
     * @param Actor $I
     *   The Actor.
     * @param string $value
     *   The file name to select.
     */
    public function fill($I, $value = null)
    {
        if (!$value) {
            return;
        }

        if (!method_exists($I, "waitForElementVisible")) {
            throw new \RuntimeException("WebDriver is required for MediaBrowserWidget::fill()");
        }

        $selector = $this->getSelector();

        if (!preg_match('/^#edit\-([\w\-]+)\-upload$/', $selector, $matches)) {
            throw new \InvalidArgumentException(
                "Must specify the input field, beginning with edit- and ending in -upload"
            );
        }

        $form_item_selector = ".form-item-" . $matches[1];

        $button = str_replace("-upload", "-browse-button", $selector);
        $I->click($button);
        $I->waitForElementVisible("#mediaBrowser");
        $id = uniqid();
        $I->executeJS("document.getElementById('mediaBrowser').name = '$id'");
        $I->switchToIFrame($id);
        $I->click("//a[@title='Library']");
        $I->waitForElement("#edit-filename");
        $I->fillField("#edit-filename", basename($value));
        $I->click("#edit-submit-media-default");
        $I->waitForElementVisible(".ajax-progress");
        $I->waitForElementNotVisible(".ajax-progress");
        $I->click("#media-browser-library-list img");
        $I->click("a.fake-submit");
        $I->switchToWindow();

        $fn = basename($value);
        $I->waitForElement("$form_item_selector div[title='$fn']");
    }
}
