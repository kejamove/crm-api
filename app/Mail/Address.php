<?php
namespace App\Mail;

class Address
{
    public $name;
    public $address;  // Changed from $email to $address

    public function __construct($address, $name = null)
    {
        $this->name = $name ?? $address;
        $this->address = $address;
    }

    public function __toString()
    {
        return $this->name ? "{$this->name} <{$this->address}>" : $this->address;
    }
}
