<?php

namespace App\Model;

class HomeManager extends AbstractManager
{
    public function findAll()
    {
        return $this->select('SELECT * FROM argonautes', 'ASSOC');
    }

    public function add(string $argonaute)
    {
        return $this->insert('argonautes', ['name' => $argonaute]);
    }

    public function del(int $id)
    {
        return $this->delete('argonautes', ['id' => $id]);
    }
}
