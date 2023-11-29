<?php
// tests/Entity/TicketsTest.php


namespace App\Tests\Entity;

use App\Entity\Tickets;
use App\Entity\Winners;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TicketsTest extends TestCase
{
    public function testGettersAndSetters()
    {
        // Testing title getter and setter
        $ticket = new Tickets();
        $title = 'Win a Car';
        $ticket->setTitle($title);
        $this->assertEquals($title, $ticket->getTitle());

        // Testing ticketsLeft getter and setter
        $ticketsLeft = 50;
        $ticket->setTicketsLeft($ticketsLeft);
        $this->assertEquals($ticketsLeft, $ticket->getTicketsLeft());
    }

    public function testWinnersCollection()
    {
        $ticket = new Tickets();
        $winner = new Winners();

        // Testing addWinner
        $ticket->addWinner($winner);
        $this->assertCount(1, $ticket->getWinners());
        $this->assertTrue($ticket->getWinners()->contains($winner));

        // Testing removeWinner
        $ticket->removeWinner($winner);
        $this->assertCount(0, $ticket->getWinners());
        $this->assertFalse($ticket->getWinners()->contains($winner));
    }

    public function testConstruct()
    {
        $ticket = new Tickets();
        $this->assertInstanceOf(ArrayCollection::class, $ticket->getWinners());
    }
}
