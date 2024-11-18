<?php

use Model\UsuarioModel;

require_once '../Config/DatabaseHandler.php';
require_once '../Config/PostHandler.php';
require_once '../Config/SessaoHandler.php';
require_once '../Model/UsuarioModel.php';
require_once '../Model/UsuarioDAO.php';



class UsuarioController{
    public function __construct()
    {
        $this->oUsuarioDAO = new UsuarioDAO();
        $this->oDatabase = new DatabaseHandler("localhost", 'root', 'password','3306', 'paulyana');
        $this->oPost = new PostHandler();
    }

    public function indexUsuario(){
        require_once  __DIR__.'/../View/home-view.php';
    }

    public function salvarUsuario($sLogin, $sSenha, $sTipo){

        if(!$this->oUsuarioDAO->isUsuarioExiste($sLogin)){
            $sSenhaCriptografada = password_hash($sSenha, PASSWORD_DEFAULT);
            $this->oUsuarioDAO->save($sLogin, $sSenhaCriptografada, $sTipo);
            header('Location: lista-usuarios-view.php');
        }else{
            echo "<script>alert('Login já cadastrado. Tente novamente.');</script>";
        }
    }


    public function senhaFindByLogin($sLogin){
        $sSql = " SELECT uso_senha FROM uso_usuario WHERE uso_login = ? ";
        $sParametro = [1 => $sLogin];
        $aResultadoConsulta = $this->oDatabase->query($sSql, $sParametro);
        if($aResultadoConsulta!=[]){
            return $aResultadoConsulta[0]['uso_senha'];
        }else{
            return [];
        }
    }

    public function listarUsuariosAdmin():array
    {
        $aUsuarioAdmin = $this->oUsuarioDAO->FindByTipo('administrador');
        return $aUsuarioAdmin;
    }

    public function listarUsuariosComum():array
    {
        $aUsuarioComum = $this->oUsuarioDAO->FindByTipo('comum');
        return $aUsuarioComum;
    }

//    public function listarUsuarios()
//    {
//        $aUsuariosAdmins = $this->oUsuarioDAO->FindByTipo('administrador');
//        $aUsuariosComuns = $this->oUsuarioDAO->FindByTipo('comum');
//
//        require_once  __DIR__.'/../View/lista-usuarios-view.php';
//    }



    public function validarSenha($sLogin, $sSenha):bool{
        if ($this->oUsuarioDAO->isUsuarioExiste($sLogin)){
            return password_verify($sSenha,$this->senhaFindByLogin($sLogin));
        } else {
            return false;
        }
    }

    public function verificarTipo($sLogin){
        $tipo = ($this->oUsuarioDAO->findByLogin($sLogin))['uso_tipo'];

        if($tipo == "administrador"){
            header('Location: usuario-admin-view.php');
            exit();
        }elseif ($tipo == "comum") {
            header('Location: usuario-comum-view.php');
            exit();
        }
    }

    public function isAdmin($sLogin):bool{
        $sTipo = ($this->oUsuarioDAO->findByLogin($sLogin))['uso_tipo'];

        if($sTipo == "administrador"){
            return true;
        }else{
            return false;
        }
    }


    public function acessarSistema()
    {
        if ($this->oPost->verificarOcorrencia()) {

            $oUsuarioController = new UsuarioController();

            $sLogin = $this->oPost->getDado('login');
            $sSenha = $this->oPost->getDado('senha');

            if ($oUsuarioController->validarSenha($sLogin, $sSenha)){

                $oSessao = new SessaoHandler();

                $oSessao->setDado('login', $sLogin);
                $oUsuarioController->verificarTipo($oSessao->getDado('login'));
            } else {
                echo "<script>alert('Usuário ou senha incorretos!');</script>";
            }
        }
    }

    public function deslogarUsuario(){
        $oSessao = new SessaoHandler();
        $oSessao->deslogarSessao();
        header("Location: login-usuario.php");
        exit();
    }


    public function cadastrarUsuario(){

        $oSessao = new SessaoHandler();
        $oSessao->verificarSessao();

        $sUsuarioLogado = $oSessao->getDado('login');

        if(($this->oPost->verificarOcorrencia()) && ($this->isAdmin($sUsuarioLogado))){

            $sLogin =  $this->oPost->getDado('login');
            $sSenha =  $this->oPost->getDado('senha');
            $sTipo=  $this->oPost->getDado('tipo');

            $this->salvarUsuario($sLogin, $sSenha, $sTipo);
        }
    }

}



