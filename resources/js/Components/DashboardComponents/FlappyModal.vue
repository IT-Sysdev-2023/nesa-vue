<template>
    <a-modal style="width: 50%;">
        <div class="game-wrap">
            <canvas
                ref="canvas"
                width="450"
                height="640"
                aria-label="Flappy game canvas"
                @click="flap"
                @touchstart.prevent="flap"
            ></canvas>
            <div class="hud">
                <div class="score-section">
                    <div class="score-display">
                        Score: <span class="score-value">{{ score }}</span>
                    </div>
                    <div class="best-score">
                        Best: <span class="score-value">{{ best }}</span>
                    </div>
                </div>
                <div class="controls">
                    <button class="btn btn-primary" @click="start">{{ state === 'running' ? 'Restart' : 'Start' }}</button>
                    <button class="btn btn-secondary" @click="togglePause" :disabled="state !== 'running'">
                        {{ paused ? "▶️ Resume" : "⏸️ Pause" }}
                    </button>
                    <button class="btn btn-action" @click="flap" :disabled="state !== 'running' || paused">🪶 Flap</button>
                    <button class="btn" @click="toggleAutoplay" :class="{ active: autoplay }">
                        {{ autoplay ? "🤖 Auto ✓" : "🤖 Auto" }}
                    </button>
                </div>
            </div>
            <div class="instructions">
                🎮 Click canvas or Space to flap • P to pause
            </div>
        </div>
    </a-modal>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";

const canvas = ref(null);
const score = ref(0);
const best = ref(0);
const paused = ref(false);
const autoplay = ref(false);
const state = ref('menu'); // 'menu', 'running', 'gameOver'

let ctx, W, H;
let bird, pipes, particles, frame, animId;
let speedMult = 1;

const cfg = {
    gravity: 0.35,
    flapPower: -7.5,
    pipeGap: 160,
    pipeWidth: 65,
    pipeSpacing: 190,
    pipeSpeed: 2.0,
    birdSize: 32,
    groundHeight: 100,
};

class Bird {
    constructor() {
        this.x = 100;
        this.y = H / 2;
        this.vy = 0;
        this.size = cfg.birdSize;
        this.rotation = 0;
        this.flapAnimation = 0;
        this.trail = [];
    }
    
    update() {
        this.vy += cfg.gravity * speedMult;
        this.y += this.vy * speedMult;
        this.rotation = Math.max(-0.5, Math.min(0.8, this.vy * 0.05));
        this.flapAnimation = Math.max(0, this.flapAnimation - 0.1);
        
        // Add trail effect
        if (frame % 3 === 0) {
            this.trail.push({x: this.x, y: this.y + this.size/2, life: 20});
        }
        this.trail = this.trail.filter(t => t.life-- > 0);
    }
    
    flap() {
        this.vy = cfg.flapPower;
        this.flapAnimation = 1;
        createParticles(this.x, this.y + this.size/2, '#FFD700', 5);
    }
    
    draw() {
        // Draw trail
        this.trail.forEach((t, i) => {
            const alpha = t.life / 20;
            ctx.save();
            ctx.globalAlpha = alpha * 0.3;
            ctx.fillStyle = '#FFD700';
            ctx.beginPath();
            ctx.arc(t.x, t.y, 3 * alpha, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        });
        
        ctx.save();
        ctx.translate(this.x + this.size/2, this.y + this.size/2);
        ctx.rotate(this.rotation);
        
        // Bird body with gradient
        const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, this.size/2);
        gradient.addColorStop(0, '#FFD700');
        gradient.addColorStop(0.7, '#FFA500');
        gradient.addColorStop(1, '#FF8C00');
        
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.arc(0, 0, this.size/2, 0, Math.PI * 2);
        ctx.fill();
        
        // Wing animation
        const wingOffset = Math.sin(this.flapAnimation * Math.PI) * 5;
        ctx.fillStyle = '#FF6B35';
        ctx.beginPath();
        ctx.ellipse(-5, wingOffset, 12, 8, -0.3, 0, Math.PI * 2);
        ctx.fill();
        
        // Eye
        ctx.fillStyle = 'white';
        ctx.beginPath();
        ctx.arc(6, -4, 6, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.fillStyle = 'black';
        ctx.beginPath();
        ctx.arc(8, -3, 3, 0, Math.PI * 2);
        ctx.fill();
        
        // Beak
        ctx.fillStyle = '#FF8C00';
        ctx.beginPath();
        ctx.moveTo(this.size/2 - 2, 0);
        ctx.lineTo(this.size/2 + 8, -2);
        ctx.lineTo(this.size/2 + 8, 4);
        ctx.closePath();
        ctx.fill();
        
        ctx.restore();
    }
    
    getBounds() {
        return { x: this.x, y: this.y, w: this.size, h: this.size };
    }
}

class Pipe {
    constructor(x) {
        this.x = x;
        this.top = Math.random() * (H - cfg.groundHeight - cfg.pipeGap - 120) + 60;
        this.passed = false;
    }
    
    update() {
        this.x -= cfg.pipeSpeed * speedMult;
    }
    
    draw() {
        const pipeGrad = ctx.createLinearGradient(this.x, 0, this.x + cfg.pipeWidth, 0);
        pipeGrad.addColorStop(0, '#228B22');
        pipeGrad.addColorStop(0.5, '#32CD32');
        pipeGrad.addColorStop(1, '#228B22');
        
        ctx.fillStyle = pipeGrad;
        
        // Top pipe
        ctx.fillRect(this.x, 0, cfg.pipeWidth, this.top);
        ctx.fillRect(this.x - 5, this.top - 30, cfg.pipeWidth + 10, 30);
        
        // Bottom pipe
        const bottomY = this.top + cfg.pipeGap;
        ctx.fillRect(this.x, bottomY, cfg.pipeWidth, H - cfg.groundHeight - bottomY);
        ctx.fillRect(this.x - 5, bottomY, cfg.pipeWidth + 10, 30);
        
        // Pipe highlights
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.fillRect(this.x + 5, 0, 10, this.top);
        ctx.fillRect(this.x + 5, bottomY, 10, H - cfg.groundHeight - bottomY);
    }
}

function rand(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function createParticles(x, y, color, count) {
    for (let i = 0; i < count; i++) {
        particles.push({
            x: x + (Math.random() - 0.5) * 20,
            y: y + (Math.random() - 0.5) * 20,
            vx: (Math.random() - 0.5) * 4,
            vy: (Math.random() - 0.5) * 4,
            life: 30,
            color: color,
            size: Math.random() * 3 + 1
        });
    }
}

function resetGame() {
    bird = new Bird();
    pipes = [];
    particles = [];
    frame = 0;
    score.value = 0;
    state.value = 'menu';
}

function start() {
    if (state.value === 'running') {
        resetGame();
    }
    resetIfDead();
    state.value = 'running';
    paused.value = false;
    loop();
}

function pause() {
    if (state.value !== 'running') return;
    paused.value = true;
    cancelAnimationFrame(animId);
}

function resume() {
    if (paused.value && state.value === 'running') {
        paused.value = false;
        loop();
    }
}

function die() {
    state.value = 'gameOver';
    createParticles(bird.x, bird.y, '#FF0000', 15);
    best.value = Math.max(best.value, score.value);
}

function flap() {
    if (state.value === 'menu' || state.value === 'gameOver') {
        start();
        return;
    }
    if (state.value === 'running' && !paused.value) {
        bird.flap();
    }
}

function addPipe() {
    pipes.push(new Pipe(W + 50));
}

function update() {
    if (state.value !== 'running' || paused.value) return;
    
    frame++;
    bird.update();
    
    // Update particles
    particles = particles.filter(p => {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.1;
        p.life--;
        return p.life > 0;
    });

    if (frame % Math.floor(cfg.pipeSpacing / (cfg.pipeSpeed * speedMult)) === 0) {
        addPipe();
    }

    pipes.forEach(p => p.update());
    pipes = pipes.filter((p) => p.x + cfg.pipeWidth > -50);

    // Check scoring and collisions
    for (let p of pipes) {
        if (!p.passed && p.x + cfg.pipeWidth < bird.x) {
            p.passed = true;
            score.value++;
            createParticles(bird.x, bird.y, '#00FF00', 8);
        }
        
        const b = bird.getBounds();
        const gapY = p.top + cfg.pipeGap;
        if (b.x + b.w > p.x && b.x < p.x + cfg.pipeWidth) {
            if (b.y < p.top || b.y + b.h > gapY) {
                die();
                return;
            }
        }
    }

    // Ground collision
    if (bird.y + bird.size > H - cfg.groundHeight) {
        bird.y = H - cfg.groundHeight - bird.size;
        die();
        return;
    }
    
    // Ceiling collision
    if (bird.y < -10) {
        bird.y = -10;
        bird.vy = 0;
    }

    if (autoplay.value && state.value === 'running') autopilot();
}

function autopilot() {
    const next = pipes.find((p) => p.x + cfg.pipeWidth > bird.x);
    if (!next) return;
    const gapCenter = next.top + cfg.pipeGap / 2;
    const birdCenter = bird.y + bird.size / 2;
    if (birdCenter > gapCenter - 10 && bird.vy > -2) {
        bird.flap();
    }
}

function render() {
    ctx.clearRect(0, 0, W, H);
    
    // Background
    const skyGrad = ctx.createLinearGradient(0, 0, 0, H - cfg.groundHeight);
    skyGrad.addColorStop(0, '#87CEEB');
    skyGrad.addColorStop(1, '#4682B4');
    ctx.fillStyle = skyGrad;
    ctx.fillRect(0, 0, W, H - cfg.groundHeight);
    
    // Animated clouds
    ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
    for (let i = 0; i < 3; i++) {
        const x = (frame * 0.2 + i * 150) % (W + 100) - 50;
        const y = 50 + i * 30;
        drawCloud(x, y, 40 + i * 10);
    }
    
    // Ground
    const groundGrad = ctx.createLinearGradient(0, H - cfg.groundHeight, 0, H);
    groundGrad.addColorStop(0, '#DEB887');
    groundGrad.addColorStop(1, '#D2691E');
    ctx.fillStyle = groundGrad;
    ctx.fillRect(0, H - cfg.groundHeight, W, cfg.groundHeight);
    
    // Draw game objects
    pipes.forEach(p => p.draw());
    
    // Draw particles
    particles.forEach(p => {
        const alpha = p.life / 30;
        ctx.save();
        ctx.globalAlpha = alpha;
        ctx.fillStyle = p.color;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
    });
    
    bird.draw();

    // In-game score
    ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.font = 'bold 36px Arial';
    ctx.textAlign = 'center';
    ctx.strokeStyle = 'rgba(0, 0, 0, 0.3)';
    ctx.lineWidth = 3;
    ctx.strokeText(score.value.toString(), W/2, 60);
    ctx.fillText(score.value.toString(), W/2, 60);

    // Game state messages
    if (state.value === 'menu') {
        drawMessage('🐦 Flappy Bird', 'Click or Press Space to Start!', '#4CAF50');
    } else if (state.value === 'gameOver') {
        drawMessage('💥 Game Over!', 'Click Start to Try Again', '#F44336');
    }
}

function drawCloud(x, y, size) {
    ctx.beginPath();
    ctx.arc(x, y, size * 0.5, 0, Math.PI * 2);
    ctx.arc(x + size * 0.6, y, size * 0.7, 0, Math.PI * 2);
    ctx.arc(x + size * 1.2, y, size * 0.5, 0, Math.PI * 2);
    ctx.fill();
}

function drawMessage(title, subtitle, color) {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
    ctx.fillRect(0, H/2 - 80, W, 160);
    
    ctx.fillStyle = color;
    ctx.font = 'bold 32px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(title, W/2, H/2 - 20);
    
    ctx.fillStyle = 'white';
    ctx.font = '18px Arial';
    ctx.fillText(subtitle, W/2, H/2 + 20);
}

function loop() {
    update();
    render();
    if (state.value === 'running' && !paused.value) {
        animId = requestAnimationFrame(loop);
    }
}

function resetIfDead() {
    if (state.value === 'gameOver' || state.value === 'menu') {
        resetGame();
    }
}

function togglePause() {
    if (paused.value) {
        resume();
    } else {
        pause();
    }
}

function toggleAutoplay() {
    autoplay.value = !autoplay.value;
}

onMounted(async () => {
    await nextTick();
    ctx = canvas.value.getContext("2d");
    W = canvas.value.width;
    H = canvas.value.height;
    resetGame();
    render();
    
    window.addEventListener("keydown", (e) => {
        if (e.code === "Space") {
            flap();
            e.preventDefault();
        }
        if (e.code === "KeyP") {
            togglePause();
        }
    });
});

onUnmounted(() => {
    cancelAnimationFrame(animId);
});
</script>

<style scoped>
.game-wrap {
    width: 100%;
    max-width: 500px;
    background: linear-gradient(135deg, #87CEEB 0%, #98D8E8 50%, #B0E0E6 100%);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
    position: relative;
    overflow: hidden;
}

.game-wrap::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="1.5" fill="white" opacity="0.1"/><circle cx="40" cy="70" r="1" fill="white" opacity="0.1"/></svg>') repeat;
    pointer-events: none;
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    100% { transform: translateY(-100px); }
}

canvas {
    display: block;
    width: 100%;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) inset;
    cursor: pointer;
    touch-action: manipulation;
}

.hud {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 15px;
    position: relative;
    z-index: 1;
}

.score-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.score-display, .best-score {
    background: rgba(255, 255, 255, 0.95);
    padding: 8px 15px;
    border-radius: 25px;
    font-weight: bold;
    color: #333;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex: 1;
    text-align: center;
    font-size: 14px;
}

.score-value {
    color: #2196F3;
    font-size: 16px;
}

.controls {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn {
    background: linear-gradient(145deg, #ffffff, #e6e6e6);
    border: none;
    padding: 10px 14px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    color: #333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    font-size: 12px;
    min-width: 70px;
    flex: 1;
    max-width: 100px;
}

.btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

.btn:active:not(:disabled) {
    transform: translateY(0);
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(145deg, #4CAF50, #45a049);
    color: white;
}

.btn-secondary {
    background: linear-gradient(145deg, #2196F3, #1976D2);
    color: white;
}

.btn-action {
    background: linear-gradient(145deg, #FF9800, #F57C00);
    color: white;
}

.btn.active {
    background: linear-gradient(145deg, #9C27B0, #7B1FA2);
    color: white;
}

.instructions {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    border-radius: 15px;
    font-size: 11px;
    z-index: 2;
    max-width: 200px;
    text-align: center;
}

@media (max-width: 600px) {
    .game-wrap {
        padding: 15px;
        border-radius: 15px;
    }
    
    .controls {
        justify-content: center;
    }
    
    .btn {
        padding: 8px 10px;
        font-size: 11px;
        min-width: 60px;
    }
    
    .score-section {
        flex-direction: column;
        gap: 8px;
    }
    
    .instructions {
        position: static;
        margin-top: 10px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
}
</style>