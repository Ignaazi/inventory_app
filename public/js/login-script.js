function closeModal() {
    const modal = document.getElementById("errorModal");
    if (modal) {
        modal.classList.add("opacity-0");
        setTimeout(() => modal.remove(), 300); // Hapus dari DOM setelah efek transisi selesai
    }
}

function togglePassword() {
    const passInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eye-icon");
    if (passInput.type === "password") {
        passInput.type = "text";
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.96 9.96 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943-9.542 7a10.025 10.025 0 01-4.132 5.411M21 21l-3.59-3.59"/>';
    } else {
        passInput.type = "password";
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

const canvas = document.getElementById('particleCanvas');
const ctx = canvas.getContext('2d');

let particlesArray = [];
const numberOfParticles = 45; 

function setCanvasSize() {
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
}
setCanvasSize();
window.addEventListener('resize', setCanvasSize);

class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2.5 + 0.5; // Ukuran bintik acak (kecil halus)
        this.speedX = Math.random() * 0.4 - 0.2; // Kecepatan gerak horizontal
        this.speedY = Math.random() * 0.4 - 0.2; // Kecepatan gerak vertikal
        this.opacity = Math.random() * 0.5 + 0.2; // Transparansi bintik
    }
    // Update posisi bintik
    update() {
        this.x += this.speedX;
        this.y += this.speedY;

        // Memantul jika menabrak dinding batas canvas
        if (this.x > canvas.width || this.x < 0) this.speedX = -this.speedX;
        if (this.y > canvas.height || this.y < 0) this.speedY = -this.speedY;
    }
    // Gambar bintiknya ke layar
    draw() {
        ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

function init() {
    particlesArray = [];
    for (let i = 0; i < numberOfParticles; i++) {
        particlesArray.push(new Particle());
    }
}
init();

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
        particlesArray[i].draw();
    }
    requestAnimationFrame(animate);
}
animate();