<?php

namespace Model;

class UsuarioModel
{
    private $iId;
    private $sLogin;
    private $sSenha;
    private $sTipo;

    public function __construct(?int $iId, string $sLogin, string $sSenha, string $sTipo)
    {
        $this->iId = $iId;
        $this->sLogin = $sLogin;
        $this->sSenha = $sSenha;
        $this->sTipo = $sTipo;
    }

    public function getIId(): ?int{
        return $this->iId;
    }


    public function getSLogin(){
        return $this->sLogin;
    }

    public function getSSenha(): string
    {
        return $this->sSenha;
    }

    public function getSTipo(){
        return $this->sTipo;
    }


}