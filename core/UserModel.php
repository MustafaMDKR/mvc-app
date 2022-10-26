<?php
namespace app\core;


abstract class UserModel extends DBModel
{

    abstract public function getDisplayName(): string;
}