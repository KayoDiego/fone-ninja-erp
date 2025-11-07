<template>
    <div class="compras-container">
        <h2>Registrar Compra</h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3>Nova Compra</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="registrarCompra">
                    <div class="mb-3">
                        <label for="fornecedor" class="form-label">Fornecedor *</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="fornecedor" 
                            v-model="formularioCompra.fornecedor"
                            required
                        >
                    </div>

                    <h4 class="mt-4 mb-3">Produtos</h4>
                    <div v-for="(produto, indice) in formularioCompra.produtos" :key="indice" class="produto-item mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Produto *</label>
                                <select 
                                    class="form-select" 
                                    v-model="produto.id"
                                    required
                                >
                                    <option value="">Selecione um produto</option>
                                    <option v-for="opcao in opcoesProdutos" :key="opcao.id" :value="opcao.id">
                                        {{ opcao.nome }} (Estoque: {{ opcao.estoque }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade *</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    v-model.number="produto.quantidade"
                                    min="1"
                                    required
                                >
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Preço Unitário *</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    v-model.number="produto.preco_unitario"
                                    step="0.01"
                                    min="0"
                                    required
                                >
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button 
                                    type="button" 
                                    class="btn btn-danger w-100"
                                    @click="removerProduto(indice)"
                                    v-if="formularioCompra.produtos.length > 1"
                                >
                                    Remover
                                </button>
                            </div>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        class="btn btn-secondary mb-3"
                        @click="adicionarProduto"
                    >
                        + Adicionar Produto
                    </button>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" :disabled="processandoCompra">
                            {{ processandoCompra ? 'Registrando...' : 'Registrar Compra' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="retornoCompra" :class="['alert', statusRetorno === 'success' ? 'alert-success' : 'alert-danger']">
            {{ retornoCompra }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'Compras',
    data() {
        return {
            opcoesProdutos: [],
            formularioCompra: {
                fornecedor: '',
                produtos: [
                    { id: '', quantidade: 1, preco_unitario: 0 }
                ]
            },
            processandoCompra: false,
            retornoCompra: '',
            statusRetorno: 'success'
        };
    },
    mounted() {
        this.listarProdutosDisponiveis();
    },
    methods: {
        listarProdutosDisponiveis() {
            fetch('/api/produtos')
                .then((resposta) => resposta.json())
                .then((dados) => {
                    this.opcoesProdutos = dados;
                })
                .catch(() => this.mostrarFeedback('Erro ao carregar produtos.', 'error'));
        },
        adicionarProduto() {
            this.formularioCompra.produtos.push({ id: '', quantidade: 1, preco_unitario: 0 });
        },
        removerProduto(indice) {
            this.formularioCompra.produtos.splice(indice, 1);
            if (this.formularioCompra.produtos.length === 0) {
                this.adicionarProduto();
            }
        },
        registrarCompra() {
            this.processandoCompra = true;
            this.retornoCompra = '';

            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenMeta ? tokenMeta.content : '';

            fetch('/api/compras', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(this.formularioCompra)
            })
                .then(async (resposta) => {
                    const corpo = await resposta.json();
                    return { ok: resposta.ok, corpo };
                })
                .then(({ ok, corpo }) => {
                    if (ok) {
                        this.mostrarFeedback('Compra registrada com sucesso.', 'success');
                        this.formularioCompra = {
                            fornecedor: '',
                            produtos: [{ id: '', quantidade: 1, preco_unitario: 0 }]
                        };
                        this.listarProdutosDisponiveis();
                    } else {
                        this.mostrarFeedback('Erro ao registrar compra.', 'error');
                    }
                })
                .catch(() => this.mostrarFeedback('Erro ao registrar compra.', 'error'))
                .finally(() => {
                    this.processandoCompra = false;
                });
        },
        formatarMoeda(valor) {
            return parseFloat(valor).toFixed(2).replace('.', ',');
        },
        mostrarFeedback(texto, tipo) {
            this.retornoCompra = texto;
            this.statusRetorno = tipo;
            setTimeout(() => {
                this.retornoCompra = '';
            }, 6000);
        }
    }
};
</script>

<style scoped>
.compras-container {
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

.produto-item {
    background-color: #f4fbf6;
    border: 1px solid var(--bordas-suave);
    border-radius: 8px;
}

.form-label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #1f2d25;
}

.form-control,
.form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--bordas-suave);
    border-radius: 8px;
    background-color: #fbfffc;
}

.form-control:focus,
.form-select:focus {
    outline: 2px solid rgba(51, 154, 79, 0.25);
}

.btn {
    padding: 10px 20px;
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

.btn-secondary {
    background-color: #52b56d;
    color: white;
}

.btn-danger {
    background-color: #d9534f;
    color: white;
}

.btn:hover:not(:disabled) {
    transform: translateY(-1px);
}

.btn-primary:hover:not(:disabled) {
    background-color: var(--verde-hover);
}

.btn-secondary:hover:not(:disabled) {
    background-color: #439a5a;
}

.btn-danger:hover:not(:disabled) {
    background-color: #b8423f;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
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

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.col-md-2,
.col-md-3,
.col-md-4 {
    padding: 0 10px;
}

.d-flex {
    display: flex;
}

.align-items-end {
    align-items: flex-end;
}

.w-100 {
    width: 100%;
}

.mt-3,
.mt-4 {
    margin-top: 1rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}
</style>

