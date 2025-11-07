<template>
    <div class="historico-container">
        <h2>Histórico de Compras e Vendas</h2>

        <div v-if="avisoHistorico" :class="['alert', tipoNotificacaoHistorico === 'success' ? 'alert-success' : 'alert-danger']">
            {{ avisoHistorico }}
        </div>
        
        <div class="tabs mb-4">
            <button 
                class="tab-button" 
                :class="{ active: etapaSelecionada === 'compras' }"
                @click="etapaSelecionada = 'compras'"
            >
                Compras
            </button>
            <button 
                class="tab-button" 
                :class="{ active: etapaSelecionada === 'vendas' }"
                @click="etapaSelecionada = 'vendas'"
            >
                Vendas
            </button>
        </div>

        <div v-if="etapaSelecionada === 'compras'" class="card">
            <div class="card-header">
                <h3>Compras Registradas</h3>
            </div>
            <div class="card-body">
                <div v-if="buscandoCompras" class="text-center">
                    <p>Carregando compras...</p>
                </div>
                <div v-else-if="historicoCompras.length === 0" class="text-center">
                    <p>Nenhuma compra registrada ainda.</p>
                </div>
                <div v-else>
                    <div v-for="compra in historicoCompras" :key="compra.id" class="compra-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>Compra #{{ compra.id }}</strong>
                                <p class="mb-0">Fornecedor: {{ compra.fornecedor }}</p>
                                <p class="mb-0 text-muted">Data: {{ exibirData(compra.created_at) }}</p>
                            </div>
                            <div class="text-end">
                                <strong class="text-primary">Total: R$ {{ formatarValor(compra.total) }}</strong>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Produtos</strong>
                            <div class="produtos-grid">
                                <div class="produtos-cabecalho">
                                    <span>Produto</span>
                                    <span>Qtd.</span>
                                    <span>Unitário</span>
                                    <span>Subtotal</span>
                                </div>
                                <div v-for="item in compra.itens" :key="item.id" class="produtos-linha">
                                    <span class="nome">{{ item.produto.nome }}</span>
                                    <span>{{ item.quantidade }}</span>
                                    <span>R$ {{ formatarValor(item.preco_unitario) }}</span>
                                    <span>R$ {{ formatarValor(item.subtotal) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="etapaSelecionada === 'vendas'" class="card">
            <div class="card-header">
                <h3>Vendas Registradas</h3>
            </div>
            <div class="card-body">
                <div v-if="buscandoVendas" class="text-center">
                    <p>Carregando vendas...</p>
                </div>
                <div v-else-if="historicoVendas.length === 0" class="text-center">
                    <p>Nenhuma venda registrada ainda.</p>
                </div>
                <div v-else>
                    <div v-for="venda in historicoVendas" :key="venda.id" class="venda-item mb-3 p-3 border rounded" :class="{ 'cancelada': venda.cancelada }">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>Venda #{{ venda.id }}</strong>
                                <span v-if="venda.cancelada" class="badge bg-danger ms-2">CANCELADA</span>
                                <p class="mb-0">Cliente: {{ venda.cliente }}</p>
                                <p class="mb-0 text-muted">Data: {{ exibirData(venda.created_at) }}</p>
                            </div>
                            <div class="text-end">
                                <strong class="text-primary">Total: R$ {{ formatarValor(venda.total) }}</strong>
                                <p class="mb-0 text-success">Lucro: R$ {{ formatarValor(venda.lucro) }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Produtos</strong>
                            <div class="produtos-grid">
                                <div class="produtos-cabecalho">
                                    <span>Produto</span>
                                    <span>Qtd.</span>
                                    <span>Unitário</span>
                                    <span>Subtotal</span>
                                </div>
                                <div v-for="item in venda.itens" :key="item.id" class="produtos-linha">
                                    <span class="nome">{{ item.produto.nome }}</span>
                                    <span>{{ item.quantidade }}</span>
                                    <span>R$ {{ formatarValor(item.preco_unitario) }}</span>
                                    <span>R$ {{ formatarValor(item.subtotal) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="!venda.cancelada" class="mt-2">
                            <button 
                                class="btn btn-sm btn-danger"
                                @click="solicitarCancelamento(venda.id)"
                            >
                                Cancelar Venda
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Historico',
    data() {
        return {
            etapaSelecionada: 'compras',
            historicoCompras: [],
            historicoVendas: [],
            buscandoCompras: false,
            buscandoVendas: false,
            avisoHistorico: '',
            tipoNotificacaoHistorico: 'success'
        };
    },
    mounted() {
        this.obterCompras();
        this.obterVendas();
    },
    watch: {
        etapaSelecionada() {
            if (this.etapaSelecionada === 'compras' && this.historicoCompras.length === 0) {
                this.obterCompras();
            } else if (this.etapaSelecionada === 'vendas' && this.historicoVendas.length === 0) {
                this.obterVendas();
            }
        }
    },
    methods: {
        obterCompras() {
            this.buscandoCompras = true;

            fetch('/api/compras')
                .then((resposta) => resposta.json())
                .then((dados) => {
                    this.historicoCompras = dados;
                })
                .catch(() => this.exibirHistoricoAviso('Erro ao carregar compras.', 'error'))
                .finally(() => {
                    this.buscandoCompras = false;
                });
        },
        obterVendas() {
            this.buscandoVendas = true;

            fetch('/api/vendas')
                .then((resposta) => resposta.json())
                .then((dados) => {
                    this.historicoVendas = dados;
                })
                .catch(() => this.exibirHistoricoAviso('Erro ao carregar vendas.', 'error'))
                .finally(() => {
                    this.buscandoVendas = false;
                });
        },
        solicitarCancelamento(id) {
            this.exibirHistoricoAviso('Cancelando venda...', 'success');

            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenMeta ? tokenMeta.content : '';

            fetch(`/api/vendas/${id}/cancelar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(async (resposta) => {
                    const corpo = await resposta.json();
                    return { ok: resposta.ok, corpo };
                })
                .then(({ ok }) => {
                    if (ok) {
                        this.exibirHistoricoAviso('Estoque devolvido e venda cancelada.', 'success');
                        this.obterVendas();
                    } else {
                        this.exibirHistoricoAviso('Erro ao cancelar venda.', 'error');
                    }
                })
                .catch(() => this.exibirHistoricoAviso('Erro ao cancelar venda.', 'error'));
        },
        formatarValor(valor) {
            return parseFloat(valor).toFixed(2).replace('.', ',');
        },
        exibirData(data) {
            return new Date(data).toLocaleString('pt-BR');
        },
        exibirHistoricoAviso(texto, tipo) {
            const mensagemBase = texto || '';
            this.avisoHistorico = mensagemBase;
            this.tipoNotificacaoHistorico = tipo;
            setTimeout(() => {
                if (this.avisoHistorico === mensagemBase) {
                    this.avisoHistorico = '';
                }
            }, 6000);
        }
    }
};
</script>

<style scoped>
.historico-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px;
}

.tabs {
    display: flex;
    gap: 10px;
    border-bottom: 2px solid var(--bordas-suave);
    margin-bottom: 18px;
}

.tab-button {
    padding: 10px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 600;
    color: #4a5b50;
    transition: color 0.2s ease, border-color 0.2s ease;
}

.tab-button:hover {
    color: var(--verde-principal);
}

.tab-button.active {
    color: var(--verde-principal);
    border-bottom-color: var(--verde-principal);
}

.card {
    margin-bottom: 20px;
    border: 1px solid var(--bordas-suave);
    border-radius: 10px;
    background-color: #ffffff;
    box-shadow: 0 4px 14px rgba(26, 86, 43, 0.08);
}

.card-header {
    background-color: #d1f0d8;
    padding: 16px;
    border-bottom: 1px solid var(--bordas-suave);
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    color: var(--verde-principal);
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

.alert {
    padding: 14px 16px;
    margin-bottom: 20px;
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

.compra-item,
.venda-item {
    background-color: #f4fbf6;
    border: 1px solid var(--bordas-suave);
    border-radius: 8px;
}

.venda-item.cancelada {
    opacity: 0.6;
    background-color: #ffe4e3;
}

.d-flex {
    display: flex;
}

.justify-content-between {
    justify-content: space-between;
}

.align-items-start {
    align-items: flex-start;
}

.text-end {
    text-align: right;
}

.text-muted {
    color: #6b7a72;
}

.text-primary {
    color: var(--verde-principal);
}

.text-success {
    color: #2f8b4a;
}

.badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
}

.bg-danger {
    background-color: #d9534f;
    color: white;
}

.ms-2 {
    margin-left: 0.5rem;
}

.list-unstyled {
    list-style: none;
    padding-left: 0;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
}

.btn-danger {
    background-color: #d9534f;
    color: white;
}

.btn-danger:hover {
    background-color: #b8423f;
}

.mb-0 {
    margin-bottom: 0;
}

.mb-1 {
    margin-bottom: 0.25rem;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.p-3 {
    padding: 1rem;
}

.border {
    border: 1px solid var(--bordas-suave);
}

.rounded {
    border-radius: 8px;
}

/* Novos estilos para a nova estrutura de produtos */
.produtos-grid {
    margin-top: 10px;
    border: 1px solid var(--bordas-suave);
    border-radius: 8px;
    overflow: hidden;
    background-color: #ffffff;
}

.produtos-cabecalho,
.produtos-linha {
    display: grid;
    grid-template-columns: 2fr 0.7fr 1fr 1fr;
    gap: 10px;
    padding: 10px 14px;
}

.produtos-cabecalho {
    background-color: #dff7e5;
    font-weight: 600;
    color: var(--verde-principal);
}

.produtos-linha:nth-child(even) {
    background-color: #f4fbf6;
}

.produtos-linha .nome {
    font-weight: 500;
}
</style>

