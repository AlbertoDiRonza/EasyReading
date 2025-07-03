const images = [
    '../assets/img/bg1.jpg',
    '../assets/img/bg2.jpg',
    '../assets/img/bg3.jpg'
];

const randomImage = images[Math.floor(Math.random() * images.length)];
document.body.style.backgroundImage = `url(${randomImage})`;