<?php

/**
 * User
 * ---------------------
 * This class is responsible for defining the user document and its properties.
 *
 * @category Document
 * @package  App\Document
 * @author   ialamin.pro@gmail.com
 *
 */

declare(strict_types=1);

namespace App\Document;

use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

/*
    * User
    ---------------------
    * This class is responsible for defining the user document and its properties.
*/

#[
    MongoDB\Document(collection: 'users')
]
class User
{
    #[ODM\Id]
    private $id;

    #[ODM\Field(type: Type::STRING)]
    private string $firstName = "";

    #[ODM\Field(type: Type::STRING)]
    private string $lastName = "";

    #[ODM\Field(type: Type::STRING)]
    private string $email = "";

    #[ODM\Field(type: Type::STRING)]
    private string $password = "";

    #[ODM\Field(type: Type::STRING)]
    private string $imagePath = "";

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    private DateTimeImmutable $createdDate;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    private DateTimeImmutable $updatedDate;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    private DateTimeImmutable $deletedDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCreatedDate(): DateTimeImmutable
    {
        return $this->createdDate;
    }

    public function setCreatedDate(DateTimeImmutable $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getUpdatedDate(): DateTimeImmutable
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(DateTimeImmutable $updatedDate): void
    {
        $this->updatedDate = $updatedDate;
    }

    public function getDeletedDate(): DateTimeImmutable
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(DateTimeImmutable $deletedDate): void
    {
        $this->deletedDate = $deletedDate;
    }


}
