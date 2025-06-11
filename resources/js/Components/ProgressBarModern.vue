<template>
    <div class="container">
        <div class="status-info">
            <h1 class="title">Your progress</h1>
            <div class="bar-wrap">
                <div class="bar"></div>
            </div>
            <span class="percentage"></span>
            <p class="line">Number of puzzles: <span class="number" id="num1"></span></p>
            <p class="line">Solved puzzles: <span class="number" id="num2"></span></p>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';

const total = 150;
const solved = ref(0);

const percentage = computed(() => {
    return Math.round((solved.value * 1000) / total) / 10;
});

const increment = () => {
    if (solved.value < total) solved.value++;
};

const decrement = () => {
    if (solved.value > 0) solved.value--;
};
</script>
<style scoped>

.container {
    display: flex;
    flex-direction: column;
    justify-content: space-around;

    width: min(97%, 900px);
    margin: 0 auto;
}

.status-info {
    display: flex;
    flex-direction: column;
}

.title {
    padding: 0;
    margin: 0;

    font-family: 'Montserrat', sans-serif;
    font-size: clamp(1.2rem, 4vw, 3.8rem);

    color: var(--text-color);
}

.bar-wrap {
    padding: 6px;
    margin-top: 50px;

    border-radius: 100px;

    background-color: rgb(201, 201, 201);
}

.bar {
    width: 10%;
    height: 15px;

    transition: width .15s ease-out;

    background-color: green;
    border-radius: 100px;
    box-shadow: inset -1px -1px 10px rgb(0 0 0 / .5);
}

.percentage {
    align-self: center;

    margin: 10px 0 25px;

    font-family: 'Montserrat', sans-serif;
    font-size: clamp(1.8rem, 3.5vw, 2.6rem);

    color: var(--text-color);
}

.line {
    margin: 0;


    color: var(--text-color);
}

.number {
    margin-left: 8px;

    font-family: 'Montserrat', sans-serif;
    font-size: clamp(1.8rem, 3vw, 2.2rem);
}

.btn-wrap {
    align-self: center;
}

.btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;

    width: min(15vw, 70px);
    height: min(15vw, 70px);
    border: 3px solid var(--text-color);
    margin: 1rem;

    font-family: 'Fira Sans', sans-serif;
    font-size: 4.8rem;

    transition: background .15s, color .15s;

    border-radius: 50%;
    background-color: var(--text-color);
    color: var(--body-color);
}

.btn:active {
    background-color: var(--body-color);
    color: var(--text-color);
}
</style>
