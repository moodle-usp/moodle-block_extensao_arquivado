-- QUERY DE RESPONSAVEIS
SELECT * FROM
	(SELECT
		o.codofeatvceu
		,UPPER(
			HASH(
				CONVERT(VARCHAR(16), o.codcurceu) 
				+ CONVERT(VARCHAR(16), o.codedicurceu)
--				+ CONVERT(VARCHAR(16), o.numseqofeedi) 
				,'md5'
			)
		) AS 'idOferecimento'
		,m.codpes
		,a.dscatc
	FROM dbo.OFERECIMENTOATIVIDADECEU o
	    LEFT JOIN MINISTRANTECEU m 
	    	ON m.codofeatvceu = o.codofeatvceu
	   	LEFT JOIN ATUACAOCEU a
	   		ON m.codatc = a.codatc
	   	LEFT JOIN dbo.EDICAOCURSOOFECEU e
			ON o.codcurceu = e.codcurceu AND o.codedicurceu = e.codedicurceu --AND o.numseqofeedi = e.numseqofeedi
	WHERE codpes IS NOT NULL
		AND YEAR(e.dtainiofeedi) >= 2023
	UNION
	SELECT
		NULL
		,UPPER(
			HASH(
				CONVERT(VARCHAR(16), r.codcurceu) 
				+ CONVERT(VARCHAR(16), r.codedicurceu)
--				+ CONVERT(VARCHAR(16), r.numseqofeedi) 
				,'md5'
			)
		) AS 'idOferecimento'
		,r.codpes
		,'Responsável'
	FROM RESPONSAVELEDICAOCEU r
		LEFT JOIN dbo.EDICAOCURSOOFECEU e
			ON r.codcurceu = e.codcurceu AND r.codedicurceu = r.codedicurceu --AND o.numseqofeedi = e.numseqofeedi
	WHERE codpes IS NOT NULL
		AND YEAR(e.dtainiofeedi) >= 2023
	) u
ORDER BY codofeatvceu
