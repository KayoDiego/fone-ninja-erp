import { createApp } from 'vue';
import Produtos from './views/Produtos.vue';
import Compras from './views/Compras.vue';
import Vendas from './views/Vendas.vue';
import Historico from './views/Historico.vue';

const App = {
    data() {
        return {
            currentView: 'produtos'
        };
    },
    methods: {
        setView(view) {
            this.currentView = view;
        }
    },
    components: {
        Produtos,
        Compras,
        Vendas,
        Historico
    },
    template: `
        <div>
            <nav class="navbar">
                <div class="brand">
                    <img src="/images/fone-ninja-logo.png" alt="Logo Fone Ninja" />
                </div>
                <div class="nav-links">
                    <a href="#" class="nav-link" :class="{ active: currentView === 'produtos' }" @click.prevent="setView('produtos')">
                        Produtos
                    </a>
                    <a href="#" class="nav-link" :class="{ active: currentView === 'compras' }" @click.prevent="setView('compras')">
                        Compras
                    </a>
                    <a href="#" class="nav-link" :class="{ active: currentView === 'vendas' }" @click.prevent="setView('vendas')">
                        Vendas
                    </a>
                    <a href="#" class="nav-link" :class="{ active: currentView === 'historico' }" @click.prevent="setView('historico')">
                        Histórico
                    </a>
                </div>
            </nav>
            <div class="container">
                <component :is="currentView"></component>
            </div>
        </div>
    `
};

const app = createApp(App);
app.mount('#app');

