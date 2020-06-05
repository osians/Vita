<?php

# padronizando a saida desta pagina teste para UTF8
header('Content-Type: text/html; charset=utf-8');

# SIMULANDO UM POST
# -----------------
# 
# Aqui criam-se um $_POST ficticio para simular 
# onde a classe de validação seria mais utilizada na 
# prática.
$_POST['nome']   = '<script>alert("Olá mundo");</script>Wande$%933([rlei';
$_POST['sbnome'] = 'Santana%%%#123444 do Nascimento';
$_POST['cpf']    = '357.512.584-51';
$_POST['email']  = 'sans.pds@gmail.com';

# usamos require_once para garantir que o arquivo 
# seja inserido apenas 1 vez em todo o sistema.
require_once 'lib/validacao/input.class.php' ;

# INICIALIZANDO O OBJETO POST 
# ---------------------------
# 
# Por padrão vai a classe Post vai verificar se 
# tem informações dentro de $_POST, então vai 
# guardar essas informações internamente e destruir 
# o $_POST original para forçar o uso de Post::get();
Post::init();

# usando metodo padrao
echo Post::get( 'nome' );
echo Post::get( 'sbnome' ) . "<br>";

# usando filtros
echo Post::letras( 'nome' )   . "<br>";
echo Post::letras( 'sbnome' ) . "<br>";
echo Post::numeros( 'cpf' )   . "<br>" ;
echo Post::email( 'email' )   . "<br>";


if( Post::isEmail( 'email' ) ) {
	echo "E-mail válido" . "<br>";
}

if( Post::isCpf( 'cpf' ) ) {
	echo "CPF válido" . "<br>";
}else {
	echo "Número de CPF ". Post::get('cpf') ." parece inválido" . "<br>";
}
