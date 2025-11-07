<template>
    <div class="vendas-container">
        <h2>Registrar Venda</h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h3>Nova Venda</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="registrarVenda">
                    <div class="mb-3">
                        <label for="cliente" class="form-label">Cliente *</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="cliente" 
                            v-model="formularioVenda.cliente"
                            required
                        >
                    </div>

                    <h4 class="mt-4 mb-3">Produtos</h4>
                    <div v-for="(produto, indice) in formularioVenda.produtos" :key="indice" class="produto-item mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Produto *</label>
                                <select 
                                    class="form-select" 
                                    v-model="produto.id"
                                    @change="atualizarPreco(indice)"
                                    required
                                >
                                    <option value="">Selecione um produto</option>
                                    <option v-for="opcao in catalogoProdutos" :key="opcao.id" :value="opcao.id">
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
                                    @input="calcularTotais"
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
                                    @input="calcularTotais"
                                    required
                                >
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button 
                                    type="button" 
                                    class="btn btn-danger w-100"
                                    @click="removerProduto(indice)"
                                    v-if="formularioVenda.produtos.length > 1"
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

                    <div class="resumo-venda p-3 bg-light rounded mb-3">
                        <h5>Resumo da Venda</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Total da Venda:</strong> R$ {{ formatarMoeda(totalCalculado) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Lucro Estimado:</strong> R$ {{ formatarMoeda(lucroCalculado) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" :disabled="enviandoVenda">
                            {{ enviandoVenda ? 'Registrando...' : 'Registrar Venda' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="avisoVenda" :class="['alert', tipoAvisoVenda === 'success' ? 'alert-success' : 'alert-danger']">
            {{ avisoVenda }}
        </div>
    </div>
</template>

<script>
export default {
    name: 'Vendas',
    data() {
        return {
            catalogoProdutos: [],
            formularioVenda: {
                cliente: '',
                produtos: [
                    { id: '', quantidade: 1, preco_unitario: 0 }
                ]
            },
            enviandoVenda: false,
            avisoVenda: '',
            tipoAvisoVenda: 'success',
            totalCalculado: 0,
            lucroCalculado: 0
        };
    },
    mounted() {
        this.obterProdutosVenda();
    },
    methods: {
        async obterProdutosVenda() {
            try {
                const resposta = await fetch('/api/produtos');
                this.catalogoProdutos = await resposta.json();
            } catch (erro) {
                this.exibirNotificacao('Erro ao carregar produtos.', 'error');
            }
        },
        adicionarProduto() {
            this.formularioVenda.produtos.push({ id: '', quantidade: 1, preco_unitario: 0 });
        },
        removerProduto(indice) {
            this.formularioVenda.produtos.splice(indice, 1);
            if (this.formularioVenda.produtos.length === 0) {
                this.adicionarProduto();
            }
            this.calcularTotais();
        },
        atualizarPreco(indice) {
            const item = this.formularioVenda.produtos[indice];
            const produto = this.catalogoProdutos.find((opcao) => opcao.id === item.id);
            if (produto) {
                item.preco_unitario = parseFloat(produto.preco_venda);
            }
            this.$nextTick(() => this.calcularTotais());
        },
        calcularTotais() {
            let soma = 0;
            let lucro = 0;

            this.formularioVenda.produtos.forEach((item) => {
                if (!item.id) {
                    return;
                }

                const produto = this.catalogoProdutos.find((opcao) => opcao.id === item.id);
                if (!produto) {
                    return;
                }

                const subtotal = (item.quantidade || 0) * (item.preco_unitario || 0);
                soma += subtotal;
                const custo = (item.quantidade || 0) * parseFloat(produto.custo_medio);
                lucro += subtotal - custo;
            });

            this.totalCalculado = soma;
            this.lucroCalculado = lucro;
        },
        async registrarVenda() {
            this.enviandoVenda = true;
            this.avisoVenda = '';

            try {
                const retorno = await fetch('/api/vendas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(this.formularioVenda)
                });

                if (retorno.ok) {
                    const resposta = await retorno.json();
                    this.exibirNotificacao('Venda salva.', 'success');
                    this.formularioVenda = {
                        cliente: '',
                        produtos: [{ id: '', quantidade: 1, preco_unitario: 0 }]
                    };
                    this.totalCalculado = resposta.total;
                    this.lucroCalculado = resposta.lucro;
                    this.obterProdutosVenda();
                } else {
                    this.exibirNotificacao('Erro ao registrar venda', 'error');
                }
            } catch (erro) {
                this.exibirNotificacao('Erro ao registrar venda', 'error');
            } finally {
                this.enviandoVenda = false;
            }
        },
        formatarMoeda(valor) {
            return parseFloat(valor).toFixed(2).replace('.', ',');
        },
        exibirNotificacao(texto, tipo) {
            this.avisoVenda = texto;
            this.tipoAvisoVenda = tipo;
            setTimeout(() => {
                this.avisoVenda = '';
            }, 6000);
        }
    }
};
</script>

<style scoped>
.vendas-container {
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

.resumo-venda {
    border: 1px solid var(--bordas-suave);
    background-color: #eefbf1;
    border-radius: 10px;
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
.col-md-4,
.col-md-6 {
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

.p-3 {
    padding: 1rem;
}

.bg-light {
    background-color: #eefbf1;
}

.rounded {
    border-radius: 8px;
}

.border {
    border: 1px solid var(--bordas-suave);
}
</style>

