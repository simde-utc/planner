<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-18
 */

namespace App\Remote\Entity;


class Asso
{
    public $id;
    public $login;
    public $name;
    public $shortname;

    public function __toString()
    {
        return $this->id;
    }
}