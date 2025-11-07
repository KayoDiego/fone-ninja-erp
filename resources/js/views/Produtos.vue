<template>
    <div class="produtos-container">
        <h2>Gerenciar Produtos</h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3>Cadastrar Novo Produto</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="salvarProduto">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nome" 
                            v-model="formProduto.nome"
                            required
                            minlength="3"
                        >
                    </div>
                    <div class="mb-3">
                        <label for="preco_venda" class="form-label">Preço de Venda Sugerido </label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="preco_venda" 
                            v-model="formProduto.preco_venda"
                            step="0.01"
                            min="0"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit" :disabled="salvandoDados">
                        {{ salvandoDados ? 'Cadastrando...' : 'Cadastrar Produto' }}
                    </button>
                </form>
            </div>
        </div>

        <div v-if="notificacaoProduto" :class="['alert', tipoAvisoProduto === 'success' ? 'alert-success' : 'alert-danger']">
            {{ notificacaoProduto }}
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Lista de Produtos</h3>
            </div>
            <div class="card-body">
                <div v-if="carregamentoAtivo" class="text-center">
                    <p>Carregando produtos...</p>
                </div>
                <div v-else-if="listaProdutos.length === 0" class="text-center">
                    <p>Nenhum produto cadastrado ainda.</p>
                </div>
                <table v-else class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Custo Médio</th>
                            <th>Preço de Venda</th>
                            <th>Estoque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="produto in listaProdutos" :key="produto.id">
                            <td>{{ produto.id }}</td>
                            <td>{{ produto.nome }}</td>
                            <td>R$ {{ formatarMoeda(produto.custo_medio) }}</td>
                            <td>R$ {{ formatarMoeda(produto.preco_venda) }}</td>
                            <td>{{ produto.estoque }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Produtos',
    data() {
        return {
            listaProdutos: [],
            formProduto: {
                nome: '',
                preco_venda: ''
            },
            salvandoDados: false,
            carregamentoAtivo: false,
            notificacaoProduto: '',
            tipoAvisoProduto: 'success'
        };
    },
    mounted() {
        this.buscarProdutos();
    },
    methods: {
        async buscarProdutos() {
            this.carregamentoAtivo = true;

            try {
                const resposta = await fetch('/api/produtos');
                this.listaProdutos = await resposta.json();
            } catch (erro) {
                this.exibirNotificacao('Erro ao carregar produtos', 'error');
            } finally {
                this.carregamentoAtivo = false;
            }
        },
        async salvarProduto() {
            this.salvandoDados = true;
            this.notificacaoProduto = '';

            try {
                const retorno = await fetch('/api/produtos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(this.formProduto)
                });

                if (retorno.ok) {
                    this.exibirNotificacao('Produto cadastrado com sucesso!', 'success');
                    this.formProduto.nome = '';
                    this.formProduto.preco_venda = '';
                    this.buscarProdutos();
                } else {
                    this.exibirNotificacao('Erro ao cadastrar produto', 'error');
                }
            } catch (erro) {
                this.exibirNotificacao('Erro ao cadastrar produto', 'error');
            } finally {
                this.salvandoDados = false;
            }
        },
        formatarMoeda(valor) {
            return parseFloat(valor).toFixed(2).replace('.', ',');
        },
        exibirNotificacao(texto, tipo) {
            this.notificacaoProduto = texto;
            this.tipoAvisoProduto = tipo;
            setTimeout(() => {
                this.notificacaoProduto = '';
            }, 6000);
        }
    }
};
</script>

<style scoped>
.produtos-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px;
}

.card {
    margin-bottom: 20px;
    border: 1px solid var(--bordas-suave);
    border-radius: 10px;
    background-color: #ffffff;
    box-shadow: 0 4px 14px rgba(26, 86, 43, 0.08);
}

.card-header {
    background-color: #dff7e5;
    padding: 16px;
    border-bottom: 1px solid var(--bordas-suave);
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    color: var(--verde-principal);
    font-weight: 600;
}

.card-body {
    padding: 22px;
}

.form-label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #1f2d25;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--bordas-suave);
    border-radius: 8px;
    background-color: #fbfffc;
}

.form-control:focus {
    outline: 2px solid rgba(51, 154, 79, 0.25);
}

.btn {
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

.btn-primary {
    background-color: var(--verde-principal);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background-color: var(--verde-hover);
}

.btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.btn-submit {
    margin-top: 10px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
}

.table th {
    background-color: var(--verde-principal);
    color: white;
    padding: 12px;
    text-align: left;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid var(--bordas-suave);
}

.table-striped tbody tr:nth-child(even) {
    background-color: #f0fdf4;
}

.alert {
    padding: 14px 16px;
    margin-bottom: 22px;
    border-radius: 8px;
    border: 1px solid transparent;
}

.alert-success {
    background-color: #d6f5de;
    border-color: #b6e2c3;
    color: #1b5a31;
}

.alert-danger {
    background-color: #ffe4e3;
    border-color: #f8c5c3;
    color: #711d1c;
}

.text-center {
    color: #4a5b50;
}
</style>

