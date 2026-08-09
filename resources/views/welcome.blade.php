<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart School Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.158.0/build/three.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'spin-slow': 'spin 8s linear infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 1.5s ease-out forwards',
                        'slide-up': 'slideUp 1s ease-out forwards',
                        'slide-down': 'slideDown 1s ease-out forwards',
                        'slide-in-right': 'slideInRight 1s ease-out forwards',
                        'slide-in-left': 'slideInLeft 1s ease-out forwards',
                        'scale-in': 'scaleIn 1s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(50px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-50px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(-50px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        slideInLeft: {
                            '0%': { transform: 'translateX(50px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    <style type="text/css">
        /* Custom styles for 3D effects */
        .card-3d {
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }

        .card-3d:hover {
            transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        .icon-3d {
            transform-style: preserve-3d;
            transform: perspective(1000px);
            transition: transform 0.6s ease;
        }

        .icon-3d:hover {
            transform: perspective(1000px) rotateY(15deg) rotateX(15deg);
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-dark {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .text-shadow {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.12), 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .text-gradient {
            background: linear-gradient(to right, #8b5cf6, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .card-content {
            transform: translateZ(20px);
        }

        .parallax-bg {
            transform-style: preserve-3d;
            transform: translateZ(-10px) scale(2);
        }

        /* 3D Book effect */
        .book-3d {
            position: relative;
            transform-style: preserve-3d;
            transform: rotateY(-30deg) rotateX(5deg);
            transition: transform 0.6s ease;
        }

        .book-3d:hover {
            transform: rotateY(-15deg) rotateX(5deg);
        }

        .book-cover {
            position: absolute;
            width: 100%;
            height: 100%;
            transform-origin: left;
            transform-style: preserve-3d;
        }

        .book-page {
            position: absolute;
            width: 95%;
            height: 95%;
            top: 2.5%;
            left: 2.5%;
            background: white;
            transform: translateZ(-1px);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .book-spine {
            position: absolute;
            width: 30px;
            height: 100%;
            left: -15px;
            background: linear-gradient(to right, #4c1d95, #6d28d9);
            transform: rotateY(90deg) translateZ(-15px);
        }

        /* Advanced 3D Icons */
        .icon-7d {
            position: relative;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .icon-7d-layer {
            position: absolute;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            transition: all 0.6s ease;
        }

        .layer-1 {
            transform: translateZ(0px);
        }

        .layer-2 {
            transform: translateZ(5px);
        }

        .layer-3 {
            transform: translateZ(10px);
        }

        .layer-4 {
            transform: translateZ(15px);
        }

        .layer-5 {
            transform: translateZ(20px);
        }

        .layer-6 {
            transform: translateZ(25px);
        }

        .layer-7 {
            transform: translateZ(30px);
        }

        .icon-7d:hover .layer-1 {
            transform: translateZ(5px) rotate(5deg);
        }

        .icon-7d:hover .layer-2 {
            transform: translateZ(10px) rotate(10deg);
        }

        .icon-7d:hover .layer-3 {
            transform: translateZ(15px) rotate(15deg);
        }

        .icon-7d:hover .layer-4 {
            transform: translateZ(20px) rotate(20deg);
        }

        .icon-7d:hover .layer-5 {
            transform: translateZ(25px) rotate(25deg);
        }

        .icon-7d:hover .layer-6 {
            transform: translateZ(30px) rotate(30deg);
        }

        .icon-7d:hover .layer-7 {
            transform: translateZ(35px) rotate(35deg);
        }

        /* Mouse follower */
        .mouse-follower {
            position: fixed;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(139, 92, 246, 0.3);
            pointer-events: none;
            mix-blend-mode: screen;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease;
        }

        /* Tilt effect */
        .tilt {
            transform-style: preserve-3d;
            transform: perspective(1000px);
        }

        .tilt-inner {
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out;
        }

        /* Glow effect */
        .glow {
            position: relative;
            overflow: hidden;
        }

        .glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle,
                    rgba(255, 255, 255, 0.3) 0%,
                    rgba(255, 255, 255, 0) 70%);
            opacity: 0;
            transition: opacity 0.3s;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .glow:hover::before {
            opacity: 1;
        }

        /* 3D Layered Icon */
        .layered-icon {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
        }

        .layered-icon-part {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            transition: all 0.4s ease;
        }

        /* Floating animation with different timing */
        .float-1 {
            animation: float 6s ease-in-out infinite;
        }

        .float-2 {
            animation: float 8s ease-in-out infinite 0.5s;
        }

        .float-3 {
            animation: float 7s ease-in-out infinite 1s;
        }

        .float-4 {
            animation: float 9s ease-in-out infinite 1.5s;
        }

        .float-5 {
            animation: float 10s ease-in-out infinite 2s;
        }

        /* Cursor effects */
        .cursor-glow {
            position: relative;
        }

        .cursor-glow::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, rgba(139, 92, 246, 0) 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            z-index: 10;
        }

        .cursor-glow:hover::after {
            opacity: 1;
        }

        /* 3D Depth Card */
        .depth-card {
            position: relative;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .depth-card-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
        }

        .depth-card-front {
            z-index: 2;
            transform: translateZ(20px);
        }

        .depth-card-middle {
            z-index: 1;
            transform: translateZ(10px);
        }

        .depth-card-back {
            z-index: 0;
            transform: translateZ(0);
        }

        /* Canvas container for 3D background */
        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body class="font-sans bg-gradient-to-br from-gray-50 to-white min-h-screen overflow-x-hidden" x-data="{ 
          scrolled: false,
          mouseX: 0,
          mouseY: 0,
          cursorX: 0,
          cursorY: 0
      }" x-init="
          window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 });
          window.addEventListener('mousemove', (e) => { 
              mouseX = e.clientX; 
              mouseY = e.clientY;
          });
          
          // Animate cursor follower
          function animateCursor() {
              cursorX += (mouseX - cursorX) * 0.1;
              cursorY += (mouseY - cursorY) * 0.1;
              if (document.querySelector('.mouse-follower')) {
                  document.querySelector('.mouse-follower').style.left = cursorX + 'px';
                  document.querySelector('.mouse-follower').style.top = cursorY + 'px';
              }
              requestAnimationFrame(animateCursor);
          }
          animateCursor();
      ">

    <!-- Mouse follower -->
    <div class="mouse-follower"></div>

    <!-- Canvas container for 3D background -->
    <div id="canvas-container"></div>

    <!-- 3D Floating Elements (Absolute positioned) -->
    <div class="fixed w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-16 h-16 opacity-30 float-1" style="animation-delay: 0s;">
            <div class="book-3d w-full h-full">
                <div class="book-cover bg-primary-600 rounded-md shadow-lg"></div>
                <div class="book-page rounded-r-sm"></div>
                <div class="book-spine"></div>
            </div>
        </div>
        <div class="absolute top-40 right-20 w-20 h-20 opacity-20 float-2" style="animation-delay: 1s;">
            <svg class="w-full h-full text-primary-400 icon-3d" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3ZM18.82 9L12 12.72L5.18 9L12 5.28L18.82 9ZM17 15.99L12 18.72L7 15.99V12.27L12 15L17 12.27V15.99Z">
                </path>
            </svg>
        </div>
        <div class="absolute bottom-40 left-1/4 w-12 h-12 opacity-30 float-3" style="animation-delay: 2s;">
            <svg class="w-full h-full text-blue-400 icon-3d" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 2L4 5V11.09C4 16.14 7.41 20.85 12 22C16.59 20.85 20 16.14 20 11.09V5L12 2ZM18 11.09C18 15.09 15.45 18.79 12 19.92C8.55 18.79 6 15.1 6 11.09V6.39L12 4.14L18 6.39V11.09Z">
                </path>
                <path d="M13 10H11V16H13V10Z"></path>
                <path d="M13 7H11V9H13V7Z"></path>
            </svg>
        </div>
        <div class="absolute bottom-20 right-1/3 w-14 h-14 opacity-20 float-4" style="animation-delay: 1.5s;">
            <svg class="w-full h-full text-green-400 icon-3d" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 3C7.03 3 3 7.03 3 12C3 16.97 7.03 21 12 21C16.97 21 21 16.97 21 12C21 7.03 16.97 3 12 3ZM12 19C8.13 19 5 15.87 5 12C5 8.13 8.13 5 12 5C15.87 5 19 8.13 19 12C19 15.87 15.87 19 12 19Z">
                </path>
                <path d="M12 17H14V12H16L13 7L10 12H12V17Z"></path>
            </svg>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center px-4 overflow-hidden"
        x-data="{ showElements: false }" x-init="setTimeout(() => showElements = true, 300)">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-20 -right-20 w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow">
            </div>
            <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse-slow"
                style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 left-1/4 w-72 h-72 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow"
                style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto max-w-6xl relative z-10">
            <!-- Hero Content -->
            <div class="text-center">
                <!-- Smart -->
                <h1 class="text-6xl md:text-8xl font-extrabold uppercase tracking-tight text-shadow"
                    x-show="showElements" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 -translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    Smart
                </h1>

                <!-- School -->
                <div class="relative my-2">
                    <h1 class="text-6xl md:text-8xl font-extrabold uppercase tracking-tight text-gradient"
                        x-show="showElements" x-transition:enter="transition ease-out duration-1000"
                        x-transition:enter-start="opacity-0 translate-x-10"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        School
                    </h1>

                    <!-- Star -->
                    <div class="absolute -right-4 -top-10 md:-right-10 md:-top-10" x-show="showElements"
                        x-transition:enter="transition ease-out duration-1000 delay-500"
                        x-transition:enter-start="opacity-0 scale-0" x-transition:enter-end="opacity-100 scale-100">
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-primary-200 animate-spin-slow" viewBox="0 0 40 40"
                            fill="currentColor">
                            <path
                                d="M25.66 17.636L40 20L25.66 22.364C23.968 22.644 22.64 23.968 22.364 25.66L20 40L17.636 25.66C17.356 23.968 16.032 22.64 14.34 22.364L0 20L14.34 17.636C16.032 17.356 17.36 16.032 17.636 14.34L20 0L22.364 14.34C22.644 16.032 23.968 17.36 25.66 17.636Z" />
                        </svg>
                    </div>

                    <!-- Glass shape -->
                    <div class="absolute -left-12 top-16 md:-left-16 md:top-20 w-6 h-6 md:w-10 md:h-10 rounded-bl-xl rounded-br-3xl rounded-tl-3xl rounded-tr-xl glass transform rotate-12"
                        x-show="showElements" x-transition:enter="transition ease-out duration-1000 delay-700"
                        x-transition:enter-start="opacity-0 -rotate-90 scale-0"
                        x-transition:enter-end="opacity-100 rotate-12 scale-100"></div>
                </div>

                <!-- Management -->
                <h1 class="text-6xl md:text-8xl font-extrabold uppercase tracking-tight text-shadow"
                    x-show="showElements" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 -translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    Management
                </h1>

                <!-- Shiny line -->
                <div class="absolute left-1/2 top-32 z-20 -translate-x-1/2 rotate-[50deg] w-[26rem] h-2.5 bg-gradient-to-r from-transparent to-white/50 ring-1 ring-white/50 transition duration-500 ease-out group-hover/header:translate-x-[-55%] group-hover/header:opacity-0"
                    x-show="showElements" x-transition:enter="transition ease-out duration-1200 delay-1000"
                    x-transition:enter-start="opacity-0 -translate-y-30"
                    x-transition:enter-end="opacity-100 translate-y-0"></div>

                <!-- Description -->
                <p class="mx-auto max-w-4xl pt-8 text-center text-lg md:text-xl text-gray-600 leading-relaxed"
                    x-show="showElements" x-transition:enter="transition ease-out duration-1000 delay-500"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    A comprehensive solution for
                    <span
                        class="inline-block font-medium text-primary-500 transition duration-200 hover:-translate-y-0.5 mx-1">
                        educational institutions
                    </span>
                    to manage their
                    <span class="text-gray-800 font-medium mx-1">
                        administrative tasks, student records, and academic activities
                    </span>
                    efficiently.
                    <br class="hidden md:block">
                    Streamline your school operations with our powerful, user-friendly platform.
                </p>

                <!-- Call to Action Button -->
                <div class="mt-10 flex justify-center">
                    <a href="#login-cards"
                        class="group relative isolate z-10 grid place-items-center leading-snug text-white cursor-glow"
                        x-show="showElements" x-transition:enter="transition ease-out duration-1000 delay-800"
                        x-transition:enter-start="opacity-0 scale-0" x-transition:enter-end="opacity-100 scale-100">
                        <!-- Label -->
                        <div
                            class="z-10 grid place-items-center gap-1.5 self-center justify-self-center [grid-area:1/-1]">
                            <div>Get</div>
                            <div>Started</div>

                            <!-- Arrow -->
                            <div class="arrow-animation">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 11" fill="none"
                                    class="mt-1 w-5 text-[#DBDAE8] transition duration-300 ease-out group-hover:text-primary-400">
                                    <path
                                        d="M1 4.8C0.613401 4.8 0.3 5.1134 0.3 5.5C0.3 5.8866 0.613401 6.2 1 6.2L1 4.8ZM14.495 5.99498C14.7683 5.72161 14.7683 5.27839 14.495 5.00503L10.0402 0.550253C9.76684 0.276886 9.32362 0.276886 9.05025 0.550253C8.77689 0.823621 8.77689 1.26684 9.05025 1.5402L13.0101 5.5L9.05025 9.4598C8.77689 9.73317 8.77689 10.1764 9.05025 10.4497C9.32362 10.7231 9.76683 10.7231 10.0402 10.4497L14.495 5.99498ZM1 6.2L14 6.2L14 4.8L1 4.8L1 6.2Z"
                                        fill="currentColor" />
                                </svg>
                            </div>
                        </div>

                        <!-- Shape -->
                        <div class="self-center justify-self-center [grid-area:1/-1] animate-spin-slow">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="size-32 text-black transition duration-500 ease-out will-change-transform group-hover:scale-110 group-hover:text-zinc-900"
                                viewBox="0 0 133 133" fill="currentColor">
                                <path
                                    d="M133 66.5028C133 58.2246 128.093 50.5844 119.798 44.4237C121.305 34.2085 119.374 25.3317 113.518 19.4759C107.663 13.6202 98.7915 11.689 88.5707 13.1967C82.4213 4.9071 74.7811 0 66.5028 0C58.2246 0 50.5844 4.9071 44.4237 13.2023C34.2085 11.6946 25.3317 13.6258 19.4759 19.4816C13.6202 25.3374 11.689 34.2086 13.1967 44.4293C4.9071 50.5787 0 58.2246 0 66.5028C0 74.7811 4.9071 82.4213 13.2023 88.582C11.6946 98.7971 13.6258 107.674 19.4816 113.53C25.3374 119.385 34.2086 121.317 44.4293 119.809C50.5844 128.099 58.2302 133.011 66.5085 133.011C74.7867 133.011 82.4269 128.104 88.5876 119.809C98.8027 121.317 107.68 119.385 113.535 113.53C119.391 107.674 121.322 98.8027 119.815 88.582C128.104 82.4269 133.017 74.7811 133.017 66.5028H133Z" />
                            </svg>
                        </div>

                        <!-- Blur -->
                        <div
                            class="hidden size-20 self-center justify-self-center bg-primary-400/70 blur-3xl [grid-area:1/-1] dark:block">
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- System Description -->
    <section class="relative py-20 px-4" id="system-description">
        <div class="container mx-auto max-w-5xl">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-8 text-gradient animate-fade-in">
                Our School Management System
            </h2>

            <p class="text-center text-lg text-gray-600 mb-16 max-w-3xl mx-auto animate-fade-in"
                style="animation-delay: 0.3s;">
                Our comprehensive school management system streamlines administrative tasks, enhances communication
                between
                teachers, students, and parents, and provides powerful tools for academic management. With role-based
                access,
                each user gets a tailored experience to meet their specific needs.
            </p>

            <!-- Advanced 7D School Icon -->
            <div class="w-40 h-40 mx-auto mb-16 icon-7d animate-fade-in cursor-glow" style="animation-delay: 0.5s;">
                <div class="relative w-full h-full transform-style preserve-3d perspective-1000">
                    <!-- Multi-layered 3D icon -->
                    <div class="icon-7d-layer layer-1">
                        <svg class="w-full h-full text-primary-300" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3Z"></path>
                        </svg>
                    </div>
                    <div class="icon-7d-layer layer-2">
                        <svg class="w-full h-full text-primary-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.82 9L12 12.72L5.18 9L12 5.28L18.82 9Z"></path>
                        </svg>
                    </div>
                    <div class="icon-7d-layer layer-3">
                        <svg class="w-full h-full text-primary-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 15.99L12 18.72L7 15.99V12.27L12 15L17 12.27V15.99Z"></path>
                        </svg>
                    </div>
                    <div class="icon-7d-layer layer-4">
                        <div
                            class="w-16 h-16 rounded-full bg-primary-100 opacity-70 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-pulse-slow">
                        </div>
                    </div>
                    <div class="icon-7d-layer layer-5">
                        <div
                            class="w-8 h-8 rounded-full bg-primary-200 opacity-50 absolute top-1/4 left-1/4 animate-float-1">
                        </div>
                    </div>
                    <div class="icon-7d-layer layer-6">
                        <div
                            class="w-6 h-6 rounded-full bg-primary-300 opacity-40 absolute bottom-1/4 right-1/4 animate-float-2">
                        </div>
                    </div>
                    <div class="icon-7d-layer layer-7">
                        <div
                            class="w-4 h-4 rounded-full bg-primary-400 opacity-30 absolute top-1/3 right-1/3 animate-float-3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Cards -->
            <div id="login-cards" class="grid grid-cols-1 md:grid-cols-4 gap-8 mt-10 mb-20">
                <!-- Student Card -->
                <div class="card-3d overflow-hidden rounded-2xl animate-fade-in tilt" style="animation-delay: 0.2s;">
                    <div class="glass h-full flex flex-col shadow-xl border border-white/20 rounded-2xl overflow-hidden tilt-inner">
                        <div class="h-2 bg-blue-500"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="card-content">
                                <!-- Advanced 3D Book Icon -->
                                <div class="w-24 h-24 mx-auto mb-4 icon-7d">
                                    <div class="layered-icon">
                                        <div class="layered-icon-part" style="transform: translateZ(0px);">
                                            <svg class="w-full h-full text-blue-300" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(5px);">
                                            <svg class="w-full h-full text-blue-400" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M17.5 14.33c-1.7 0-3.24.29-4.5.83v1.66c1.13-.64 2.7-.99 4.5-.99.88 0 1.73.09 2.5.26v-1.52c-.79-.15-1.64-.24-2.5-.24zm0-1.5c1.7 0 3.24.3 4.5.83v-1.57c-.79-.15-1.64-.26-2.5-.26-1.7 0-3.24.3-4.5.83v1.57c1.13-.64 2.7-.99 4.5-.99z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(10px);">
                                            <svg class="w-full h-full text-blue-500" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M17.5 10.5c.88 0 1.73.09 2.5.26V9.24c-.79-.15-1.64-.24-2.5-.24-1.7 0-3.24.29-4.5.83v1.66c1.13-.64 2.7-.99 4.5-.99z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-center mb-2">Student Portal</h3>
                                <p class="text-gray-600 text-center text-sm mb-6">Access your courses, grades, and
                                    assignments</p>

                                <ul class="space-y-3 text-sm text-gray-600 mb-6">
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>View course materials and assignments</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Check grades and attendance</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Communicate with teachers</span>
                                    </li>
                                </ul>
                            </div>

                            <a href="/student">
                                <div class="mt-auto">
                                    <button
                                        class="w-full py-3 px-4 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 glow">
                                        Student Login
                                    </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Student Card -->
                <div class="card-3d overflow-hidden rounded-2xl animate-fade-in tilt" style="animation-delay: 0.2s;">
                    <div class="glass h-full flex flex-col shadow-xl border border-white/20 rounded-2xl overflow-hidden tilt-inner">
                        <div class="h-2 bg-blue-500"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="card-content">
                                <!-- Advanced 3D Book Icon -->
                                <div class="w-24 h-24 mx-auto mb-4 icon-7d">
                                    <div class="layered-icon">
                                        <div class="layered-icon-part" style="transform: translateZ(0px);">
                                            <svg class="w-full h-full text-blue-300" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(5px);">
                                            <svg class="w-full h-full text-blue-400" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M17.5 14.33c-1.7 0-3.24.29-4.5.83v1.66c1.13-.64 2.7-.99 4.5-.99.88 0 1.73.09 2.5.26v-1.52c-.79-.15-1.64-.24-2.5-.24zm0-1.5c1.7 0 3.24.3 4.5.83v-1.57c-.79-.15-1.64-.26-2.5-.26-1.7 0-3.24.3-4.5.83v1.57c1.13-.64 2.7-.99 4.5-.99z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(10px);">
                                            <svg class="w-full h-full text-blue-500" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M17.5 10.5c.88 0 1.73.09 2.5.26V9.24c-.79-.15-1.64-.24-2.5-.24-1.7 0-3.24.29-4.5.83v1.66c1.13-.64 2.7-.99 4.5-.99z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-center mb-2">Parent Portal</h3>
                                <p class="text-gray-600 text-center text-sm mb-6">Access your children grades, attendances...</p>

                                <ul class="space-y-3 text-sm text-gray-600 mb-6">
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>View course materials and assignments</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Check grades and attendance</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Communicate with teachers</span>
                                    </li>
                                </ul>
                            </div>

                            <a href="/parent">
                                <div class="mt-auto">
                                    <button
                                        class="w-full py-3 px-4 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 glow">
                                        Parent Login
                                    </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Teacher Card -->
                <div class="card-3d overflow-hidden rounded-2xl animate-fade-in tilt" style="animation-delay: 0.4s;">
                    <div class="glass h-full flex flex-col shadow-xl border border-white/20 rounded-2xl overflow-hidden tilt-inner">
                        <div class="h-2 bg-primary-500"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="card-content">
                                <!-- Advanced 3D Teacher Icon -->
                                <div class="w-24 h-24 mx-auto mb-4 icon-7d">
                                    <div class="layered-icon">
                                        <div class="layered-icon-part" style="transform: translateZ(0px);">
                                            <svg class="w-full h-full text-primary-300" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M20 17V16C20 14.3431 18.6569 13 17 13H13.5V14H17C17.8284 14 18.5 14.6716 18.5 15.5V17H20Z" />
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(5px);">
                                            <svg class="w-full h-full text-primary-400" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M20 20V19C20 17.3431 18.6569 16 17 16H7C5.34315 16 4 17.3431 4 19V20H20Z" />
                                            </svg>
                                        </div>
                                        <div class="layered-icon-part" style="transform: translateZ(10px);">
                                            <svg class="w-full h-full text-primary-500" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <circle cx="15" cy="10" r="3" />
                                                <circle cx="9" cy="13" r="3" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-center mb-2">Teacher Portal</h3>
                                <p class="text-gray-600 text-center text-sm mb-6">Manage classes, grades, and student
                                    progress</p>

                                <ul class="space-y-3 text-sm text-gray-600 mb-6">
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Create and grade assignments</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Track student attendance</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Communicate with students and parents</span>
                                    </li>
                                </ul>
                            </div>

                            <a href="/teacher">
                                <div class="mt-auto">
                                    <button
                                        class="w-full py-3 px-4 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50 glow">
                                        Teacher Login
                                    </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Card -->
                <div class="card-3d overflow-hidden rounded-2xl animate-fade-in tilt" style="animation-delay: 0.6s;">
                    <div class="glass h-full flex flex-col shadow-xl border border-white/20 rounded-2xl overflow-hidden tilt-inner">
                        <div class="h-2 bg-green-500"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="card-content">
                                <!-- Advanced 3D Admin Icon -->
                                <div class="w-24 h-24 mx-auto mb-4 icon-7d">
                                    <div class="depth-card">
                                        <div class="depth-card-layer depth-card-back">
                                            <svg class="w-full h-full text-green-300" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1Z" />
                                            </svg>
                                        </div>
                                        <div class="depth-card-layer depth-card-middle">
                                            <svg class="w-full h-full text-green-400" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M12 5C13.1 5 14 5.9 14 7C14 8.1 13.1 9 12 9C10.9 9 10 8.1 10 7C10 5.9 10.9 5 12 5Z" />
                                            </svg>
                                        </div>
                                        <div class="depth-card-layer depth-card-front">
                                            <svg class="w-full h-full text-green-500" viewBox="0 0 24 24"
                                                fill="currentColor" opacity="0.8">
                                                <path
                                                    d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 19C8.13 19 5 15.87 5 12C5 8.13 8.13 5 12 5C15.87 5 19 8.13 19 12C19 15.87 15.87 19 12 19Z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-center mb-2">Admin Portal</h3>
                                <p class="text-gray-600 text-center text-sm mb-6">Manage school operations and more</p>

                                <ul class="space-y-3 text-sm text-gray-600 mb-6">
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Manage student and staff accounts</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Configure courses and schedules</span>
                                    </li>
                                    <li class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z" />
                                        </svg>
                                        <span>Generate reports and analytics</span>
                                    </li>
                                </ul>
                            </div>

                            <a href="/admin/login">
                                <div class="mt-auto">
                                    <button
                                        class="w-full py-3 px-4 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 glow">
                                        Admin Login
                                    </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section with 3D Icons -->
    <section class="relative py-20 px-4 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto max-w-5xl">
            <h2 class="text-center text-3xl md:text-4xl font-bold mb-16 text-gradient animate-fade-in">
                Powerful Features
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- Feature 1: Attendance Tracking -->
                <div class="flex flex-col items-center text-center animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="w-24 h-24 mb-6 icon-7d cursor-glow">
                        <div class="layered-icon">
                            <div class="layered-icon-part" style="transform: translateZ(0px);">
                                <svg class="w-full h-full text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" />
                                </svg>
                            </div>
                            <div class="layered-icon-part" style="transform: translateZ(10px);">
                                <svg class="w-full h-full text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 17H7V10H9V17ZM13 17H11V7H13V17ZM17 17H15V13H17V17Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Attendance Tracking</h3>
                    <p class="text-gray-600">Real-time attendance tracking with automated reports and notifications for
                        absences.</p>
                </div>

                <!-- Feature 2: Grade Management -->
                <div class="flex flex-col items-center text-center animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="w-24 h-24 mb-6 icon-7d cursor-glow">
                        <div class="layered-icon">
                            <div class="layered-icon-part" style="transform: translateZ(0px);">
                                <svg class="w-full h-full text-primary-300" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" />
                                </svg>
                            </div>
                            <div class="layered-icon-part" style="transform: translateZ(10px);">
                                <svg class="w-full h-full text-primary-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M9 17H7V14H9V17ZM9 13H7V10H9V13ZM13 17H11V13H13V17ZM13 11H11V7H13V11ZM17 17H15V15H17V17ZM17 13H15V9H17V13ZM17 7H15V5H17V7Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Grade Management</h3>
                    <p class="text-gray-600">Comprehensive grading system with customizable assessment criteria and
                        progress tracking.</p>
                </div>

                <!-- Feature 3: Communication -->
                <div class="flex flex-col items-center text-center animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="w-24 h-24 mb-6 icon-7d cursor-glow">
                        <div class="layered-icon">
                            <div class="layered-icon-part" style="transform: translateZ(0px);">
                                <svg class="w-full h-full text-green-300" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z" />
                                </svg>
                            </div>
                            <div class="layered-icon-part" style="transform: translateZ(10px);">
                                <svg class="w-full h-full text-green-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 15L13.57 11.57L17 10L13.57 8.43L12 5L10.43 8.43L7 10L10.43 11.57L12 15Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Communication</h3>
                    <p class="text-gray-600">Integrated messaging system connecting teachers, students, and parents for
                        effective collaboration.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="relative py-6 px-4 bg-gray-950 text-white">
        <a href="https://afghancosmos.com" target="_blank" rel="noopener noreferrer"
            class="mx-auto flex w-fit items-center justify-center gap-3 text-sm transition-opacity hover:opacity-90">
            <span>Made By</span>
            <img src="{{ asset('schools/cosmos.png') }}" alt="Afghan Cosmos IT & Solutions"
                class="h-9 w-9 rounded-full object-contain bg-white p-1">
            <span class="font-semibold text-primary-300">
                Afghan Cosmos IT &amp; Solutions
            </span>
        </a>
    </footer>

</body>

</html>
