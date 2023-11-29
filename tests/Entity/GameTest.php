<?php
// tests/Entity/GameTest.php

namespace App\Tests\Entity;

use App\Entity\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testClosingDate()
    {
        $game = new Game();
        
        // Use the setter to set the closing date
        $closingDate = new \DateTime();
        $game->setClosingDate($closingDate);
        
        // Use the getter to test if the closing date has been set correctly
        $this->assertSame($closingDate, $game->getClosingDate());
    }
    
    // You can add more test methods here to cover other aspects of the Game entity.
}
