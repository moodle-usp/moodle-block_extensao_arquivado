<?php

require_once('../../config.php');

require_once('classes/Sybase.php');

require_once("$CFG->dirroot/course/lib.php"); //Para criação de curso

require_once("$CFG->dirroot/user/lib.php"); //Para criação de usuario

//$id = required_param('id', PARAM_INT);

use block_extensao\Sybase;

$data = date(Y);

$cursos = Sybase::fetchAll("SELECT * FROM OFERECIMENTOATIVIDADECEU WHERE dtainiofeatv LIKE '%$data%'");

$codcurceu =$cursos[1]["codcurceu"];

$codofe = $cursos[1]['codofeatvceu'];

$infocur = Sybase::fetchAll("SELECT * FROM CURSOCEU WHERE codcurceu = $codcurceu");

$nomcur = $infocur[0]["nomcurceu"];

$coordid = Sybase::fetchAll("SELECT * FROM RESPONSAVELEDICAOCEU WHERE codcurceu = $codcurceu");

$codpesCoord = $coordid[0]['codpes'];

$coordnome = Sybase::fetchAll("SELECT * FROM PESSOA WHERE codpes = $codpesCoord");

$nomecord = array_column($coordnome, 'nompes');

$coordemail = Sybase::fetchAll("SELECT * FROM EMAILPESSOA WHERE codpes = $codpesCoord AND codema LIKE '%usp%'");

$emailcord = array_column($coordemail, 'codema');

print("O curso é ". $nomcur . " o coordenador se chama ". $nomecord[0] . " e seu email é ". $emailcord[0]);

$ministrante = Sybase::fetchAll("SELECT codpes FROM MINISTRANTECEU WHERE codofeatvceu = $codofe");

$ministrantecod = $ministrante[0]['codpes'];

$mininome = Sybase::fetchAll("SELECT * FROM PESSOA WHERE codpes = $ministrantecod");

$nomemini = array_column($mininome, 'nompes');

$miniemail = Sybase::fetchAll("SELECT * FROM EMAILPESSOA WHERE codpes = $ministrantecod AND codema LIKE '%usp%'");

$emailmini = array_column($miniemail, 'codema');

print(" - O curso é ". $nomcur . " o ministrante se chama ". $nomemini[0] . " e seu email é ". $emailmini[0]);

$alunos =  Sybase::fetchAll("SELECT * FROM MATRICULAATIVIDADECEU WHERE codofeatvceu = $codofe");

/*
$newcourse = new stdClass();
$newcourse->shortname = $nomcur;
$newcourse->fullname = $nomcur;
$newcourse->category = 1;

create_course($newcourse);
\core\notification::success("Curso {[$nomcur]} criado com sucesso"); 

*/

//Na tabela CURSOCEU podemos localizar tudo referente ao curso em outras tabelas pelo campo codcurceu

//Na tabela RESPONSAVELEDICAOCEU podemos localizar o codigo do docente/servidor responsavel por aquele curso pelo campo codpes

//Na tabela PESSOA podemos localizar o docente/servidor responsavel atraves do codpes

//Na tabela MATRICULACURSOCEU podemos localizar o codpes dos alunos matriculados no curso atraves do codcurceu

//Na tabela PESSOA podemos localizar os alunos matriculados atraves do codpes (Nome Completo)

//Minimo necessario para cadastrar um curso -> Nome completo do Curso, Nome breve do curso, Categoria do curso


//Anotações

//JAMES JONES (ONLINE) FOI CANCELADO

//DEMANDA -> Não inscrever os alunos diretamente. Esperar os professores aceitar.

//Quantidade de alunos no moodle

echo "<pre>";

var_dump($cursos);
die();