/*  
Informação sobre os alunos e os seus cursos
*/

 SELECT 
        CM.codmtrcurceu AS 'CodMatricula'
        ,CM.codcurceu AS 'CodCurso'
        ,CM.codpes AS 'NUSP'
        ,CM.stamtrcurceu AS 'StatusMatricula'
        ,CM.codcurceu AS 'CodCurso1'
        ,C.nomcurceu AS 'NomeCurso'
        ,C.codcurceu AS 'CodCurso2'
        ,P.nompes AS 'NomePessoa'
        ,P.codpes AS 'NUSP1'
        ,ED.codcurceu AS 'codCursoExtensao'
        ,ED.codedicurceu  AS 'EdCurso'
        ,ED.dtainiofeedi AS 'InicioCurso'
        ,ED.dtafimofeedi AS 'FimCurso'
        FROM 
         MATRICULACURSOCEU CM
    LEFT JOIN CURSOCEU C
        ON CM.codcurceu = C.codcurceu
    RIGHT JOIN PESSOA P
        ON CM.codpes = P.codpes 
    RIGHT JOIN EDICAOCURSOOFECEU ED
        ON ED.codcurceu = C.codcurceu WHERE ED.dtainiofeedi LIKE '%2023%'