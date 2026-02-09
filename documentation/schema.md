classDiagram

    Usuario <--|> Lista : 1-N
    Lista <--|> Tarefa : 1-N
    Tarefa <--|> Subtarefa : 1-N
    Usuario <--|> Tag : 1-N
    Tarefa <--|> TarefaTag : N-M
    Usuario <--|> Projeto : 1-N
    Projeto <--|> Lista : 1-N
    Usuario <--|> Comentario : 1-N
    Tarefa <--|> Comentario : 1-N
    Usuario <--|> Anexo : 1-N
    Tarefa <--|> Anexo : 1-N
    Tarefa <--|> Lembrete : 1-N
    Usuario <--|> Notificacao : 1-N
    Tarefa <--|> HistoricoStatus : 1-N

    class Usuario {
        + id: UUID
        + nome: string
        + email: string
        + senha_hash: string
        + fuso_horario: string
        + criado_em: DateTime

        criarLista()
        criarTarefa()
        concluirTarefa()
    }

    class Projeto {
        + id: UUID
        + usuario_id: UUID
        + nome: string
        + descricao: string
        + cor: string
        + arquivado: boolean
        + criado_em: DateTime

        arquivar()
        renomear()
    }

    class Lista {
        + id: UUID
        + usuario_id: UUID
        + projeto_id: UUID
        + nome: string
        + ordem: int
        + arquivada: boolean
        + criado_em: DateTime

        reordenar()
        arquivar()
    }

    class Tarefa {
        + id: UUID
        + lista_id: UUID
        + titulo: string
        + descricao: string
        + prioridade: ENUM
        + status: ENUM
        + data_inicio: Date
        + data_vencimento: Date
        + concluida_em: DateTime
        + ordem: int
        + recorrencia: string
        + criado_em: DateTime
        + atualizado_em: DateTime

        criar()
        editar()
        concluir()
        reagendar()
    }

    class Subtarefa {
        + id: UUID
        + tarefa_id: UUID
        + titulo: string
        + concluida: boolean
        + ordem: int
        + criado_em: DateTime

        concluir()
        reordenar()
    }

    class Tag {
        + id: UUID
        + usuario_id: UUID
        + nome: string
        + cor: string
        + criado_em: DateTime
    }

    class TarefaTag {
        + tarefa_id: UUID
        + tag_id: UUID
        + criado_em: DateTime
    }

    class Comentario {
        + id: UUID
        + tarefa_id: UUID
        + usuario_id: UUID
        + conteudo: string
        + criado_em: DateTime
        + editado_em: DateTime

        editar()
        remover()
    }

    class Anexo {
        + id: UUID
        + tarefa_id: UUID
        + usuario_id: UUID
        + nome_arquivo: string
        + mime_type: string
        + tamanho_bytes: int
        + url_storage: string
        + criado_em: DateTime
    }

    class Lembrete {
        + id: UUID
        + tarefa_id: UUID
        + data_hora: DateTime
        + canal: ENUM
        + enviado: boolean
        + enviado_em: DateTime

        agendar()
        cancelar()
    }

    class Notificacao {
        + id: UUID
        + usuario_id: UUID
        + tipo: ENUM
        + titulo: string
        + mensagem: string
        + lida: boolean
        + lida_em: DateTime
        + criado_em: DateTime

        marcarComoLida()
    }

    class HistoricoStatus {
        + id: UUID
        + tarefa_id: UUID
        + status_anterior: ENUM
        + status_novo: ENUM
        + alterado_por_usuario_id: UUID
        + criado_em: DateTime
    }
