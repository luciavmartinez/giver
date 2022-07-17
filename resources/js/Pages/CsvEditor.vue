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

                            </form>
                        </div>

                        <div class="text-sky-900 font-bold mt-4" v-if="$page.props.flash && $page.props.flash.message">{{ $page.props.flash.message }}</div>
                    </div>

                    <div v-if="props.users && props.users.total > 0">
                        <Pagination :links="props.users.links" class="mt-6" />
                        <table class="border-collapse border border-slate-400 m-auto mt-12 w-full">
                            <thead>
                                <th class="border border-slate-300 p-1">Title</th>
                                <th class="border border-slate-300 p-1">Name</th>
                                <th class="border border-slate-300 p-1">Company</th>
                                <th class="border border-slate-300 p-1">Email</th>
                                <th class="border border-slate-300 p-1">City</th>
                            </thead>
                            <tbody>
                                <tr v-for="(user, index) in props.users.data" :key="index">
                                    <td class="border border-slate-300 px-2">{{ user.title }}</td>
                                    <td class="border border-slate-300 px-2">{{ user.first_name + ' ' + user.last_name }}</td>
                                    <td class="border border-slate-300 px-2">{{ user.company }}</td>
                                    <td class="border border-slate-300 px-2">{{ user.email }}</td>
                                    <td class="border border-slate-300 px-2">{{ user.city }}</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <div id="container-graficos" class="mt-12">

                        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                            <Bar
                                v-if="isRendered"
                                :chart-options="chartOptions"
                                :chart-data="chartSobrenomeData"
                                :width="300"
                                :height="300"
                            />
                        </div>

                        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                            <Doughnut
                                v-if="isRendered"
                                :chart-options="donutsOptions"
                                :chart-data="donutsMailData"
                                :width="300"
                                :height="300"
                            />
                        </div>

                        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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

        </div>
    </AppLayout>
</template>

<style>

#container-graficos{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
}

#container-graficos canvas{
    flex: fit-content;
}

</style>

<script setup>

import Pagination from '@/Shared/Pagination.vue';
import { Inertia } from '@inertiajs/inertia'
import {reactive, ref, onMounted} from 'vue';
import { useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue';
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const form = useForm({
    file: null,
})

const donutsMailData = ref({
    labels: ['Com email', 'Sem email'],
    datasets: [
        {
            backgroundColor: ['#41B883', '#E46651'],
            data: [40, 20]
        }
    ]
})

const donutsOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: 50,
    rotation: 120
}

function submit() {
    form.post('/csv', {
        onSuccess: () => {
            form.reset('name');

            Inertia.reload({ only: ['emailsInvalidos', 'emailsValidos', 'generos', 'sobrenomes', 'users'] })
        },
    })
}

Inertia.on('success', (event) => {
    renderGraphics()
})

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

function render(){
    isRendered.value = false;
    isRendered.value = true;
}

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
    minBarLength: 50,
    responsive: true,
    scales: {
        y: {
            beginAtZero: true
        }
    }
}

function renderGraphics(){
    // Fill email data
    if(props && props.emailsValidos && props.sobrenomes) {
        donutsMailData.value.datasets[0].data = [props.emailsValidos, props.emailsInvalidos]
        chartSobrenomeData.value.datasets[0].data = [props.sobrenomes[0].total, props.sobrenomes[1].total]
        chartGeneroData.value.datasets[0].data = [props.generos[0].total, props.generos[1].total]
        isRendered.value = true;
    }
}

let props = defineProps({
    users: Object,
    sobrenomes: Object,
    generos: Object,
    emailsValidos: Number,
    emailsInvalidos: Number
});

onMounted(() => {
    renderGraphics()
})

</script>
