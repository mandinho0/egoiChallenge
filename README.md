
# EgoiChallenge por Rui Martins

## Pré-requisitos:
- Docker instalado no sistema.

## Instalação:
1. No terminal, na raiz do projeto, correr o comando:
   ```bash
   docker-compose up --build -d
   ```

## Guia de Utilização:
A API deste projeto permite realizar operações CRUD sobre a tabela `recipients`. Esta tabela armazena o nome, email e número de telemóvel dos destinatários para o envio de SMS e EMAIL.

### Endpoints API:

- **GET**:  
  `http://localhost:8080/recipients`  
  (podes ainda adicionar `/id-do-destinatario` no final para obter um destinatário específico)

- **POST**:  
  `http://localhost:8080/recipients`  
  (podes acrescentar `/bulk` para inserir um array de destinatários)

- **PUT**:  
  `http://localhost:8080/recipients/id-do-destinatario`

- **DELETE**:  
  `http://localhost:8080/recipients/id-do-destinatario`

### Comando CLI para Envio de Mensagens:
O projeto contém um comando CLI que simula o envio de mensagens através de um serviço assíncrono. Existe um argumento opcional que pode ser utilizado para o envio através de ActiveMQ, no entanto, esta funcionalidade encontra-se temporariamente desativada devido a um problema com as configurações.

Para executar o comando, corre o seguinte na raiz do projeto:

```bash
./vendor/bin/laminas app:sendSMS
```

### Notas:
- A funcionalidade de envio via ActiveMQ não está ativa no momento.
- Certifica-te de que tens todas as dependências instaladas, incluindo o `composer`, antes de tentar executar o comando.
