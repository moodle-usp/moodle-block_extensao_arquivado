/*  
Informação sobre o professor  
Código do tipo de atuação: 1- Docente USP, 2- Esp. Externo, 3- Monitor, 4- Servidor, 5- Docente Colaborador
*/

 SELECT 
        M.codofeatvceu AS 'Codoferecimentoatv'
        ,M.codpes AS 'NUSP'
        ,P.codpes AS 'NUSP1'
        ,P.nompes AS 'NomeMinistrante'
        ,M.codatc AS 'CodAtuacao'
        ,O.codcurceu AS 'CodCurso'
        ,O.codatvceu AS 'CodAtividade'
        ,O.codofeatvceu AS 'Codoferecimentoatv2'
        ,O.dtainiofeatv AS 'Inicio'
        ,O.dtafimofeatv AS 'Fim'
        ,C.nomcurceu AS 'NomeCurso'
        ,C.codcurceu AS 'codCursoExtensao1'
        ,ED.codcurceu AS 'codCursoExtensao'
        ,ED.codedicurceu  AS 'EdCurso'
        ,ED.dtainiofeedi AS 'InicioCurso'
        ,ED.dtafimofeedi AS 'FimCurso'
        FROM 
        MINISTRANTECEU M
    LEFT JOIN PESSOA P
        ON M.codpes = P.codpes
    RIGHT JOIN OFERECIMENTOATIVIDADECEU O 
       ON M.codofeatvceu = O.codofeatvceu
    RIGHT JOIN CURSOCEU C
    	ON C.codcurceu = O.codcurceu
    RIGHT JOIN EDICAOCURSOOFECEU ED
        ON ED.codcurceu = C.codcurceu

