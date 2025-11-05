# 🎓 Academo — Sistema de Gestão Acadêmica

Uma plataforma web de **Gestão Acadêmica** que conecta alunos, professores e coordenação em um ambiente digital eficiente, otimizando a rotina de escolas e faculdades.

---

## 🎯 O Problema que Resolvemos

O gerenciamento de notas, faltas, tarefas e a comunicação entre a instituição e os alunos é muitas vezes fragmentado, dependendo de planilhas complexas, papéis ou múltiplos sistemas que não se conversam.

O **Academo** centraliza todas essas operações em uma única plataforma web, acessível de qualquer navegador, tornando a vida acadêmica mais organizada, transparente e eficiente para todos os envolvidos.

---

## ✨ Funcionalidades Principais

O sistema possui painéis de controle modulares e seguros, com funcionalidades específicas para cada tipo de usuário.

### 🧑‍🎓 Para Alunos
- **Painel Centralizado:** Interface moderna com menu lateral fixo para navegação rápida e fluida.  
- **Consulta de Notas:** Acompanhamento em tempo real das notas lançadas, com feedback e observações dos professores.  
- **Consulta de Frequência:** Resumo geral de presença (percentual e total) e histórico detalhado por dia e aula, com filtro por matéria.  
- **Minhas Matérias:** Lista de todas as disciplinas matriculadas e professores responsáveis.  
- **Entrega de Tarefas:** Portal para visualizar tarefas pendentes, baixar arquivos e enviar trabalhos para avaliação.  

### 👨‍🏫 Para Professores
- **Painel de Controle:** Hub central para gerenciar turmas e atividades acadêmicas.  
- **Lançamento de Notas Flexível:** Criação de atividades avaliativas (ex.: "Prova 1", "Trabalho 2"), definição de valores e lançamento de notas/observações.  
- **Registro de Presença por Aula:** Sistema detalhado para registrar presença (Presente, Falta, Atraso, Justificado) para cada aula do dia.  
- **Gerenciamento de Tarefas:** Ferramenta para criar tarefas, adicionar descrições, definir prazos e avaliar trabalhos enviados pelos alunos.  

### 👩‍💼 Para Coordenação e Administração
- **Gestão Completa de Usuários:** Cadastro, edição e desativação de contas de alunos, professores e coordenadores (mantendo o histórico).  
- **Gestão de Turmas e Disciplinas:** Criação e edição de turmas/disciplinas, com atribuição de professores responsáveis.  
- **Gerenciamento de Matrículas:** Interface visual para matricular alunos em turmas ou remover matrículas existentes.  

---

## 🔒 Recursos Gerais do Sistema

- **Login Seguro:** Autenticação com senhas criptografadas (hash).  
- **Recuperação de Senha Funcional:** Sistema seguro de redefinição via e-mail (PHPMailer), com token e expiração.  
- **Modo Escuro (Dark Mode):** Alternância de tema com preferência salva no navegador.  
- **Hierarquia de Permissões:** O Admin cadastra a Coordenação, que cadastra Professores e Alunos, garantindo controle rigoroso.  

---

## 📸 Telas do Sistema

> Substitua os placeholders abaixo por imagens reais do seu projeto. Elas são essenciais para demonstrar valor visualmente!

- **Login (com Modo Escuro)**  
  [COLOQUE UM GIF OU PRINT DA TELA DE LOGIN AQUI]

- **Painel do Aluno**  
  [COLOQUE UM PRINT DO PAINEL DO ALUNO AQUI]

- **Painel do Professor (Marcar Presença)**  
  [COLOQUE UM PRINT DA TELA DE PRESENÇA AQUI]

- **Painel da Coordenação (Gerenciar Usuários)**  
  [COLOQUE UM PRINT DA TELA DE GERENCIAMENTO AQUI]

---

## 🚀 Fluxo de Uso Básico

Uma visão geral de como o sistema funciona:

1. O **Administrador** acessa o sistema e cadastra um novo usuário de Coordenação.  
2. O **Coordenador** faz login e cria novas Turmas (ex.: "Banco de Dados") e Usuários (Professores e Alunos).  
3. O **Coordenador** atribui o Professor à respectiva Turma e matricula os Alunos.  
4. O **Professor** acessa seu painel e pode:
   - **Lançar Notas** criando uma nova atividade (ex.: "Prova 1").  
   - **Marcar Presença** preenchendo a grade de aulas do dia.  
   - **Criar Tarefas** definindo prazos e descrições.  
5. O **Aluno** pode:
   - Consultar notas lançadas.  
   - Verificar frequência.  
   - Enviar arquivos de tarefas pendentes.  
6. Qualquer usuário pode usar **"Esqueci a Senha"**, receber o e-mail e redefinir a senha com segurança.  

---

## 🛠️ Tecnologias Utilizadas

- **Back-End:** PHP 8  
- **Banco de Dados:** MySQL (MariaDB)  
- **Front-End:** HTML5, CSS3, JavaScript (ES6)  

---

## 💡 Sugestões de Melhoria
- Adicionar **prints reais** ou GIFs animados das principais telas.  
- Inserir **badges** de versão, tecnologia e status no topo do README.  
- Criar uma **demo hospedada** (via Render, Vercel, ou 000webhost).  
- Adicionar um **guia de instalação local** para avaliadores técnicos.

---

Desenvolvido com 💻 e dedicação pela equipe **Academo**.
