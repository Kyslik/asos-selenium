<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use PHPUnit_Framework_TestCase;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;
    protected $action;

    public function setUp()
    {
        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::chrome();

        $options = new ChromeOptions();
        $options->addArguments([
            '--window-size=800,600',
            '--window-position=580,100',
        ]);

        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->webDriver = RemoteWebDriver::create($host, $capabilities);

        $this->action = new WebDriverActions($this->webDriver);
    }

    public function tearDown()
    {
        $this->webDriver->quit();
    }

    public function prepTodo(array $todos = [])
    {
        $this->webDriver->get($this->url);
        $input = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/header/input'));
        $input->click();

        if (empty($todos)) {
            $todos = ['Climb Mount Everest', 'Write presentation', 'Water plants & flowers'];
        }

        foreach ($todos as $todo) {
            $this->webDriver->getKeyboard()->sendKeys($todo);
            $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        }
    }
}
