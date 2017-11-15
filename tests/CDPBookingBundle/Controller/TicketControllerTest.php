<?php

namespace CDP\BookingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Bienvenue', $client->getResponse()->getContent());
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/new');

        $this->assertContains('Formulaire de réservation', $client->getResponse()->getContent());
    }

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/add');
        $client->followRedirect();
        $this->assertContains('Formulaire de réservation', $client->getResponse()->getContent());
    }
    public function testResume()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/resume');
        $client->followRedirect();
        $this->assertContains('Formulaire de réservation', $client->getResponse()->getContent());
    }
    public function testAddRecord()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('cdp_bookingbundle_ticket_valider')->form();
        $form['cdp_bookingbundle_ticket[date][day]'] = 21;    // nom du champs obtenu via inspector de firefox
        $form['cdp_bookingbundle_ticket[date][month]'] = 01;
        $form['cdp_bookingbundle_ticket[date][year]'] = 2021;
        $form['cdp_bookingbundle_ticket[number]'] = 1;
        $form['cdp_bookingbundle_ticket[email]'] = "test@test.fr";
        $form['cdp_bookingbundle_ticket[halfday]'] = false;

        $crawler = $client->submit($form);
        $client->followRedirect();
        $this->assertContains('Coordonnées', $client->getResponse()->getContent());

    }
}
