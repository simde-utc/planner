<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-30
 */

namespace App\Remote\Entity;


class User
{
    public $id;
    public $name;
    public $image;

    public function getText()
    {
        return $this->name;
    }
}