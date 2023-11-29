<?php 
// tests/Entity/DistributedTicketsTest.php
namespace App\Tests\Entity;

use App\Entity\DistributedTickets;
use PHPUnit\Framework\TestCase;

class DistributedTicketsTest extends TestCase
{
    public function testNumero()
    {
        $ticket = new DistributedTickets();
        
        // Use the setter to set the numero
        $numero = '1234567890';
        $ticket->setNumero($numero);
        
        // Use the getter to test if the numero has been set correctly
        $this->assertEquals($numero, $ticket->getNumero());
    }
    
    // If you have more properties and methods, you can continue to add more test methods here.
}
