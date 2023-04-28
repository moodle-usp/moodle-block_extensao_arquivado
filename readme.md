# Cursos de Extensão USP

Instruções de instalação:

    cd blocks
    git clone https://github.com/SEU-FORK/moodle-block_extensao.git extensao
    cd extensao
    composer install

Projeto desenvolvido pela equipe de Moodle da USP.

- [Talita Ventura](https://github.com/TalitaVentura16) ;
- [Thiago Gomes Veríssimo](https://github.com/thiagogomesverissimo);
- [Ricardo Fontoura](https://github.com/ricardfo);
- [Octavio Augusto Potalej](https://github.com/Potalej).


Referências:

- https://gitlab.uspdigital.usp.br/atp/moodle/-/tree/edisc/blocks/usp_cursos


## Sincronização com o Apolo

```
Uso:    
    php cli/sync.php [--apagar] [--pular_ministrantes] [--pular_alunos]
    php cli/sync.php [--help|-h]

Opções:
  -h --help              Exibe essa ajuda.
  --apagar               Apaga os dados da base do Moodle e sincroniza com o Apolo do zero.
  --pular_ministrantes   Não sincroniza os ministrantes.
  --pular_alunos         Não sincroniza os alunos.
```