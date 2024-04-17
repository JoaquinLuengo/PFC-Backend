<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcesor implements ProcessorInterface
{
    private $mailer;

    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ProcessorInterface $removeProcessor,
        MailerInterface $mailer
    )
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($operation instanceof DeleteOperationInterface)
        {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $data = $this->hashPassword($data);

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        //$this->sendWelcomeEmail($data);

        // Handle the state
        return $result;
    }

    private function hashPassword($data)
    {
        if (!$data->getPlainPassword()) {
            return $data;

        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPlainPassword()
        );
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        return $data;

    }


}
