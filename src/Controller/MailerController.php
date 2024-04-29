<?php

namespace App\Controller;

use PhpParser\Node\Expr\Cast\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mime\Address;
use Twig\Environment as TwigEnvironment;
class MailerController extends AbstractController
{
    
    public function sendEmail(MailerInterface $mailer): Response
    {
        // Create a transport
        $transport = Transport::fromDsn('smtp://fathimaddeh88@gmail.com:wxnfnrqwjjcjzjby@smtp.gmail.com:587');

        // Create a Mailer
        $mailer = new Mailer($transport);
        $email = (new Email());
        $email->from('fathimaddeh88@gmail.com');
        $email->to('fathimaddeh88@gmail.com');
        // Create an Email object
        $email->text('The plain text version of the message.');
        // Set the HTML part using a Twig template
        $email->subject('SUCCESSFUL RESERVATION!');
      $email->html('
    <h1 style="color: #fff300; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
        YOUR RESERVATION is SUCCESSFUL
    </h1>
    <img src="public/Photos/active-body-exercise-2294353.jpg" style="width: 600px; border-radius: 50px">
    <br>
    <img src="photos/active-body-exercise-2294353.jpg" style="width: 600px; border-radius: 50px">
    <h1 style="color: #ff0000; background-color: #5bff9c; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
        THANK YOU
    </h1>
');
        // Alternatively, you can set the text part using a Twig template
        // $email->text($this->renderView('emails/signup.txt.twig', [
        //     'expiration_date' => new \DateTime('+7 days'),
        //     'username' => 'foo',
        // ]));

        // Send the email
        $mailer->send($email);

        return new Response('Email sent successfully!');
    }

}
