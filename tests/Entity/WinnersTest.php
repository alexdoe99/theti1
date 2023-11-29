<?php
// tests/Entity/WinnersTest.php

namespace App\Tests\Entity;

use App\Entity\Winners;
use App\Entity\User;
use App\Entity\Tickets;
use PHPUnit\Framework\TestCase;

class WinnersTest extends TestCase
{
    public function testUser()
    {
        $winner = new Winners();
        $user = new User();

        $winner->setUser($user);
        $this->assertSame($user, $winner->getUser());
    }

    public function testTicket()
    {
        $winner = new Winners();
        $ticket = new Tickets();

        $winner->setTicket($ticket);
        $this->assertSame($ticket, $winner->getTicket());
    }

    public function testSend()
    {
        $winner = new Winners();
        $sendStatus = true;

        $winner->setSend($sendStatus);
        $this->assertTrue($winner->isSend());
    }
}
