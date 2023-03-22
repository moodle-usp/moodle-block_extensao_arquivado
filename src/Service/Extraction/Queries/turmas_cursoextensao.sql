-- QUERY TURMAS
SELECT
	o.codofeatvceu
	,UPPER(
			HASH(
				CONVERT(VARCHAR(16), o.codcurceu) 
				+ CONVERT(VARCHAR(16), o.codedicurceu)
--				+ CONVERT(VARCHAR(16), o.numseqofeedi) 
				,'md5'
			)
	) AS 'idOferecimento'
	,c.nomcurceu as 'Nome do Curso'
	,o.dtainiofeatv as 'Início'
	,o.dtafimofeatv as 'Fim'
FROM dbo.OFERECIMENTOATIVIDADECEU o
    LEFT JOIN dbo.CURSOCEU c
    	ON c.codcurceu = o.codcurceu
    LEFT JOIN dbo.EDICAOCURSOOFECEU e
    	ON o.codcurceu = e.codcurceu AND o.codedicurceu = e.codedicurceu --AND o.numseqofeedi = e.numseqofeedi
WHERE YEAR(e.dtainiofeedi) >= 2023
ORDER BY codofeatvceu 

