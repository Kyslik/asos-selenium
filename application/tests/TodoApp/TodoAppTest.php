<?php

namespace Tests\TodoApp;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Tests\AbstractTestCase;

class TodoAppTest extends AbstractTestCase
{
    protected $url = 'http://todo.dev';

    public function testTodoAppHome()
    {
        $this->webDriver->get($this->url);
        $this->assertContains('TodoMVC', $this->webDriver->getTitle());
    }

    public function testAddingTodos()
    {
        $this->prepTodo();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $todoClimb = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/label'));
        $todoWrite = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[2]/div/label'));
        $todoWater = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[3]/div/label'));

        $this->assertEquals(3, count($elements));
        $this->assertEquals('Climb Mount Everest', $todoClimb->getText());
        $this->assertEquals('Write presentation', $todoWrite->getText());
        $this->assertEquals('Water plants & flowers', $todoWater->getText());
    }

    public function testMarkingAsComplete()
    {
        $this->prepTodo();
        $todo = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/input'));
        $todo->click();

        $todoCompleted = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]'));
        $todoClassCompleted = $todoCompleted->getAttribute('class');

        $this->assertContains('completed', $todoClassCompleted);
    }

    public function testDestroyingTodo()
    {
        $this->prepTodo();
        $todo = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/label'));
        $todoDestroy = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li/div/button'));

        $this->assertFalse($todoDestroy->isDisplayed());
        $this->action->moveToElement($todo)->click($todoDestroy)->perform();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(2, count($elements));
    }

    public function testEditingTodo()
    {
        $this->prepTodo();
        $todo = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/label'));

        $this->action->doubleClick($todo)->perform();
        $todoEditing = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]'));
        $todoClassEditing = $todoEditing->getAttribute('class');

        $this->assertContains('editing', $todoClassEditing);
    }

    public function testMakeAnEditToTodo()
    {
        $this->prepTodo();
        $todoButton = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li/input'));
        $todoLabel = $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/label'));

        $this->action->doubleClick($todoLabel)->perform();
        $todoButton->clear()->sendKeys('Making an edit to task');
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);

        $this->assertEquals('Making an edit to task', $todoLabel->getText());
    }

    public function testSeeAllTodos()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/ul/li[1]/a'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(3, count($elements));
    }

    public function testSeeActiveTodos()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/ul/li[2]/a'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(2, count($elements));
    }

    public function testSeeCompletedTodos()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/ul/li[3]/a'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(1, count($elements));
    }

    public function testClearCompletedTodos()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[1]/div/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/ul/li[2]/div/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/button'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(1, count($elements));
    }

    public function testCheckAllTodosAsCompleted()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/input'))->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/ul/li[2]/a'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(0, count($elements));
    }

    public function testCheckAllTodosAsActive()
    {
        $this->prepTodo();

        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/section/input'))->click()->click();
        $this->webDriver->findElement(WebDriverBy::xpath('/html/body/section/footer/ul/li[2]/a'))->click();

        $elements = $this->webDriver->findElements(WebDriverBy::xpath('/html/body/section/section/ul/li'));
        $this->assertEquals(3, count($elements));
    }

    protected function waitForUserInput()
    {
        if (trim(fgets(fopen("php://stdin", "r"))) != chr(13)) {
            return;
        }
    }

    protected function assertElementNotFound($by)
    {
        $els = $this->webDriver->findElements($by);
        if (count($els)) {
            $this->fail("Unexpectedly element was found");
        }

        $this->assertTrue(true);
    }
}
