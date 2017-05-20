<?php 

namespace Framework\Vita\Core ;

/*
Esta interface é usada para permitir que o Vita consiga 
identificar as propriedades publicas do objeto, e fornece-las 
ao sistema gerenciador de templates Twig, de uma forma mais 
amigavel. Permitindo assim que o sistema Vita possa ser 
extendido e permitindo que uma class get ou set seja 
implementada de diferentes maneiras nos objetos e as propriedades 
do Objeto não sejam obrigatoriamente publicas.
*/
interface Vitalib
{    
    /*
	Torna publicas algumas das propriedades do Objeto que implementa 
	esta interface, no formato de Array, para que sejam acessiveis 
	atraves do gerenciador de templates. Em outras palavras, deve 
	tornar publica as propriedades que serao acessiveis ao template, Ex:
	{{ vita.minha_livraria.propriedade }}

	@return array()
    -------------------------------- */
    public function publicar();
}