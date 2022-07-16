<template>
    <AppLayout title="CsvEditor" :class="[form.processing ? 'cursor-progress' : '']">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Processar CSV
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <h4>Selecione um arquivo</h4>

                    <div class="mt-4">
                        <div>
                            <form @submit.prevent="submit">
                                <input type="file" @input="form.file = $event.target.files[0]" />

                                <button
                                    :disabled="form.processing"
                                    type="submit"
                                    class="mt-4 inline-block rounded-sm font-medium border border-solid cursor-pointer text-center text-xs py-1 px-2 text-white bg-green-400 hover:bg-green-600 hover:border-green-600"
                                    :class="[form.processing ? 'bg-gray-300 border-gray-300 hover:bg-gray-600 hover:border-gray-600 cursor-progress' : '']"
                                >
                                    Processar
                                </button>

                                <button @click.prevent="render">
                                    RENDER
                                </button>
                            </form>
                        </div>

                        <div class="text-sky-900 font-bold mt-4" v-if="$page.props.flash && $page.props.flash.message">{{ $page.props.flash.message }}</div>
                    </div>

                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div style="width: 300px; margin: 0 auto">
                        <Bar
                            v-if="isRendered"
                            :chart-options="chartOptions"
                            :chart-data="chartSobrenomeData"
                            :width="300"
                            :height="300"
                        />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div style="width: 300px; margin: 0 auto">
                        <Bar
                            v-if="isRendered"
                            :chart-options="chartOptions"
                            :chart-data="chartEmailData"
                            :width="300"
                            :height="300"
                        />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div style="width: 300px; margin: 0 auto">

                        <Bar
                            v-if="isRendered"
                            :chart-options="chartOptions"
                            :chart-data="chartGeneroData"
                            :width="300"
                            :height="300"
                        />

                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>

import {reactive, ref, onMounted} from 'vue';
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue';
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const form = useForm({
    file: null,
})

function submit() {
    form.post('/csv', {
        onSuccess: () => {
            form.reset('name');

        },
    })
}

let isRendered = ref(false);

const graphicBarColors = {
    backgroundColor: [
        'rgba(70,161,161,0.2)',
        'rgba(135,45,166,0.2)',
    ],
    borderColor: [
        'rgba(70,161,161,1)',
        'rgba(135,45,166,1)',
    ]
}

function updateSobrenome(values){
    chartSobrenomeData.value.datasets[0].data = values;
}

function render(){
    isRendered.value = false;
    isRendered.value = true;
}

const chartEmailData = ref({
    labels: ['Com email', 'Sem email'],
    datasets: [{
        label: 'Emails',
        data: [0,0],
        backgroundColor: graphicBarColors.backgroundColor,
        borderColor: graphicBarColors.borderColor,
        borderWidth: 1
    }]
})

const chartGeneroData = ref({
    labels: ['Preenchidos', 'Vazios'],
    datasets: [{
        label: 'Genero',
        data: [0,0],
        backgroundColor: graphicBarColors.backgroundColor,
        borderColor: graphicBarColors.borderColor,
        borderWidth: 1
    }]
})

const chartSobrenomeData = ref({
    labels: ['Com sobrenome', 'Sem sobrenome'],
    datasets: [{
        label: 'Sobrenome',
        data: [0, 0],
        backgroundColor: graphicBarColors.backgroundColor,
        borderColor: graphicBarColors.borderColor,
        borderWidth: 1
    }]
})

const chartOptions = {
    responsive: true,
    scales: {
        y: {
            beginAtZero: true
        }
    }
}

function renderGraphics(){

    // Fill email data
    chartEmailData.value.datasets[0].data = [props.emailsValidos, props.emailsInvalidos]
}

let props = defineProps({
    sobrenomes: Object,
    generos: Object,
    emailsValidos: Number,
    emailsInvalidos: Number
});

onMounted(() => {
    renderGraphics()
})

</script>
