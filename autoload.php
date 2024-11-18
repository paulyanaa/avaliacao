<?php

spl_autoload_register(function ($sClasse) {

    $sArquivo = __DIR__ . "/Controller/$sClasse.php";
    if(file_exists($sArquivo)){
        include $sArquivo;
    } else {
        throw new Exception("Classe '$sClasse' não encontrada.");
    }
});